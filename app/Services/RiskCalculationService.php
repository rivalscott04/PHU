<?php

namespace App\Services;

use App\Enums\RiskLevel;
use App\Models\BAP;
use App\Models\InspectionFinding;
use App\Models\Jamaah;
use App\Models\Pengaduan;
use App\Models\RiskScore;
use App\Models\Sertifikat;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\RiskRepository;
use Illuminate\Support\Collection;
use App\Support\DashboardCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RiskCalculationService
{
    public function __construct(
        private readonly RiskRepository $riskRepository,
        private readonly AuditLogService $auditLogService,
        private readonly WorkQueueService $workQueueService,
    ) {
    }

    public function recalculateForTravel(int $travelId, bool $logAudit = true): RiskScore
    {
        return DB::transaction(function () use ($travelId, $logAudit) {
            $context = $this->loadTravelContext($travelId);
            $scores = $this->calculateAllScores($context);
            $total = min(100, array_sum($scores));
            $riskLevel = $this->resolveRiskLevel($total);

            $riskScore = $this->riskRepository->upsertForTravel($travelId, [
                ...$scores,
                'total_score' => $total,
                'risk_level' => $riskLevel,
                'last_calculated_at' => now(),
            ]);

            if ($logAudit) {
                $travelName = $riskScore->travel?->Penyelenggara ?? "travel #{$travelId}";
                $this->auditLogService->log(
                    'risk',
                    'recalculate',
                    "menghitung ulang skor risiko untuk {$travelName}: {$total} poin ({$riskLevel})"
                );
            }

            $riskScore = $riskScore->fresh(['travel']);
            $this->workQueueService->handleRiskScoreUpdated($riskScore);

            return $riskScore;
        });
    }

    public function recalculateAll(bool $logAudit = true): int
    {
        $count = 0;

        TravelCompany::query()->select('id')->chunkById(50, function ($travels) use (&$count) {
            $count += $this->recalculateChunk($travels->pluck('id')->all());
        });

        if ($logAudit) {
            $this->auditLogService->log(
                'risk',
                'recalculate',
                "menghitung ulang skor risiko untuk {$count} travel"
            );
        }

        $this->clearDashboardCache();

        return $count;
    }

    /**
     * @param  list<int>  $travelIds
     */
    private function recalculateChunk(array $travelIds): int
    {
        if ($travelIds === []) {
            return 0;
        }

        $batch = $this->preloadBatchContext($travelIds);
        $now = now();
        $rows = [];
        $upsertedTravelIds = [];

        foreach ($travelIds as $travelId) {
            $context = $this->contextFromBatch($travelId, $batch);

            if ($context['travel'] === null) {
                continue;
            }

            $scores = $this->calculateAllScores($context);
            $total = min(100, array_sum($scores));

            $rows[] = [
                'travel_id' => $travelId,
                'complaint_score' => $scores['complaint_score'],
                'inspection_score' => $scores['inspection_score'],
                'followup_score' => $scores['followup_score'],
                'certificate_score' => $scores['certificate_score'],
                'bap_score' => $scores['bap_score'],
                'activity_score' => $scores['activity_score'],
                'total_score' => $total,
                'risk_level' => $this->resolveRiskLevel($total),
                'last_calculated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $upsertedTravelIds[] = $travelId;
        }

        if ($rows === []) {
            return 0;
        }

        RiskScore::query()->upsert(
            $rows,
            ['travel_id'],
            [
                'complaint_score',
                'inspection_score',
                'followup_score',
                'certificate_score',
                'bap_score',
                'activity_score',
                'total_score',
                'risk_level',
                'last_calculated_at',
                'updated_at',
            ]
        );

        RiskScore::query()
            ->with('travel')
            ->whereIn('travel_id', $upsertedTravelIds)
            ->get()
            ->each(fn (RiskScore $riskScore) => $this->workQueueService->handleRiskScoreUpdated($riskScore));

        return count($upsertedTravelIds);
    }

    /** @return array<string, mixed> */
    public function getBreakdown(int $travelId): array
    {
        $context = $this->loadTravelContext($travelId);
        $scores = $this->calculateAllScores($context);
        $total = min(100, array_sum($scores));
        $level = $this->resolveRiskLevel($total);

        return [
            'scores' => $scores,
            'total_score' => $total,
            'risk_level' => $level,
            'recommendation' => $this->getRecommendation($level),
            'indicators' => $this->buildIndicatorNotes($scores),
        ];
    }

    public function getRecommendation(string $riskLevel): string
    {
        return match ($riskLevel) {
            RiskLevel::Low->value, 'LOW' => 'Monitoring normal, tidak perlu tindakan khusus.',
            RiskLevel::Medium->value, 'MEDIUM' => 'Masukkan ke monitoring intensif.',
            RiskLevel::High->value, 'HIGH' => 'Jadwalkan pengawasan dalam waktu dekat.',
            RiskLevel::Critical->value, 'CRITICAL' => 'Prioritas pengawasan, tindakan segera diperlukan.',
            default => 'Monitoring normal.',
        };
    }

    /**
     * @return array{
     *     travel: ?TravelCompany,
     *     user_ids: Collection<int, int>,
     *     findings: Collection<int, InspectionFinding>,
     *     complaint_count?: int,
     *     oldest_pending_bap?: mixed,
     *     has_expired_cert?: bool,
     *     has_recent_bap?: bool,
     *     has_recent_jamaah?: bool
     * }
     */
    private function loadTravelContext(int $travelId): array
    {
        $batch = $this->preloadBatchContext([$travelId]);

        return $this->contextFromBatch($travelId, $batch);
    }

    /**
     * @param  list<int>  $travelIds
     * @return array{
     *     travels: Collection<int, TravelCompany>,
     *     user_ids_by_travel: Collection<int, Collection<int, int>>,
     *     findings_by_travel: Collection<int, Collection<int, InspectionFinding>>,
     *     complaint_counts: Collection<int, int>,
     *     oldest_pending_bap_by_travel: array<int, mixed>,
     *     expired_cert_travel_ids: array<int, true>,
     *     recent_bap_travel_ids: array<int, true>,
     *     recent_jamaah_travel_ids: array<int, true>
     * }
     */
    private function preloadBatchContext(array $travelIds): array
    {
        $travels = TravelCompany::query()
            ->whereIn('id', $travelIds)
            ->get(['id', 'Penyelenggara', 'kab_kota', 'license_expiry'])
            ->keyBy('id');

        $userIdsByTravel = User::query()
            ->whereIn('travel_id', $travelIds)
            ->get(['id', 'travel_id'])
            ->groupBy('travel_id')
            ->map(fn (Collection $users) => $users->pluck('id'));

        $userToTravel = [];
        foreach ($userIdsByTravel as $travelId => $userIds) {
            foreach ($userIds as $userId) {
                $userToTravel[$userId] = (int) $travelId;
            }
        }

        $findingsByTravel = collect();
        if (Schema::hasTable('pengawasan_temuan') && Schema::hasTable('pengawasan')) {
            $findingsByTravel = InspectionFinding::query()
                ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
                ->whereIn('pengawasan.travel_id', $travelIds)
                ->whereNotIn('pengawasan_temuan.status', ['CLOSED', 'VERIFIED'])
                ->select('pengawasan_temuan.*', 'pengawasan.travel_id')
                ->get()
                ->groupBy('travel_id');
        }

        $complaintCounts = collect();
        if (Schema::hasTable('pengaduan')) {
            $complaintCounts = Pengaduan::query()
                ->whereIn('travels_id', $travelIds)
                ->selectRaw('travels_id, COUNT(*) as total')
                ->groupBy('travels_id')
                ->pluck('total', 'travels_id')
                ->map(fn ($count) => (int) $count);
        }

        $oldestPendingBapByTravel = [];
        if (Schema::hasTable('bap') && $userToTravel !== []) {
            $oldestByUser = BAP::query()
                ->whereIn('user_id', array_keys($userToTravel))
                ->where('status', 'pending')
                ->selectRaw('user_id, MIN(created_at) as oldest')
                ->groupBy('user_id')
                ->pluck('oldest', 'user_id');

            foreach ($oldestByUser as $userId => $oldest) {
                $travelId = $userToTravel[$userId];
                if (
                    ! isset($oldestPendingBapByTravel[$travelId])
                    || $oldest < $oldestPendingBapByTravel[$travelId]
                ) {
                    $oldestPendingBapByTravel[$travelId] = $oldest;
                }
            }
        }

        $expiredCertTravelIds = [];
        if (Schema::hasTable('sertifikat')) {
            $expiredCertTravelIds = array_fill_keys(
                Sertifikat::query()
                    ->whereIn('travel_id', $travelIds)
                    ->whereIn('status', ['expired', 'revoked'])
                    ->distinct()
                    ->pluck('travel_id')
                    ->all(),
                true,
            );
        }

        $recentBapTravelIds = [];
        if (Schema::hasTable('bap') && $userToTravel !== []) {
            $recentUserIds = BAP::query()
                ->whereIn('user_id', array_keys($userToTravel))
                ->where('created_at', '>=', now()->subMonths(6))
                ->distinct()
                ->pluck('user_id');

            foreach ($recentUserIds as $userId) {
                $travelId = $userToTravel[$userId] ?? null;
                if ($travelId !== null) {
                    $recentBapTravelIds[$travelId] = true;
                }
            }
        }

        $recentJamaahTravelIds = [];
        if (Schema::hasTable('jamaah')) {
            $recentJamaahTravelIds = array_fill_keys(
                Jamaah::query()
                    ->whereIn('travel_id', $travelIds)
                    ->where('created_at', '>=', now()->subYear())
                    ->distinct()
                    ->pluck('travel_id')
                    ->all(),
                true,
            );
        }

        return [
            'travels' => $travels,
            'user_ids_by_travel' => $userIdsByTravel,
            'findings_by_travel' => $findingsByTravel,
            'complaint_counts' => $complaintCounts,
            'oldest_pending_bap_by_travel' => $oldestPendingBapByTravel,
            'expired_cert_travel_ids' => $expiredCertTravelIds,
            'recent_bap_travel_ids' => $recentBapTravelIds,
            'recent_jamaah_travel_ids' => $recentJamaahTravelIds,
        ];
    }

    /**
     * @param  array{
     *     travels: Collection<int, TravelCompany>,
     *     user_ids_by_travel: Collection<int, Collection<int, int>>,
     *     findings_by_travel: Collection<int, Collection<int, InspectionFinding>>,
     *     complaint_counts: Collection<int, int>,
     *     oldest_pending_bap_by_travel: array<int, mixed>,
     *     expired_cert_travel_ids: array<int, true>,
     *     recent_bap_travel_ids: array<int, true>,
     *     recent_jamaah_travel_ids: array<int, true>
     * }  $batch
     * @return array{
     *     travel: ?TravelCompany,
     *     user_ids: Collection<int, int>,
     *     findings: Collection<int, InspectionFinding>,
     *     complaint_count: int,
     *     oldest_pending_bap: mixed,
     *     has_expired_cert: bool,
     *     has_recent_bap: bool,
     *     has_recent_jamaah: bool
     * }
     */
    private function contextFromBatch(int $travelId, array $batch): array
    {
        return [
            'travel' => $batch['travels'][$travelId] ?? null,
            'user_ids' => $batch['user_ids_by_travel']->get($travelId, collect()),
            'findings' => $batch['findings_by_travel']->get($travelId, collect()),
            'complaint_count' => (int) ($batch['complaint_counts'][$travelId] ?? 0),
            'oldest_pending_bap' => $batch['oldest_pending_bap_by_travel'][$travelId] ?? null,
            'has_expired_cert' => isset($batch['expired_cert_travel_ids'][$travelId]),
            'has_recent_bap' => isset($batch['recent_bap_travel_ids'][$travelId]),
            'has_recent_jamaah' => isset($batch['recent_jamaah_travel_ids'][$travelId]),
        ];
    }

    /** @param  array{travel: ?TravelCompany, user_ids: Collection, findings: Collection, complaint_count?: int, oldest_pending_bap?: mixed, has_expired_cert?: bool, has_recent_bap?: bool, has_recent_jamaah?: bool}  $context
     * @return array<string, float>
     */
    private function calculateAllScores(array $context): array
    {
        return [
            'complaint_score' => $this->calculateComplaintScore(
                $context['travel']?->id ?? 0,
                $context['complaint_count'] ?? null,
            ),
            'inspection_score' => $this->calculateInspectionScore($context['findings']),
            'followup_score' => $this->calculateFollowupScore($context['findings']),
            'bap_score' => $this->calculateBapScore(
                $context['user_ids'],
                $context['oldest_pending_bap'] ?? null,
            ),
            'certificate_score' => $this->calculateCertificateScore(
                $context['travel'],
                array_key_exists('has_expired_cert', $context) ? $context['has_expired_cert'] : null,
            ),
            'activity_score' => $this->calculateActivityScore(
                $context['travel']?->id ?? 0,
                $context['user_ids'],
                array_key_exists('has_recent_bap', $context) ? $context['has_recent_bap'] : null,
                array_key_exists('has_recent_jamaah', $context) ? $context['has_recent_jamaah'] : null,
            ),
        ];
    }

    private function calculateComplaintScore(int $travelId, ?int $preloadedCount = null): float
    {
        if (! Schema::hasTable('pengaduan') || $travelId === 0) {
            return 0;
        }

        $count = $preloadedCount ?? Pengaduan::where('travels_id', $travelId)->count();

        return match (true) {
            $count === 0 => 0,
            $count <= 3 => 10,
            $count <= 6 => 20,
            default => 30,
        };
    }

  /** @param  Collection<int, InspectionFinding>  $findings */
    private function calculateInspectionScore(Collection $findings): float
    {
        $score = 0;

        foreach ($findings as $finding) {
            $severity = $finding->severity?->value ?? $finding->severity;
            $score += match ($severity) {
                'CRITICAL' => 20,
                'MAJOR' => 10,
                default => 5,
            };
        }

        return min(25, $score);
    }

    /** @param  Collection<int, InspectionFinding>  $findings */
    private function calculateFollowupScore(Collection $findings): float
    {
        $score = 0;

        foreach ($findings as $finding) {
            if ($finding->deadline && $finding->deadline->isPast()) {
                $score += 15;
                continue;
            }

            $status = $finding->status?->value ?? $finding->status;
            if (in_array($status, ['WAITING_RESPONSE', 'FOLLOWUP_UPLOADED', 'OPEN'], true)) {
                $score += 5;
            }
        }

        return min(15, $score);
    }

    /** @param  Collection<int, int>  $userIds */
    private function calculateBapScore(Collection $userIds, mixed $preloadedOldestPending = null): float
    {
        if (! Schema::hasTable('bap') || $userIds->isEmpty()) {
            return 0;
        }

        $oldestPending = $preloadedOldestPending ?? BAP::query()
            ->whereIn('user_id', $userIds)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->value('created_at');

        if (! $oldestPending) {
            return 0;
        }

        $days = now()->diffInDays($oldestPending);

        return match (true) {
            $days > 14 => 10,
            $days > 7 => 5,
            default => 0,
        };
    }

    private function calculateCertificateScore(?TravelCompany $travel, ?bool $preloadedHasExpired = null): float
    {
        if (! $travel) {
            return 0;
        }

        $score = 0;

        if ($travel->license_expiry) {
            if ($travel->license_expiry->isPast()) {
                $score = max($score, 10);
            } elseif ($travel->license_expiry->lte(now()->addDays(90))) {
                $score = max($score, 5);
            }
        }

        if (Schema::hasTable('sertifikat')) {
            $hasExpired = $preloadedHasExpired ?? Sertifikat::where('travel_id', $travel->id)
                ->whereIn('status', ['expired', 'revoked'])
                ->exists();

            if ($hasExpired) {
                $score = max($score, 10);
            }
        }

        return min(10, $score);
    }

    /** @param  Collection<int, int>  $userIds */
    private function calculateActivityScore(
        int $travelId,
        Collection $userIds,
        ?bool $preloadedHasRecentBap = null,
        ?bool $preloadedHasRecentJamaah = null,
    ): float {
        if ($travelId === 0) {
            return 0;
        }

        $score = 0;

        if (Schema::hasTable('bap') && $userIds->isNotEmpty()) {
            $hasRecentBap = $preloadedHasRecentBap ?? BAP::query()
                ->whereIn('user_id', $userIds)
                ->where('created_at', '>=', now()->subMonths(6))
                ->exists();

            if (! $hasRecentBap) {
                $score += 5;
            }
        }

        if (Schema::hasTable('jamaah')) {
            $hasRecentJamaah = $preloadedHasRecentJamaah ?? Jamaah::query()
                ->where('travel_id', $travelId)
                ->where('created_at', '>=', now()->subYear())
                ->exists();

            if (! $hasRecentJamaah) {
                $score += 10;
            }
        }

        return min(10, $score);
    }

    private function resolveRiskLevel(float $totalScore): string
    {
        return match (true) {
            $totalScore >= 76 => RiskLevel::Critical->value,
            $totalScore >= 51 => RiskLevel::High->value,
            $totalScore >= 26 => RiskLevel::Medium->value,
            default => RiskLevel::Low->value,
        };
    }

    /** @return array<int, string> */
    private function buildIndicatorNotes(array $scores): array
    {
        $notes = [];

        if ($scores['complaint_score'] > 0) {
            $notes[] = 'Terdapat pengaduan yang mempengaruhi skor risiko.';
        }
        if ($scores['inspection_score'] > 0) {
            $notes[] = 'Terdapat temuan pengawasan aktif.';
        }
        if ($scores['followup_score'] > 0) {
            $notes[] = 'Ada temuan yang menunggu atau melewati deadline tindak lanjut.';
        }
        if ($scores['bap_score'] > 0) {
            $notes[] = 'BAP pending melebihi batas waktu yang ditentukan.';
        }
        if ($scores['certificate_score'] > 0) {
            $notes[] = 'Izin atau sertifikat perlu perhatian segera.';
        }
        if ($scores['activity_score'] > 0) {
            $notes[] = 'Aktivitas operasional travel rendah (BAP/jamaah).';
        }

        if (empty($notes)) {
            $notes[] = 'Seluruh indikator dalam kondisi normal.';
        }

        return $notes;
    }

    private function clearDashboardCache(): void
    {
        DashboardCache::flush();
    }
}

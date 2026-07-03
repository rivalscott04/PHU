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
            foreach ($travels as $travel) {
                $this->recalculateForTravel($travel->id, logAudit: false);
                $count++;
            }
        });

        if ($logAudit) {
            $this->auditLogService->log(
                'risk',
                'recalculate',
                "menghitung ulang skor risiko untuk {$count} perusahaan travel"
            );
        }

        $this->clearDashboardCache();

        return $count;
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
     *     findings: Collection<int, InspectionFinding>
     * }
     */
    private function loadTravelContext(int $travelId): array
    {
        $travel = TravelCompany::find($travelId);
        $userIds = User::where('travel_id', $travelId)->pluck('id');

        $findings = collect();
        if (Schema::hasTable('pengawasan_temuan') && Schema::hasTable('pengawasan')) {
            $findings = InspectionFinding::query()
                ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
                ->where('pengawasan.travel_id', $travelId)
                ->whereNotIn('pengawasan_temuan.status', ['CLOSED', 'VERIFIED'])
                ->select('pengawasan_temuan.*')
                ->get();
        }

        return [
            'travel' => $travel,
            'user_ids' => $userIds,
            'findings' => $findings,
        ];
    }

    /** @param  array{travel: ?TravelCompany, user_ids: Collection, findings: Collection}  $context
     * @return array<string, float>
     */
    private function calculateAllScores(array $context): array
    {
        return [
            'complaint_score' => $this->calculateComplaintScore($context['travel']?->id ?? 0),
            'inspection_score' => $this->calculateInspectionScore($context['findings']),
            'followup_score' => $this->calculateFollowupScore($context['findings']),
            'bap_score' => $this->calculateBapScore($context['user_ids']),
            'certificate_score' => $this->calculateCertificateScore($context['travel']),
            'activity_score' => $this->calculateActivityScore($context['travel']?->id ?? 0, $context['user_ids']),
        ];
    }

    private function calculateComplaintScore(int $travelId): float
    {
        if (! Schema::hasTable('pengaduan') || $travelId === 0) {
            return 0;
        }

        $count = Pengaduan::where('travels_id', $travelId)->count();

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
    private function calculateBapScore(Collection $userIds): float
    {
        if (! Schema::hasTable('bap') || $userIds->isEmpty()) {
            return 0;
        }

        $oldestPending = BAP::query()
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

    private function calculateCertificateScore(?TravelCompany $travel): float
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
            $hasExpired = Sertifikat::where('travel_id', $travel->id)
                ->whereIn('status', ['expired', 'revoked'])
                ->exists();

            if ($hasExpired) {
                $score = max($score, 10);
            }
        }

        return min(10, $score);
    }

    /** @param  Collection<int, int>  $userIds */
    private function calculateActivityScore(int $travelId, Collection $userIds): float
    {
        if ($travelId === 0) {
            return 0;
        }

        $score = 0;

        if (Schema::hasTable('bap') && $userIds->isNotEmpty()) {
            $hasRecentBap = BAP::query()
                ->whereIn('user_id', $userIds)
                ->where('created_at', '>=', now()->subMonths(6))
                ->exists();

            if (! $hasRecentBap) {
                $score += 5;
            }
        }

        if (Schema::hasTable('jamaah')) {
            $hasRecentJamaah = Jamaah::query()
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

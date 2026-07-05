<?php

namespace App\Services;

use App\Enums\FindingStatus;
use App\Enums\RiskLevel;
use App\Enums\WorkQueueStatus;
use App\Enums\WorkQueueType;
use App\Models\Followup;
use App\Models\InspectionFinding;
use App\Models\Pengaduan;
use App\Models\RiskScore;
use App\Models\SupervisionWorkQueue;
use App\Models\User;
use App\Notifications\V2\PengaduanReceivedNotification;
use App\Repositories\WorkQueueRepository;
use App\Support\KabupatenScopeFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class WorkQueueService
{
    public function __construct(
        private readonly WorkQueueRepository $repository,
        private readonly NotificationService $notificationService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function handlePengaduanCreated(Pengaduan $pengaduan): SupervisionWorkQueue
    {
        $pengaduan->loadMissing('travel');
        $travel = $pengaduan->travel;

        $item = $this->repository->upsert(
            [
                'type' => WorkQueueType::Pengaduan->value,
                'reference_type' => 'pengaduan',
                'reference_id' => $pengaduan->id,
            ],
            [
                'priority' => 80,
                'travel_id' => $pengaduan->travels_id,
                'kabupaten' => $travel?->kab_kota,
                'title' => 'Pengaduan: '.$pengaduan->nama_pengadu,
                'summary' => str($pengaduan->hal_aduan)->limit(180)->toString(),
                'action_url' => route('pengaduan.show', $pengaduan->id),
                'status' => WorkQueueStatus::Open->value,
                'due_at' => now()->addDays(3),
            ]
        );

        if ($travel) {
            $this->notificationService->notifySupervisors(
                $travel,
                new PengaduanReceivedNotification($pengaduan)
            );
        }

        $this->auditLogService->log(
            'antrian',
            'enqueue',
            "menambahkan pengaduan #{$pengaduan->id} ke antrian kerja pengawasan"
        );

        return $item;
    }

    public function resolveFollowupQueue(Followup $followup): void
    {
        $item = $this->repository->findByReference(
            WorkQueueType::VerifikasiFollowup->value,
            'pengawasan_followups',
            $followup->id
        );

        if ($item && $item->isActionable()) {
            $item->update([
                'status' => WorkQueueStatus::Resolved,
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);
        }
    }

    public function handlePengaduanResolved(Pengaduan $pengaduan): void
    {
        $item = $this->repository->findByReference(
            WorkQueueType::Pengaduan->value,
            'pengaduan',
            $pengaduan->id
        );

        if (! $item || ! $item->isActionable()) {
            return;
        }

        $item->update([
            'status' => WorkQueueStatus::Resolved,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);
    }

    public function handleRiskScoreUpdated(RiskScore $riskScore): ?SupervisionWorkQueue
    {
        $level = $riskScore->risk_level?->value ?? $riskScore->risk_level;

        if (! in_array($level, [RiskLevel::High->value, RiskLevel::Critical->value], true)) {
            $existing = $this->repository->findByReference(
                WorkQueueType::RisikoTinggi->value,
                'risk_scores',
                $riskScore->id
            );

            if ($existing && $existing->isActionable()) {
                $existing->update([
                    'status' => WorkQueueStatus::Resolved,
                    'resolved_at' => now(),
                ]);
            }

            return null;
        }

        $riskScore->loadMissing('travel');
        $travel = $riskScore->travel;
        $priority = $level === RiskLevel::Critical->value ? 90 : 70;
        $levelLabel = RiskLevel::tryFrom($level)?->label() ?? $level;

        return $this->repository->upsert(
            [
                'type' => WorkQueueType::RisikoTinggi->value,
                'reference_type' => 'risk_scores',
                'reference_id' => $riskScore->id,
            ],
            [
                'priority' => $priority,
                'travel_id' => $riskScore->travel_id,
                'kabupaten' => $travel?->kab_kota,
                'title' => 'Skor risiko '.$levelLabel.': '.($travel?->Penyelenggara ?? 'Travel'),
                'summary' => "Total skor {$riskScore->total_score} poin. Perlu monitoring intensif atau jadwalkan inspeksi.",
                'action_url' => route('v2.risk.show', $riskScore->travel_id),
                'status' => WorkQueueStatus::Open->value,
                'due_at' => now()->addDays($level === RiskLevel::Critical->value ? 2 : 5),
            ]
        );
    }

    public function handleFollowupSubmitted(Followup $followup): SupervisionWorkQueue
    {
        $followup->loadMissing('finding.inspection.travel');
        $finding = $followup->finding;
        $travel = $finding?->inspection?->travel;

        return $this->repository->upsert(
            [
                'type' => WorkQueueType::VerifikasiFollowup->value,
                'reference_type' => 'pengawasan_followups',
                'reference_id' => $followup->id,
            ],
            [
                'priority' => 75,
                'travel_id' => $travel?->id,
                'kabupaten' => $travel?->kab_kota,
                'title' => 'Verifikasi tindak lanjut: '.($finding?->title ?? 'Temuan'),
                'summary' => str($followup->description)->limit(180)->toString(),
                'action_url' => route('v2.followup.show', $followup->id),
                'status' => WorkQueueStatus::Open->value,
                'due_at' => now()->addDays(5),
            ]
        );
    }

    public function syncOverdueFindings(?string $kabupaten = null): int
    {
        if (! Schema::hasTable('pengawasan_temuan')) {
            return 0;
        }

        $findings = InspectionFinding::query()
            ->with('inspection.travel')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<=', now())
            ->whereNotIn('status', [
                FindingStatus::Closed->value,
                FindingStatus::Verified->value,
            ])
            ->when($kabupaten, function ($query) use ($kabupaten) {
                $query->whereHas('inspection.travel', fn ($travelQuery) => $travelQuery->where('kab_kota', $kabupaten));
            })
            ->get();

        $count = 0;

        foreach ($findings as $finding) {
            $travel = $finding->inspection?->travel;
            $isOverdue = $finding->deadline?->isPast() ?? false;

            $this->repository->upsert(
                [
                    'type' => WorkQueueType::DeadlineTemuan->value,
                    'reference_type' => 'pengawasan_temuan',
                    'reference_id' => $finding->id,
                ],
                [
                    'priority' => $isOverdue ? 85 : 60,
                    'travel_id' => $travel?->id,
                    'kabupaten' => $travel?->kab_kota,
                    'title' => 'Deadline temuan: '.$finding->title,
                    'summary' => 'Batas waktu '.($finding->deadline?->format('d M Y') ?? '-').'. Segera tindak lanjuti.',
                    'action_url' => route('v2.pengawasan.show', $finding->inspection_id),
                    'status' => WorkQueueStatus::Open->value,
                    'due_at' => $finding->deadline,
                ]
            );

            $count++;
        }

        return $count;
    }

    /** @return array<string, mixed> */
    public function getSummary(array $filters = []): array
    {
        $byType = $this->repository->countOpenByType($filters);

        return [
            'total_open' => array_sum($byType),
            'pengaduan' => $byType[WorkQueueType::Pengaduan->value] ?? 0,
            'risiko_tinggi' => $byType[WorkQueueType::RisikoTinggi->value] ?? 0,
            'deadline_temuan' => $byType[WorkQueueType::DeadlineTemuan->value] ?? 0,
            'verifikasi_followup' => $byType[WorkQueueType::VerifikasiFollowup->value] ?? 0,
        ];
    }

    /** @return array<string, array{label: string, icon: string, color: string, value: int}> */
    public function getKpiCards(array $filters = []): array
    {
        $summary = $this->getSummary($filters);

        return [
            'total_open' => [
                'label' => 'Antrian Aktif',
                'icon' => 'bx-list-check',
                'color' => '#556ee6',
                'value' => $summary['total_open'],
            ],
            'pengaduan' => [
                'label' => 'Pengaduan Baru',
                'icon' => 'bx-message-square-dots',
                'color' => '#f46a6a',
                'value' => $summary['pengaduan'],
            ],
            'risiko_tinggi' => [
                'label' => 'Skor Risiko Tinggi',
                'icon' => 'bx-error',
                'color' => '#f1b44c',
                'value' => $summary['risiko_tinggi'],
            ],
            'deadline_temuan' => [
                'label' => 'Deadline Temuan',
                'icon' => 'bx-time-five',
                'color' => '#343a40',
                'value' => $summary['deadline_temuan'],
            ],
            'verifikasi_followup' => [
                'label' => 'Verifikasi TL',
                'icon' => 'bx-task',
                'color' => '#50a5f1',
                'value' => $summary['verifikasi_followup'],
            ],
        ];
    }

    public function paginate(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if ($user->role === 'pengawas') {
            $filters = array_merge($filters, KabupatenScopeFilter::pengawasFilters($user));
        }

        return $this->repository->paginate($filters, $perPage);
    }

    public function markInProgress(SupervisionWorkQueue $item, User $user): SupervisionWorkQueue
    {
        $item->update([
            'status' => WorkQueueStatus::InProgress,
            'assigned_to' => $user->id,
        ]);

        return $item->fresh(['travel']);
    }

    public function resolve(SupervisionWorkQueue $item, User $user): SupervisionWorkQueue
    {
        $item->update([
            'status' => WorkQueueStatus::Resolved,
            'resolved_by' => $user->id,
            'resolved_at' => now(),
        ]);

        return $item->fresh(['travel']);
    }
}

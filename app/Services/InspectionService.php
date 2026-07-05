<?php

namespace App\Services;

use App\Enums\ChecklistInputType;
use App\Enums\FindingStatus;
use App\Enums\InspectionStatus;
use App\Models\Inspection;
use App\Models\InspectionChecklist;
use App\Repositories\ChecklistRepository;
use App\Repositories\InspectionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Notifications\V2\InspectionCreatedNotification;
use App\Support\ChecklistScoring;
use App\Support\DashboardCache;

class InspectionService
{
    /** @var array<string, array<int, string>> */
    private const STATUS_TRANSITIONS = [
        'DRAFT' => ['SCHEDULED', 'CANCELLED'],
        'SCHEDULED' => ['ON_PROGRESS', 'CANCELLED'],
        'ON_PROGRESS' => ['WAITING_FOLLOWUP', 'CANCELLED'],
        'WAITING_FOLLOWUP' => ['FOLLOWUP_UPLOADED'],
        'FOLLOWUP_UPLOADED' => ['VERIFIED'],
        'VERIFIED' => ['CLOSED'],
        'CLOSED' => [],
        'CANCELLED' => [],
    ];

    public function __construct(
        private readonly InspectionRepository $inspectionRepository,
        private readonly ChecklistRepository $checklistRepository,
        private readonly AuditLogService $auditLogService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->inspectionRepository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Inspection
    {
        return $this->inspectionRepository->findById($id);
    }

    public function create(array $data): Inspection
    {
        return DB::transaction(function () use ($data) {
            $data['inspection_no'] = $this->generateInspectionNo();
            $inspection = $this->inspectionRepository->create($data);
            $this->generateChecklists($inspection);
            $inspection->load('travel');
            $travelName = $inspection->travel?->Penyelenggara ?? 'travel terkait';
            $this->auditLogService->log(
                'pengawasan',
                'create',
                "menjadwalkan pengawasan baru {$inspection->inspection_no} untuk {$travelName}"
            );

            $this->notificationService->notifyTravelUsers(
                $inspection->travel_id,
                new InspectionCreatedNotification($inspection)
            );

            DashboardCache::flush();

            return $inspection->fresh(['travel', 'checklists.masterChecklist']);
        });
    }

    public function update(Inspection $inspection, array $data): Inspection
    {
        if (isset($data['status'])) {
            $this->assertValidStatusTransition($inspection, $data['status']);
        }

        if ($inspection->status === InspectionStatus::Closed) {
            throw new InvalidArgumentException('Pengawasan yang sudah ditutup tidak dapat diubah.');
        }

        return DB::transaction(function () use ($inspection, $data) {
            $updated = $this->inspectionRepository->update($inspection, $data);
            $this->auditLogService->log(
                'pengawasan',
                'update',
                "memperbarui jadwal pengawasan {$updated->inspection_no}"
            );

            DashboardCache::flush();

            return $updated;
        });
    }

    public function generateInspectionNo(): string
    {
        $prefix = 'PWG'.now()->format('Y');
        $latest = Inspection::where('inspection_no', 'like', "{$prefix}%")
            ->orderByDesc('inspection_no')
            ->value('inspection_no');

        $sequence = 1;
        if ($latest) {
            $sequence = (int) substr($latest, -4) + 1;
        }

        return sprintf('%s%04d', $prefix, $sequence);
    }

    private function generateChecklists(Inspection $inspection): void
    {
        $activeChecklists = $this->checklistRepository->getActiveWithCategories()
            ->flatMap(fn ($category) => $category->checklists);

        $rows = $activeChecklists->map(fn ($checklist) => [
            'inspection_id' => $inspection->id,
            'master_checklist_id' => $checklist->id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        if ($rows !== []) {
            InspectionChecklist::insert($rows);
        }
    }

    private function assertValidStatusTransition(Inspection $inspection, string $newStatus): void
    {
        $current = $inspection->status instanceof InspectionStatus
            ? $inspection->status->value
            : (string) $inspection->status;

        $allowed = self::STATUS_TRANSITIONS[$current] ?? [];

        if (! in_array($newStatus, $allowed, true)) {
            throw new InvalidArgumentException("Transisi status dari {$current} ke {$newStatus} tidak diperbolehkan.");
        }
    }

    public function createFinding(Inspection $inspection, array $data): \App\Models\InspectionFinding
    {
        return DB::transaction(function () use ($inspection, $data) {
            $finding = $this->inspectionRepository->createFinding([
                ...$data,
                'inspection_id' => $inspection->id,
                'status' => FindingStatus::WaitingResponse->value,
            ]);

            $currentStatus = $inspection->status instanceof InspectionStatus
                ? $inspection->status
                : InspectionStatus::tryFrom((string) $inspection->status);

            if ($currentStatus === InspectionStatus::OnProgress) {
                $this->assertValidStatusTransition($inspection, InspectionStatus::WaitingFollowup->value);
                $this->inspectionRepository->update($inspection, [
                    'status' => InspectionStatus::WaitingFollowup->value,
                ]);
                $inspection->refresh();
            }

            $this->auditLogService->log(
                'pengawasan',
                'create',
                "mencatat temuan baru \"{$finding->title}\" pada pengawasan {$inspection->inspection_no}"
            );

            DashboardCache::flush();

            return $finding;
        });
    }

    public function updateFinding(\App\Models\InspectionFinding $finding, array $data): \App\Models\InspectionFinding
    {
        return DB::transaction(function () use ($finding, $data) {
            $updated = $this->inspectionRepository->updateFinding($finding, $data);
            $updated->load('inspection');
            $this->auditLogService->log(
                'pengawasan',
                'update',
                "memperbarui temuan \"{$updated->title}\" pada pengawasan {$updated->inspection?->inspection_no}"
            );

            DashboardCache::flush();

            return $updated;
        });
    }

    public function updateChecklists(Inspection $inspection, array $items): Inspection
    {
        if (in_array($inspection->status, [InspectionStatus::Closed, InspectionStatus::Cancelled], true)) {
            throw new InvalidArgumentException('Checklist pengawasan yang sudah ditutup atau dibatalkan tidak dapat diubah.');
        }

        return DB::transaction(function () use ($inspection, $items) {
            $existing = $inspection->checklists()
                ->with(['masterChecklist.options'])
                ->get()
                ->keyBy('id');

            foreach ($items as $itemData) {
                $checklist = $existing->get($itemData['id']);

                if ($checklist === null) {
                    throw new InvalidArgumentException('Item checklist tidak termasuk dalam pengawasan ini.');
                }

                $master = $checklist->masterChecklist;

                if ($master === null) {
                    continue;
                }

                $answer = $itemData['answer'] ?? null;

                if ($master->input_type === ChecklistInputType::Option && $answer !== null && $answer !== '') {
                    $validValues = $master->options->pluck('value')->all();

                    if (! in_array($answer, $validValues, true)) {
                        throw new InvalidArgumentException("Jawaban \"{$answer}\" tidak valid untuk pertanyaan \"{$master->title}\".");
                    }
                }

                $this->inspectionRepository->updateChecklist($checklist, [
                    'answer' => $answer,
                    'note' => $itemData['note'] ?? null,
                    'score' => ChecklistScoring::scoreItem($master, $answer),
                ]);
            }

            $inspection->load(['checklists.masterChecklist.options']);
            $overallScore = ChecklistScoring::overallScore($inspection->checklists);

            $updated = $this->inspectionRepository->update($inspection, [
                'overall_score' => $overallScore,
            ]);

            $this->auditLogService->log(
                'pengawasan',
                'update',
                "mengisi daftar periksa pengawasan {$updated->inspection_no}"
            );

            DashboardCache::flush();

            return $updated->load([
                'travel',
                'checklists.masterChecklist.category',
                'checklists.masterChecklist.options',
                'findings.followups',
            ]);
        });
    }
}

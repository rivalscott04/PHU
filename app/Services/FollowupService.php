<?php

namespace App\Services;

use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Models\Followup;
use App\Models\InspectionFinding;
use App\Repositories\FollowupRepository;
use App\Notifications\V2\FollowupApprovedNotification;
use App\Notifications\V2\FollowupRevisionNotification;
use App\Notifications\V2\FollowupUploadedNotification;
use App\Support\DashboardCache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FollowupService
{
    public function __construct(
        private readonly FollowupRepository $followupRepository,
        private readonly AuditLogService $auditLogService,
        private readonly RiskCalculationService $riskCalculationService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->followupRepository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Followup
    {
        return $this->followupRepository->findById($id);
    }

    public function submit(InspectionFinding $finding, array $data): Followup
    {
        $inspection = $finding->inspection;

        if (! in_array($inspection->status, [
            InspectionStatus::WaitingFollowup,
            InspectionStatus::FollowupUploaded,
        ], true) && ! in_array($inspection->status?->value ?? $inspection->status, [
            InspectionStatus::WaitingFollowup->value,
            InspectionStatus::FollowupUploaded->value,
        ], true)) {
            throw new ConflictHttpException('Tindak lanjut hanya dapat diunggah saat pengawasan menunggu tindak lanjut.');
        }

        if (! in_array($finding->status, [
            FindingStatus::WaitingResponse,
            FindingStatus::RevisionRequired,
        ], true) && ! in_array($finding->status?->value ?? $finding->status, [
            FindingStatus::WaitingResponse->value,
            FindingStatus::RevisionRequired->value,
        ], true)) {
            throw new ConflictHttpException('Temuan tidak dalam status yang mengizinkan upload tindak lanjut.');
        }

        $hasActiveFollowup = Followup::query()
            ->where('finding_id', $finding->id)
            ->whereIn('status', [
                FollowupStatus::Submitted,
                FollowupStatus::Pending,
            ])
            ->exists();

        if ($hasActiveFollowup) {
            throw new ConflictHttpException('Sudah ada tindak lanjut aktif untuk temuan ini.');
        }

        return DB::transaction(function () use ($finding, $data, $inspection) {
            $followup = $this->followupRepository->create([
                'finding_id' => $finding->id,
                'description' => $data['description'],
                'attachment' => $data['attachment'] ?? null,
                'status' => FollowupStatus::Submitted,
                'submitted_at' => now(),
            ]);

            $this->followupRepository->addLog($followup, [
                'status' => FollowupStatus::Submitted->value,
                'description' => 'Travel mengunggah bukti tindak lanjut.',
                'created_by' => auth()->id(),
            ]);

            $finding->update(['status' => FindingStatus::FollowupUploaded]);
            $inspection->update(['status' => InspectionStatus::FollowupUploaded]);

            $this->auditLogService->log(
                'followup',
                'upload',
                "mengunggah bukti tindak lanjut untuk temuan \"{$finding->title}\""
            );

            $followup = $followup->fresh(['finding.inspection.travel', 'logs']);
            $travel = $followup->finding?->inspection?->travel;
            if ($travel) {
                $this->notificationService->notifySupervisors(
                    $travel,
                    new FollowupUploadedNotification($followup)
                );
            }

            DashboardCache::flush();

            return $followup;
        });
    }

    public function approve(Followup $followup, ?string $remarks = null): Followup
    {
        if ($followup->status === FollowupStatus::Verified) {
            throw new ConflictHttpException('Tindak lanjut sudah diverifikasi.');
        }

        if (! in_array($followup->status, [
            FollowupStatus::Submitted,
            FollowupStatus::Pending,
        ], true) && ! in_array($followup->status?->value ?? $followup->status, [
            FollowupStatus::Submitted->value,
            FollowupStatus::Pending->value,
        ], true)) {
            throw new ConflictHttpException('Tindak lanjut tidak dalam status yang dapat disetujui.');
        }

        return DB::transaction(function () use ($followup, $remarks) {
            $updated = $this->followupRepository->update($followup, [
                'status' => FollowupStatus::Verified,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'remarks' => $remarks,
            ]);

            $this->followupRepository->addLog($updated, [
                'status' => FollowupStatus::Verified->value,
                'description' => $remarks ?? 'Tindak lanjut disetujui.',
                'created_by' => auth()->id(),
            ]);

            $finding = $updated->finding;
            $finding->update(['status' => FindingStatus::Verified]);

            $travelId = $finding->inspection->travel_id;
            $this->riskCalculationService->recalculateForTravel($travelId);

            $travelName = $finding->inspection?->travel?->Penyelenggara ?? 'travel terkait';
            $this->auditLogService->log(
                'followup',
                'approve',
                "menyetujui bukti tindak lanjut dari {$travelName} untuk temuan \"{$finding->title}\""
            );

            $updated = $updated->fresh(['finding.inspection', 'logs']);
            $this->notificationService->notifyTravelUsers(
                $travelId,
                new FollowupApprovedNotification($updated)
            );

            return $updated;
        });
    }

    public function requestRevision(Followup $followup, string $remarks): Followup
    {
        if ($followup->status === FollowupStatus::Verified) {
            throw new ConflictHttpException('Tindak lanjut yang sudah diverifikasi tidak dapat direvisi.');
        }

        if (! in_array($followup->status, [
            FollowupStatus::Submitted,
            FollowupStatus::Pending,
        ], true) && ! in_array($followup->status?->value ?? $followup->status, [
            FollowupStatus::Submitted->value,
            FollowupStatus::Pending->value,
        ], true)) {
            throw new ConflictHttpException('Tindak lanjut tidak dalam status yang dapat direvisi.');
        }

        return DB::transaction(function () use ($followup, $remarks) {
            $updated = $this->followupRepository->update($followup, [
                'status' => FollowupStatus::RevisionRequired,
                'remarks' => $remarks,
            ]);

            $this->followupRepository->addLog($updated, [
                'status' => FollowupStatus::RevisionRequired->value,
                'description' => $remarks,
                'created_by' => auth()->id(),
            ]);

            $updated->finding->update(['status' => FindingStatus::RevisionRequired]);

            $travelName = $updated->finding?->inspection?->travel?->Penyelenggara ?? 'travel terkait';
            $this->auditLogService->log(
                'followup',
                'revision',
                "meminta revisi bukti tindak lanjut dari {$travelName} untuk temuan \"{$updated->finding?->title}\""
            );

            $updated = $updated->fresh(['finding.inspection', 'logs']);
            $travelId = $updated->finding?->inspection?->travel_id;
            if ($travelId) {
                $this->notificationService->notifyTravelUsers(
                    $travelId,
                    new FollowupRevisionNotification($updated)
                );
            }

            DashboardCache::flush();

            return $updated;
        });
    }
}

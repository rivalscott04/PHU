<?php

namespace Tests\Unit\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\FollowupRepository;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class FollowupRepositoryTest extends TestCase
{
    use RunsV2Migrations;

    private FollowupRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->repository = new FollowupRepository();
    }

    public function test_paginate_filters_by_travel_and_status(): void
    {
        $admin = $this->seedAdminUser();
        $travelA = TravelCompany::first();
        $travelB = TravelCompany::skip(1)->first();
        $user = User::first();

        $findingA = $this->createFinding($travelA, $user, FindingStatus::WaitingResponse);
        $findingB = $this->createFinding($travelB, $user, FindingStatus::WaitingResponse);

        $this->repository->create([
            'finding_id' => $findingA->id,
            'description' => 'Bukti perbaikan travel A sudah dilakukan sesuai rekomendasi.',
            'status' => FollowupStatus::Submitted,
            'submitted_at' => now(),
        ]);

        $this->repository->create([
            'finding_id' => $findingB->id,
            'description' => 'Bukti perbaikan travel B sudah dilakukan sesuai rekomendasi.',
            'status' => FollowupStatus::Verified,
            'submitted_at' => now(),
        ]);

        $byTravel = $this->repository->paginate(['travel_id' => $travelA->id]);
        $this->assertSame(1, $byTravel->total());

        $byStatus = $this->repository->paginate(['status' => FollowupStatus::Verified->value]);
        $this->assertSame(1, $byStatus->total());
    }

    public function test_count_by_status_groups_followups(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();
        $finding = $this->createFinding($travel, $user, FindingStatus::WaitingResponse);

        $this->repository->create([
            'finding_id' => $finding->id,
            'description' => 'Bukti perbaikan sudah dilakukan sesuai rekomendasi.',
            'status' => FollowupStatus::Submitted,
            'submitted_at' => now(),
        ]);

        $counts = $this->repository->countByStatus();

        $this->assertSame(1, (int) ($counts[FollowupStatus::Submitted->value] ?? 0));
    }

    private function createFinding(TravelCompany $travel, User $user, FindingStatus $status): InspectionFinding
    {
        $inspectionId = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-'.uniqid(),
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::WaitingFollowup,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ])->id;

        return InspectionFinding::create([
            'inspection_id' => $inspectionId,
            'category' => 'Operasional',
            'severity' => FindingSeverity::Major,
            'title' => 'Temuan uji',
            'description' => 'Deskripsi temuan.',
            'recommendation' => 'Perbaiki segera.',
            'deadline' => now()->addWeek(),
            'status' => $status,
        ]);
    }
}

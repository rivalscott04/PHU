<?php

namespace Tests\Unit\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Inspection;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\ComplianceRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\RiskRepository;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class ComplianceRepositoryTest extends TestCase
{
    use RunsV2Migrations;

    private ComplianceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->repository = new ComplianceRepository(
            new RiskRepository(),
            new InspectionRepository(),
        );
    }

    public function test_get_statistics_returns_travel_compliance_counts(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-5001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 55,
            'risk_level' => RiskLevel::High,
            'last_calculated_at' => now(),
        ]);

        $stats = $this->repository->getStatistics($travel->fresh(['riskScore']));

        $this->assertSame(1, $stats['total_pengawasan']);
        $this->assertSame($travel->Status, $stats['travel_type']);
        $this->assertSame($travel->kab_kota, $stats['kabupaten']);
        $this->assertSame(RiskLevel::High, $stats['risk_score']->risk_level);
    }

    public function test_get_inspection_history_is_scoped_to_travel(): void
    {
        $travelA = TravelCompany::first();
        $travelB = TravelCompany::skip(1)->first();
        $user = User::first();

        Inspection::create([
            'travel_id' => $travelA->id,
            'inspection_no' => 'PWG-2026-5002',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        Inspection::create([
            'travel_id' => $travelB->id,
            'inspection_no' => 'PWG-2026-5003',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $history = $this->repository->getInspectionHistory($travelA->id);

        $this->assertSame(1, $history->total());
        $this->assertSame($travelA->id, $history->items()[0]->travel_id);
    }
}

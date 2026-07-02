<?php

namespace Tests\Unit\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\Inspection;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\InspectionRepository;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class InspectionRepositoryExportTest extends TestCase
{
    use RunsV2Migrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
    }

    public function test_list_for_export_applies_status_and_travel_filters(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();
        $repository = new InspectionRepository();

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-8001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-8002',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::SpotCheck,
            'status' => InspectionStatus::Closed,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $rows = $repository->listForExport([
            'travel_id' => $travel->id,
            'status' => InspectionStatus::OnProgress->value,
        ]);

        $this->assertCount(1, $rows);
        $this->assertSame('PWG-2026-8001', $rows->first()->inspection_no);
        $this->assertSame(0, $rows->first()->findings_count);
    }
}

<?php

namespace Tests\Unit\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\ChecklistCategory;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\ChecklistRepository;
use App\Repositories\FollowupRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\RiskRepository;
use App\Services\AuditLogService;
use App\Services\ChecklistService;
use App\Services\InspectionService;
use App\Services\NotificationService;
use App\Services\RiskCalculationService;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100001_create_master_checklist_categories_table.php',
                'database/migrations/2026_07_02_100002_create_master_checklists_table.php',
                'database/migrations/2026_07_02_100003_create_master_checklist_options_table.php',
                'database/migrations/2026_07_02_100004_create_pengawasan_table.php',
                'database/migrations/2026_07_02_100005_create_pengawasan_checklists_table.php',
                'database/migrations/2026_07_02_100006_create_pengawasan_temuan_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
                'database/migrations/2026_07_02_100012_create_notifications_table.php',
            ],
        ]);
    }

    public function test_checklist_service_generates_unique_codes(): void
    {
        $service = new ChecklistService(new ChecklistRepository(), new AuditLogService());

        $category = ChecklistCategory::create([
            'name' => 'Legalitas',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $first = $service->create([
            'category_id' => $category->id,
            'title' => 'Izin Operasional',
            'input_type' => 'BOOLEAN',
            'weight' => 10,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $second = $service->create([
            'category_id' => $category->id,
            'title' => 'Akreditasi Berlaku',
            'input_type' => 'BOOLEAN',
            'weight' => 5,
            'required' => true,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $period = now()->format('Ym');
        $this->assertSame("LEG{$period}001", $first->code);
        $this->assertSame("LEG{$period}002", $second->code);
    }

    public function test_inspection_service_generates_checklists_on_create(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        ChecklistCategory::create(['name' => 'Legalitas', 'sort_order' => 1, 'is_active' => true])
            ->checklists()
            ->create([
                'code' => 'LEG-001',
                'title' => 'Izin Operasional',
                'input_type' => 'BOOLEAN',
                'weight' => 10,
                'required' => true,
                'sort_order' => 1,
                'is_active' => true,
            ]);

        $service = new InspectionService(
            new InspectionRepository(),
            new ChecklistRepository(),
            new AuditLogService(),
            new NotificationService(),
        );

        $inspection = $service->create([
            'travel_id' => $travel->id,
            'inspection_no' => $service->generateInspectionNo(),
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->assertCount(1, $inspection->checklists);
        $this->assertDatabaseHas('audit_logs', [
            'module' => 'pengawasan',
            'action' => 'create',
        ]);
    }

    public function test_inspection_service_blocks_invalid_status_transition(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        $inspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0999',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = new InspectionService(
            new InspectionRepository(),
            new ChecklistRepository(),
            new AuditLogService(),
            new NotificationService(),
        );

        $this->expectException(InvalidArgumentException::class);

        $service->update($inspection, ['status' => InspectionStatus::Closed->value]);
    }

    public function test_risk_calculation_service_calculates_score(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        $inspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0888',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        InspectionFinding::create([
            'inspection_id' => $inspection->id,
            'category' => 'Legalitas',
            'severity' => FindingSeverity::Critical,
            'title' => 'Temuan kritis',
            'description' => 'Deskripsi temuan.',
            'recommendation' => 'Segera perbaiki.',
            'deadline' => now()->subDay(),
            'status' => FindingStatus::Open,
        ]);

        $service = new RiskCalculationService(new RiskRepository(), new AuditLogService());
        $score = $service->recalculateForTravel($travel->id);

        $this->assertGreaterThan(0, $score->total_score);
        $this->assertDatabaseHas('risk_scores', ['travel_id' => $travel->id]);
        $this->assertDatabaseHas('audit_logs', ['module' => 'risk', 'action' => 'recalculate']);
    }
}

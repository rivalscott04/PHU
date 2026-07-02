<?php

namespace Tests\Unit\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Checklist;
use App\Models\ChecklistCategory;
use App\Models\Inspection;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\ChecklistRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\MonitoringRepository;
use App\Repositories\RiskRepository;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    private ChecklistRepository $checklistRepository;

    private InspectionRepository $inspectionRepository;

    private RiskRepository $riskRepository;

    private MonitoringRepository $monitoringRepository;

    private DashboardRepository $dashboardRepository;

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
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
            ],
        ]);

        $this->checklistRepository = new ChecklistRepository();
        $this->inspectionRepository = new InspectionRepository();
        $this->riskRepository = new RiskRepository();
        $this->monitoringRepository = new MonitoringRepository();
        $this->dashboardRepository = new DashboardRepository(
            $this->monitoringRepository,
            $this->riskRepository,
        );
    }

    public function test_checklist_repository_returns_active_checklists(): void
    {
        $category = $this->checklistRepository->createCategory([
            'name' => 'Legalitas',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->checklistRepository->create([
            'category_id' => $category->id,
            'code' => 'LEG-001',
            'title' => 'Izin Operasional',
            'input_type' => 'BOOLEAN',
            'weight' => 10,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->checklistRepository->create([
            'category_id' => $category->id,
            'code' => 'LEG-002',
            'title' => 'Checklist Nonaktif',
            'input_type' => 'BOOLEAN',
            'weight' => 5,
            'required' => false,
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $active = $this->checklistRepository->getActiveWithCategories();

        $this->assertCount(1, $active);
        $this->assertCount(1, $active->first()->checklists);
        $this->assertSame('LEG-001', $active->first()->checklists->first()->code);
    }

    public function test_inspection_repository_paginates_with_filters(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        $this->inspectionRepository->create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0100',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->inspectionRepository->create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0101',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::SpotCheck,
            'status' => InspectionStatus::Closed,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $filtered = $this->inspectionRepository->paginate(['status' => InspectionStatus::OnProgress->value]);

        $this->assertSame(1, $filtered->total());
        $this->assertSame('PWG-2026-0100', $filtered->items()[0]->inspection_no);
    }

    public function test_risk_repository_upsert_and_ranking(): void
    {
        $travel = TravelCompany::first();

        $this->riskRepository->upsertForTravel($travel->id, [
            'total_score' => 85,
            'risk_level' => RiskLevel::High,
            'last_calculated_at' => now(),
        ]);

        $score = $this->riskRepository->findByTravelId($travel->id);

        $this->assertNotNull($score);
        $this->assertSame(RiskLevel::High, $score->risk_level);

        $ranking = $this->riskRepository->getRanking(5);

        $this->assertCount(1, $ranking);
        $this->assertSame($travel->id, $ranking->first()->travel_id);
    }

    public function test_monitoring_and_dashboard_repositories_return_summary(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0200',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 90,
            'risk_level' => RiskLevel::Critical,
            'last_calculated_at' => now(),
        ]);

        $kpi = $this->monitoringRepository->getKpiSummary();
        $stats = $this->dashboardRepository->getOverviewStats();
        $ranking = $this->dashboardRepository->getRiskRanking(3);

        $this->assertGreaterThan(0, $kpi['total_travel']);
        $this->assertSame(1, $kpi['pengawasan_berjalan']);
        $this->assertSame(1, $kpi['travel_risiko_tinggi']);
        $this->assertArrayHasKey('total_travel', $stats);
        $this->assertCount(1, $ranking);
    }
}

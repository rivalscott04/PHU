<?php

namespace Tests\Feature\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\AuditLog;
use App\Models\Inspection;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\DashboardRepository;
use App\Repositories\MonitoringRepository;
use App\Repositories\RiskRepository;
use App\Services\DashboardService;
use App\Support\DashboardFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class DashboardPerformanceTest extends TestCase
{
    use RunsV2Migrations;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runV2Migrations();
        $this->admin = $this->seedAdminUser();

        $this->seedDashboardSampleData();
    }

    public function test_dashboard_overview_loads_within_three_seconds(): void
    {
        $start = microtime(true);

        $response = $this->actingAs($this->admin)->get(route('v2.dashboard'));

        $elapsed = microtime(true) - $start;

        $response->assertOk();
        $this->assertLessThan(3, $elapsed, 'Dashboard harus dimuat kurang dari 3 detik.');
    }

    public function test_dashboard_repository_timeline_does_not_lazy_load_relations(): void
    {
        Model::preventLazyLoading();

        $repository = new DashboardRepository(
            new MonitoringRepository(),
            new RiskRepository(),
        );

        $timeline = $repository->getTimeline(new DashboardFilter());

        $this->assertNotEmpty($timeline);
    }

    public function test_dashboard_overview_uses_bounded_query_count(): void
    {
        $service = app(DashboardService::class);
        $filter = new DashboardFilter();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $service->getOverview($filter);

        $queryCount = count(DB::getQueryLog());

        $this->assertLessThan(50, $queryCount, 'Dashboard overview tidak boleh mengeksekusi terlalu banyak query.');
    }

    public function test_cached_dashboard_overview_reduces_queries(): void
    {
        $service = app(DashboardService::class);
        $filter = new DashboardFilter();

        $service->getOverview($filter);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $service->getOverview($filter);

        $this->assertLessThanOrEqual(2, count(DB::getQueryLog()), 'Cache dashboard harus mengurangi query pada permintaan berikutnya.');
    }

    private function seedDashboardSampleData(): void
    {
        $travel = TravelCompany::first();
        $user = $this->admin;

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-9001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 85,
            'risk_level' => RiskLevel::High,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'module' => 'pengawasan',
            'action' => 'create',
            'description' => 'menjadwalkan pengawasan baru',
        ]);
    }
}

<?php

namespace Tests\Unit\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\DashboardRepository;
use App\Repositories\MonitoringRepository;
use App\Repositories\RiskRepository;
use App\Support\DashboardFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DashboardRepositoryTest extends TestCase
{
    private DashboardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100004_create_pengawasan_table.php',
                'database/migrations/2026_07_02_100006_create_pengawasan_temuan_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
            ],
        ]);

        $this->repository = new DashboardRepository(
            new MonitoringRepository(),
            new RiskRepository(),
        );
    }

    public function test_kpi_stats_returns_cards_with_trend(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-3001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $filter = new DashboardFilter();
        $stats = $this->repository->getKpiStats($filter);

        $this->assertArrayHasKey('total_ppiu', $stats);
        $this->assertArrayHasKey('label', $stats['total_ppiu']);
        $this->assertArrayHasKey('value', $stats['total_ppiu']);
        $this->assertArrayHasKey('trend', $stats['total_ppiu']);
        $this->assertArrayHasKey('direction', $stats['total_ppiu']);
        $this->assertGreaterThan(0, $stats['pengawasan_berjalan']['value']);
    }

    public function test_charts_returns_all_chart_datasets(): void
    {
        $filter = new DashboardFilter(tahun: (int) now()->year);
        $charts = $this->repository->getCharts($filter);

        $this->assertArrayHasKey('jamaah_monthly', $charts);
        $this->assertArrayHasKey('risk_distribution', $charts);
        $this->assertArrayHasKey('temuan_severity', $charts);
        $this->assertCount(12, $charts['jamaah_monthly']['labels']);
    }

    public function test_rankings_and_warnings(): void
    {
        $travel = TravelCompany::first();

        RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 90,
            'risk_level' => RiskLevel::Critical,
        ]);

        InspectionFinding::create([
            'inspection_id' => Inspection::create([
                'travel_id' => $travel->id,
                'inspection_no' => 'PWG-2026-3002',
                'inspection_date' => now(),
                'inspection_type' => InspectionType::Routine,
                'status' => InspectionStatus::WaitingFollowup,
                'created_by' => User::first()->id,
                'updated_by' => User::first()->id,
            ])->id,
            'category' => 'Legalitas',
            'severity' => 'CRITICAL',
            'title' => 'Temuan kritis',
            'description' => 'Deskripsi',
            'recommendation' => 'Rekomendasi',
            'status' => 'OPEN',
        ]);

        $filter = new DashboardFilter();
        $rankings = $this->repository->getRankings($filter);
        $warnings = $this->repository->getEarlyWarnings($filter);

        $this->assertNotEmpty($rankings['risk']);
        $this->assertNotEmpty($warnings);
    }

    public function test_timeline_includes_inspection_event(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-3003',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $timeline = $this->repository->getTimeline(new DashboardFilter());

        $this->assertNotEmpty($timeline);
        $this->assertSame('pengawasan', $timeline[0]['type']);
    }
}

<?php

namespace Tests\Feature\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\ChecklistCategory;
use App\Models\Inspection;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    private User $admin;

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
            ],
        ]);

        $this->admin = User::first();
        \DB::table('users')->where('id', $this->admin->id)->update([
            'role' => 'admin',
            'is_password_changed' => true,
        ]);
        $this->admin->refresh();
    }

    public function test_admin_can_view_v2_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('v2.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Pengawasan V2');
        $response->assertSee('Early Warning');
        $response->assertSee('Activity Timeline');
    }

    public function test_dashboard_statistics_endpoint_returns_kpi_cards(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('v2.dashboard.statistics'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_ppiu' => ['label', 'value', 'trend', 'direction'],
                ],
            ]);
    }

    public function test_dashboard_charts_endpoint_returns_chart_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('v2.dashboard.charts', ['tahun' => now()->year]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['jamaah_monthly', 'risk_distribution', 'temuan_severity']]);
    }

    public function test_dashboard_warning_endpoint_returns_warnings(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('v2.dashboard.warning'));

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_admin_can_create_pengawasan(): void
    {
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

        $travel = TravelCompany::first();

        $response = $this->actingAs($this->admin)->post(route('v2.pengawasan.store'), [
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-1001',
            'inspection_date' => now()->format('Y-m-d'),
            'inspection_type' => InspectionType::Routine->value,
            'notes' => 'Pengawasan awal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pengawasan', ['inspection_no' => 'PWG-2026-1001']);
        $this->assertDatabaseHas('pengawasan_checklists', [
            'inspection_id' => Inspection::first()->id,
        ]);
    }

    public function test_monitoring_statistics_returns_json(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('v2.monitoring.statistics'));

        $response->assertOk()
            ->assertJsonStructure(['success', 'message', 'data' => ['total_travel']]);
    }

    public function test_admin_can_create_checklist(): void
    {
        $category = ChecklistCategory::create([
            'name' => 'Operasional',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('v2.checklist.store'), [
            'category_id' => $category->id,
            'title' => 'Kantor Aktif',
            'input_type' => 'BOOLEAN',
            'weight' => 7,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('v2.checklist.index'));
        $this->assertDatabaseHas('master_checklists', [
            'title' => 'Kantor Aktif',
            'code' => 'OPS'.now()->format('Ym').'001',
        ]);
    }

    public function test_admin_can_save_pengawasan_checklists_and_score(): void
    {
        $category = ChecklistCategory::create(['name' => 'Legalitas', 'sort_order' => 1, 'is_active' => true]);
        $master = $category->checklists()->create([
            'code' => 'LEG-001',
            'title' => 'Izin Operasional',
            'input_type' => 'BOOLEAN',
            'weight' => 10,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $travel = TravelCompany::first();
        $inspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-2001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine->value,
            'status' => InspectionStatus::Draft->value,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $item = $inspection->checklists()->create([
            'master_checklist_id' => $master->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('v2.pengawasan.checklist.update', $inspection), [
            'items' => [
                ['id' => $item->id, 'answer' => '1', 'note' => 'Sudah dicek'],
            ],
        ]);

        $response->assertRedirect(route('v2.pengawasan.show', $inspection));
        $this->assertDatabaseHas('pengawasan_checklists', [
            'id' => $item->id,
            'answer' => '1',
            'score' => 10,
            'note' => 'Sudah dicek',
        ]);
        $this->assertDatabaseHas('pengawasan', [
            'id' => $inspection->id,
            'overall_score' => 100,
        ]);
    }

    public function test_guest_cannot_access_v2_routes(): void
    {
        $this->get(route('v2.dashboard'))->assertRedirect(route('login'));
    }
}

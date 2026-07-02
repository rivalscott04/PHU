<?php

namespace Tests\Feature\V2;

use App\Models\Inspection;
use App\Models\TravelCompany;
use App\Models\User;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExportTest extends TestCase
{
    private User $admin;

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

        $this->admin = User::first();
        \DB::table('users')->where('id', $this->admin->id)->update([
            'role' => 'admin',
            'is_password_changed' => true,
        ]);
        $this->admin->refresh();
    }

    public function test_admin_can_export_travel_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('v2.export.travel', ['format' => 'xlsx']));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertDatabaseHas('audit_logs', [
            'module' => 'export',
            'action' => 'export',
        ]);
    }

    public function test_admin_can_export_monitoring_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('v2.export.monitoring', ['format' => 'csv']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_admin_can_export_pengawasan_pdf(): void
    {
        $travel = TravelCompany::first();
        Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-3001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('v2.export.pengawasan'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_admin_can_export_dashboard_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('v2.export.dashboard', [
            'tahun' => now()->year,
            'bulan' => now()->month,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_export_logs_plain_language_activity(): void
    {
        $this->actingAs($this->admin)->get(route('v2.export.travel', ['format' => 'xlsx']));

        $this->assertDatabaseHas('audit_logs', [
            'description' => 'mengekspor daftar travel ke Excel',
        ]);
    }
}

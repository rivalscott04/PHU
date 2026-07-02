<?php

namespace Tests\Feature\V2;

use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CalculateRiskScoresCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
            ],
        ]);
    }

    public function test_risk_calculate_command_updates_all_travels(): void
    {
        $this->assertSame(0, RiskScore::count());

        Artisan::call('risk:calculate');

        $this->assertGreaterThan(0, RiskScore::count());
        $this->assertSame(0, Artisan::call('risk:calculate'));
    }

    public function test_risk_calculate_command_for_single_travel(): void
    {
        $travel = TravelCompany::first();

        Artisan::call('risk:calculate', ['--travel' => $travel->id]);

        $this->assertDatabaseHas('risk_scores', [
            'travel_id' => $travel->id,
        ]);
    }

    public function test_risk_calculate_is_scheduled_daily(): void
    {
        Artisan::call('schedule:list');
        $output = Artisan::output();

        $this->assertStringContainsString('risk:calculate', $output);
        $this->assertStringContainsString('30 0 * * *', $output);
    }
}

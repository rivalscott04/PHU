<?php

namespace Tests\Unit\V2;

use App\Enums\RiskLevel;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Repositories\ComplianceRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\RiskRepository;
use App\Services\ComplianceService;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class ComplianceServiceTest extends TestCase
{
    use RunsV2Migrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
    }

    public function test_get_profile_returns_statistics_and_recommendations(): void
    {
        $travel = TravelCompany::first();
        $travel->update(['license_expiry' => now()->subDay()]);

        RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 80,
            'risk_level' => RiskLevel::Critical,
            'last_calculated_at' => now(),
        ]);

        $service = new ComplianceService(new ComplianceRepository(
            new RiskRepository(),
            new InspectionRepository(),
        ));

        $profile = $service->getProfile($travel->id);

        $this->assertArrayHasKey('travel', $profile);
        $this->assertArrayHasKey('statistics', $profile);
        $this->assertArrayHasKey('inspection_history', $profile);
        $this->assertArrayHasKey('recommendations', $profile);
        $this->assertNotEmpty($profile['recommendations']);
        $this->assertStringContainsString('izin', strtolower($profile['recommendations'][0]));
    }
}

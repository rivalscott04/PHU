<?php

namespace Tests\Feature\V2;

use App\Enums\RiskLevel;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class RiskControllerTest extends TestCase
{
    use RunsV2Migrations;

    private TravelCompany $travel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->seedAdminUser();
        $this->travel = TravelCompany::first();
    }

    public function test_admin_can_view_risk_index_and_detail(): void
    {
        $admin = $this->seedAdminUser();

        RiskScore::create([
            'travel_id' => $this->travel->id,
            'total_score' => 72,
            'risk_level' => RiskLevel::High,
            'last_calculated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('v2.risk.index'))
            ->assertOk()
            ->assertSee('Risk Score');

        $this->actingAs($admin)
            ->get(route('v2.risk.show', $this->travel))
            ->assertOk()
            ->assertSee('Breakdown Indikator');
    }

    public function test_admin_can_recalculate_all_risk_scores(): void
    {
        $admin = $this->seedAdminUser();

        $this->actingAs($admin)
            ->post(route('v2.risk.recalculate'))
            ->assertRedirect();

        $this->assertGreaterThan(0, RiskScore::count());
    }

    public function test_risk_show_json_includes_breakdown(): void
    {
        $admin = $this->seedAdminUser();

        $this->actingAs($admin)
            ->getJson(route('v2.risk.show', $this->travel))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['risk', 'breakdown']]);
    }
}

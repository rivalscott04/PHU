<?php

namespace Tests\Feature\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Services\FollowupService;
use Illuminate\Http\UploadedFile;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class ComplianceControllerTest extends TestCase
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

    public function test_admin_can_view_compliance_index(): void
    {
        $admin = $this->seedAdminUser();

        $this->actingAs($admin)
            ->get(route('v2.compliance.index'))
            ->assertOk()
            ->assertSee('Profil Kepatuhan');
    }

    public function test_travel_user_only_sees_own_compliance_profile(): void
    {
        $travelUser = $this->seedTravelUser($this->travel);
        $otherTravel = TravelCompany::skip(1)->first();

        $this->actingAs($travelUser)
            ->get(route('v2.compliance.show', $this->travel))
            ->assertOk();

        $this->actingAs($travelUser)
            ->get(route('v2.compliance.show', $otherTravel))
            ->assertForbidden();
    }

    public function test_compliance_show_json_returns_profile_structure(): void
    {
        $admin = $this->seedAdminUser();

        RiskScore::create([
            'travel_id' => $this->travel->id,
            'total_score' => 40,
            'risk_level' => RiskLevel::Medium,
            'last_calculated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->getJson(route('v2.compliance.show', $this->travel))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['travel', 'statistics', 'inspection_history', 'recommendations'],
            ]);
    }
}

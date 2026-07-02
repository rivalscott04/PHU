<?php

namespace Tests\Feature\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Services\FollowupService;
use Illuminate\Http\UploadedFile;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class KabupatenScopeTest extends TestCase
{
    use RunsV2Migrations;

    private User $admin;
    private User $kabupaten;
    private User $otherKabupaten;
    private TravelCompany $travelInScope;
    private TravelCompany $travelOutOfScope;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->admin = $this->seedAdminUser();

        $this->travelInScope = TravelCompany::where('kab_kota', 'Lombok Barat')->first();
        $this->travelOutOfScope = TravelCompany::where('kab_kota', '!=', 'Lombok Barat')->first();

        $this->kabupaten = $this->seedKabupatenUser('Lombok Barat');
        $this->otherKabupaten = $this->seedKabupatenUser('Lombok Tengah');
    }

    public function test_kabupaten_only_sees_compliance_travels_in_wilayah(): void
    {
        $this->actingAs($this->kabupaten)
            ->get(route('v2.compliance.index'))
            ->assertOk()
            ->assertSee($this->travelInScope->Penyelenggara)
            ->assertDontSee($this->travelOutOfScope->Penyelenggara);
    }

    public function test_kabupaten_cannot_view_compliance_profile_outside_wilayah(): void
    {
        $this->actingAs($this->kabupaten)
            ->get(route('v2.compliance.show', $this->travelOutOfScope))
            ->assertForbidden();
    }

    public function test_kabupaten_only_sees_risk_scores_in_wilayah(): void
    {
        RiskScore::create([
            'travel_id' => $this->travelInScope->id,
            'total_score' => 40,
            'risk_level' => RiskLevel::Medium,
            'last_calculated_at' => now(),
        ]);
        RiskScore::create([
            'travel_id' => $this->travelOutOfScope->id,
            'total_score' => 80,
            'risk_level' => RiskLevel::Critical,
            'last_calculated_at' => now(),
        ]);

        $this->actingAs($this->kabupaten)
            ->get(route('v2.risk.index'))
            ->assertOk()
            ->assertSee($this->travelInScope->Penyelenggara)
            ->assertDontSee($this->travelOutOfScope->Penyelenggara);
    }

    public function test_kabupaten_cannot_recalculate_risk(): void
    {
        $this->actingAs($this->kabupaten)
            ->post(route('v2.risk.recalculate'))
            ->assertForbidden();
    }

    public function test_kabupaten_only_sees_followups_in_wilayah(): void
    {
        $inScopeFollowup = $this->createFollowupForTravel($this->travelInScope);
        $outOfScopeFollowup = $this->createFollowupForTravel($this->travelOutOfScope);

        $this->actingAs($this->kabupaten)
            ->get(route('v2.followup.index'))
            ->assertOk()
            ->assertSee($inScopeFollowup->finding?->title)
            ->assertDontSee($outOfScopeFollowup->finding?->title);
    }

    public function test_kabupaten_can_approve_followup_in_wilayah(): void
    {
        $followup = $this->createFollowupForTravel($this->travelInScope);

        $this->actingAs($this->kabupaten)
            ->post(route('v2.followup.approve', $followup), ['remarks' => 'Sudah sesuai.'])
            ->assertRedirect();

        $this->assertDatabaseHas('pengawasan_followups', [
            'id' => $followup->id,
            'status' => FollowupStatus::Verified->value,
        ]);
    }

    public function test_kabupaten_cannot_approve_followup_outside_wilayah(): void
    {
        $followup = $this->createFollowupForTravel($this->travelOutOfScope);

        $this->actingAs($this->kabupaten)
            ->post(route('v2.followup.approve', $followup), ['remarks' => 'Sudah sesuai.'])
            ->assertForbidden();
    }

    private function createFollowupForTravel(TravelCompany $travel): Followup
    {
        $inspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-'.uniqid(),
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::WaitingFollowup,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $finding = InspectionFinding::create([
            'inspection_id' => $inspection->id,
            'category' => 'Operasional',
            'severity' => FindingSeverity::Major,
            'title' => 'Temuan '.$travel->kab_kota.' '.uniqid(),
            'description' => 'Deskripsi temuan.',
            'recommendation' => 'Perbaiki segera.',
            'deadline' => now()->addWeek(),
            'status' => FindingStatus::WaitingResponse,
        ]);

        return app(FollowupService::class)->submit($finding, [
            'description' => 'Bukti perbaikan telah dilakukan sesuai rekomendasi pengawas.',
            'attachment' => 'followups/test.pdf',
        ]);
    }
}

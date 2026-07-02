<?php

namespace Tests\Feature\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use App\Services\FollowupService;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class FollowupControllerTest extends TestCase
{
    use RunsV2Migrations;

    private User $admin;
    private User $travelUser;
    private TravelCompany $travel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->admin = $this->seedAdminUser();
        $this->travel = TravelCompany::first();
        $this->travelUser = $this->seedTravelUser($this->travel);
    }

    public function test_admin_can_view_followup_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('v2.followup.index'))
            ->assertOk();
    }

    public function test_travel_user_can_upload_followup(): void
    {
        $finding = $this->createFindingReadyForFollowup();

        $this->actingAs($this->travelUser)->post(route('v2.followup.store'), [
            'finding_id' => $finding->id,
            'description' => 'Bukti perbaikan telah dilakukan sesuai rekomendasi pengawas.',
            'attachment' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $this->assertDatabaseHas('pengawasan_followups', [
            'finding_id' => $finding->id,
            'status' => FollowupStatus::Submitted->value,
        ]);
    }

    public function test_admin_can_approve_followup(): void
    {
        $finding = $this->createFindingReadyForFollowup();
        $followup = app(FollowupService::class)->submit($finding, [
            'description' => 'Bukti perbaikan telah dilakukan sesuai rekomendasi pengawas.',
            'attachment' => 'followups/test.pdf',
        ]);

        $this->actingAs($this->admin)->post(route('v2.followup.approve', $followup), [
            'remarks' => 'Sudah sesuai.',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengawasan_followups', [
            'id' => $followup->id,
            'status' => FollowupStatus::Verified->value,
        ]);
    }

    public function test_travel_user_cannot_approve_followup(): void
    {
        $finding = $this->createFindingReadyForFollowup();
        $followup = app(FollowupService::class)->submit($finding, [
            'description' => 'Bukti perbaikan telah dilakukan sesuai rekomendasi pengawas.',
            'attachment' => 'followups/test.pdf',
        ]);

        $this->actingAs($this->travelUser)
            ->post(route('v2.followup.approve', $followup))
            ->assertForbidden();
    }

    private function createFindingReadyForFollowup(): InspectionFinding
    {
        $inspection = Inspection::create([
            'travel_id' => $this->travel->id,
            'inspection_no' => 'PWG-2026-'.uniqid(),
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::WaitingFollowup,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        return InspectionFinding::create([
            'inspection_id' => $inspection->id,
            'category' => 'Operasional',
            'severity' => FindingSeverity::Major,
            'title' => 'Dokumen tidak lengkap',
            'description' => 'Lengkapi dokumen.',
            'recommendation' => 'Upload dokumen.',
            'deadline' => now()->addWeek(),
            'status' => FindingStatus::WaitingResponse,
        ]);
    }
}

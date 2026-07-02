<?php

namespace Tests\Unit\V2;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\AuditLog;
use App\Models\Checklist;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\ChecklistPolicy;
use App\Policies\FollowupPolicy;
use App\Policies\InspectionPolicy;
use App\Policies\RiskPolicy;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    private User $admin;

    private User $kabupaten;

    private User $travelUser;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100002_create_master_checklists_table.php',
                'database/migrations/2026_07_02_100001_create_master_checklist_categories_table.php',
                'database/migrations/2026_07_02_100004_create_pengawasan_table.php',
                'database/migrations/2026_07_02_100006_create_pengawasan_temuan_table.php',
                'database/migrations/2026_07_02_100008_create_pengawasan_followups_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
            ],
        ]);

        $this->admin = User::first();
        \DB::table('users')->where('id', $this->admin->id)->update(['role' => 'admin']);
        $this->admin->refresh();

        $kabId = \DB::table('users')->insertGetId([
            'username' => 'kabupaten',
            'firstname' => 'Admin',
            'lastname' => 'Kabupaten',
            'email' => 'kab@phu.com',
            'password' => bcrypt('password'),
            'role' => 'kabupaten',
            'kabupaten' => 'Lombok Barat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->kabupaten = User::find($kabId);

        $travel = TravelCompany::first();
        $travelUserId = \DB::table('users')->insertGetId([
            'username' => 'traveluser',
            'firstname' => 'Travel',
            'lastname' => 'User',
            'email' => 'travel@phu.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'travel_id' => $travel->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->travelUser = User::find($travelUserId);
    }

    public function test_checklist_policy_allows_admin_crud_only(): void
    {
        $policy = new ChecklistPolicy();
        $checklist = new Checklist();

        $this->assertTrue($policy->create($this->admin));
        $this->assertFalse($policy->create($this->kabupaten));
        $this->assertTrue($policy->view($this->kabupaten, $checklist));
        $this->assertFalse($policy->view($this->travelUser, $checklist));
    }

    public function test_inspection_policy_scopes_travel_visibility(): void
    {
        $policy = new InspectionPolicy();
        $travel = TravelCompany::first();

        $draftInspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0701',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $publishedInspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0702',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->assertFalse($policy->view($this->travelUser, $draftInspection));
        $this->assertTrue($policy->view($this->travelUser, $publishedInspection));
        $this->assertTrue($policy->create($this->admin));
        $this->assertFalse($policy->update($this->travelUser, $publishedInspection));
    }

    public function test_risk_policy_allows_admin_recalculate(): void
    {
        $policy = new RiskPolicy();
        $travel = TravelCompany::first();

        $riskScore = RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 40,
            'risk_level' => 'MEDIUM',
        ]);

        $this->assertTrue($policy->recalculate($this->admin));
        $this->assertFalse($policy->recalculate($this->kabupaten));
        $this->assertTrue($policy->view($this->travelUser, $riskScore));
    }

    public function test_followup_policy_allows_travel_upload(): void
    {
        $policy = new FollowupPolicy();
        $followup = new Followup();

        $this->assertTrue($policy->create($this->travelUser));
        $this->assertFalse($policy->create($this->admin));
        $this->assertTrue($policy->approve($this->admin, $followup));
    }

    public function test_audit_log_policy_limits_access_to_admin_and_kabupaten(): void
    {
        $policy = new AuditLogPolicy();
        $log = new AuditLog(['user_id' => $this->admin->id]);

        $this->assertTrue($policy->viewAny($this->admin));
        $this->assertTrue($policy->viewAny($this->kabupaten));
        $this->assertFalse($policy->viewAny($this->travelUser));
        $this->assertTrue($policy->view($this->admin, $log));
    }
}

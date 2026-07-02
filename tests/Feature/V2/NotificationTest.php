<?php

namespace Tests\Feature\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Models\ChecklistCategory;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\TravelCompany;
use App\Models\User;
use App\Notifications\V2\DeadlineReminderNotification;
use App\Notifications\V2\FollowupApprovedNotification;
use App\Notifications\V2\FollowupRevisionNotification;
use App\Notifications\V2\FollowupUploadedNotification;
use App\Notifications\V2\InspectionCreatedNotification;
use App\Services\FollowupService;
use App\Services\InspectionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    private User $admin;
    private User $travelUser;
    private TravelCompany $travel;

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
                'database/migrations/2026_07_02_100008_create_pengawasan_followups_table.php',
                'database/migrations/2026_07_02_100009_create_pengawasan_followup_logs_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
                'database/migrations/2026_07_02_100012_create_notifications_table.php',
            ],
        ]);

        $this->admin = User::first();
        \DB::table('users')->where('id', $this->admin->id)->update([
            'role' => 'admin',
            'is_password_changed' => true,
        ]);
        $this->admin->refresh();

        $this->travel = TravelCompany::first();
        $travelUserId = \DB::table('users')->insertGetId([
            'travel_id' => $this->travel->id,
            'username' => 'travel_user',
            'firstname' => 'Travel',
            'lastname' => 'User',
            'email' => 'travel@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_password_changed' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->travelUser = User::find($travelUserId);
    }

    public function test_inspection_created_notifies_travel_users(): void
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

        $this->actingAs($this->admin)->post(route('v2.pengawasan.store'), [
            'travel_id' => $this->travel->id,
            'inspection_no' => 'PWG-2026-2001',
            'inspection_date' => now()->format('Y-m-d'),
            'inspection_type' => InspectionType::Routine->value,
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->travelUser->id,
            'notifiable_type' => User::class,
            'type' => InspectionCreatedNotification::class,
        ]);
    }

    public function test_followup_upload_notifies_supervisors(): void
    {
        $finding = $this->createFindingReadyForFollowup();

        $this->actingAs($this->travelUser)->post(route('v2.followup.store'), [
            'finding_id' => $finding->id,
            'description' => 'Bukti perbaikan telah dilakukan sesuai rekomendasi.',
            'attachment' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
            'notifiable_type' => User::class,
            'type' => FollowupUploadedNotification::class,
        ]);
    }

    public function test_followup_revision_and_approve_notify_travel_users(): void
    {
        $finding = $this->createFindingReadyForFollowup();

        $followup = app(FollowupService::class)->submit($finding, [
            'description' => 'Bukti perbaikan telah dilakukan sesuai rekomendasi.',
            'attachment' => 'followups/test.pdf',
        ]);

        $this->actingAs($this->admin)->post(route('v2.followup.revision', $followup), [
            'remarks' => 'Mohon lengkapi dokumen pendukung tambahan.',
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->travelUser->id,
            'type' => FollowupRevisionNotification::class,
        ]);

        $followup->refresh()->update(['status' => FollowupStatus::Submitted]);

        $this->actingAs($this->admin)->post(route('v2.followup.approve', $followup), [
            'remarks' => 'Sudah sesuai.',
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->travelUser->id,
            'type' => FollowupApprovedNotification::class,
        ]);
    }

    public function test_notification_api_endpoints(): void
    {
        $this->travelUser->notify(new InspectionCreatedNotification(
            Inspection::create([
                'travel_id' => $this->travel->id,
                'inspection_no' => 'PWG-2026-2002',
                'inspection_date' => now(),
                'inspection_type' => InspectionType::Routine,
                'status' => InspectionStatus::Draft,
                'created_by' => $this->admin->id,
                'updated_by' => $this->admin->id,
            ])->load('travel')
        ));

        $list = $this->actingAs($this->travelUser)->getJson(route('v2.notifications.index'));
        $list->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.unread_count', 1);

        $notificationId = $list->json('data.notifications.0.id');

        $this->actingAs($this->travelUser)->postJson(route('v2.notifications.read'), [
            'id' => $notificationId,
        ])->assertOk();

        $this->assertNotNull(
            $this->travelUser->fresh()->notifications()->where('id', $notificationId)->value('read_at')
        );

        $this->actingAs($this->travelUser)->postJson(route('v2.notifications.read-all'))
            ->assertOk();
    }

    public function test_deadline_reminder_command_sends_notifications(): void
    {
        $finding = InspectionFinding::create([
            'inspection_id' => Inspection::create([
                'travel_id' => $this->travel->id,
                'inspection_no' => 'PWG-2026-2003',
                'inspection_date' => now(),
                'inspection_type' => InspectionType::Routine,
                'status' => InspectionStatus::WaitingFollowup,
                'created_by' => $this->admin->id,
                'updated_by' => $this->admin->id,
            ])->id,
            'category' => 'Legalitas',
            'severity' => FindingSeverity::Major,
            'title' => 'Izin kedaluwarsa',
            'description' => 'Perlu perpanjangan.',
            'recommendation' => 'Perpanjang izin.',
            'deadline' => now()->addDays(7)->toDateString(),
            'status' => FindingStatus::WaitingResponse,
        ]);

        Artisan::call('followup:send-deadline-reminders');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->travelUser->id,
            'type' => DeadlineReminderNotification::class,
        ]);

        $this->assertStringContainsString(
            (string) $finding->id,
            (string) $this->travelUser->notifications()->first()?->data['meta']['finding_id'] ?? ''
        );
    }

    public function test_deadline_reminder_is_scheduled_daily(): void
    {
        Artisan::call('schedule:list');
        $output = Artisan::output();

        $this->assertStringContainsString('followup:send-deadline-reminders', $output);
        $this->assertMatchesRegularExpression('/0\s+8\s+\*\s+\*\s+\*/', $output);
    }

    private function createFindingReadyForFollowup(): InspectionFinding
    {
        $inspection = Inspection::create([
            'travel_id' => $this->travel->id,
            'inspection_no' => 'PWG-2026-2100',
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

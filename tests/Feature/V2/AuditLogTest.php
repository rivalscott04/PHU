<?php

namespace Tests\Feature\V2;

use App\Models\AuditLog;
use App\Models\TravelCompany;
use App\Models\User;
use App\Support\AuditLogNarrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    private User $admin;
    private User $travelUser;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
            ],
        ]);

        $this->admin = User::first();
        \DB::table('users')->where('id', $this->admin->id)->update([
            'role' => 'admin',
            'is_password_changed' => true,
        ]);
        $this->admin->refresh();

        $travel = TravelCompany::first();
        $travelUserId = \DB::table('users')->insertGetId([
            'travel_id' => $travel->id,
            'username' => 'travel_user',
            'firstname' => 'Andi',
            'lastname' => 'Travel',
            'email' => 'travel@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_password_changed' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->travelUser = User::find($travelUserId);
    }

    public function test_admin_can_view_audit_log_page_with_plain_language(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'module' => 'followup',
            'action' => 'approve',
            'description' => 'menyetujui bukti tindak lanjut dari PT. Lombok Barat Travel untuk temuan "Dokumen tidak lengkap"',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('v2.audit-log.index'));

        $response->assertOk();
        $response->assertSee('Riwayat Aktivitas');
        $response->assertSee('Apa yang dilakukan');
        $response->assertSee('menyetujui bukti tindak lanjut');
        $response->assertSee('Tindak Lanjut');
    }

    public function test_audit_log_api_returns_narrative_summary(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'module' => 'pengawasan',
            'action' => 'create',
            'description' => 'menjadwalkan pengawasan baru PWG-2026-1001 untuk PT. Lombok Barat Travel',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('v2.audit-log.index'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.data.0.category', 'Pengawasan')
            ->assertJsonStructure(['data' => ['data' => [['summary', 'actor', 'actor_role']]]]);
    }

    public function test_travel_user_cannot_view_audit_log(): void
    {
        $this->actingAs($this->travelUser)
            ->get(route('v2.audit-log.index'))
            ->assertForbidden();
    }

    public function test_login_creates_plain_language_audit_entry(): void
    {
        $this->post(route('login.perform'), [
            'email_or_phone' => $this->admin->email,
            'password' => 'admin123',
        ])->assertRedirect();

        $log = AuditLog::where('module', 'auth')->where('action', 'login')->first();

        $this->assertNotNull($log);
        $this->assertSame('masuk ke sistem', $log->description);

        $summary = (new AuditLogNarrator())->present($log->load('user'))['summary'];
        $this->assertStringContainsString('masuk ke sistem', $summary);
    }
}

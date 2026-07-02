<?php

namespace Tests\Unit\V2;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditLogNarrator;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuditLogNarratorTest extends TestCase
{
    private AuditLogNarrator $narrator;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
            ],
        ]);

        $this->narrator = new AuditLogNarrator();
    }

    public function test_presents_human_readable_summary_for_pengawasan(): void
    {
        $admin = User::first();

        $log = AuditLog::create([
            'user_id' => $admin->id,
            'module' => 'pengawasan',
            'action' => 'create',
            'description' => 'menjadwalkan pengawasan baru PWG-2026-1001 untuk PT. Lombok Barat Travel',
            'created_at' => now(),
        ]);

        $log->load('user');
        $presented = $this->narrator->present($log);

        $this->assertStringContainsString('Super', $presented['actor']);
        $this->assertSame('Admin Kanwil', $presented['actor_role']);
        $this->assertSame('Pengawasan', $presented['category']);
        $this->assertStringContainsString('menjadwalkan pengawasan baru PWG-2026-1001', $presented['summary']);
        $this->assertStringNotContainsString('module', strtolower($presented['summary']));
        $this->assertStringNotContainsString('create', strtolower($presented['summary']));
    }

    public function test_presents_login_activity_in_plain_language(): void
    {
        $admin = User::first();

        $log = AuditLog::create([
            'user_id' => $admin->id,
            'module' => 'auth',
            'action' => 'login',
            'description' => 'masuk ke sistem',
            'created_at' => now(),
        ]);

        $log->load('user');
        $presented = $this->narrator->present($log);

        $this->assertSame('Akses Sistem', $presented['category']);
        $this->assertStringContainsString('masuk ke sistem', $presented['summary']);
    }
}

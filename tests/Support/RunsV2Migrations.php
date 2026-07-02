<?php

namespace Tests\Support;

use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

trait RunsV2Migrations
{
    /** @return array<int, string> */
    protected function v2MigrationPaths(array $extra = []): array
    {
        return array_merge([
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
            'database/migrations/2026_07_02_100013_add_v2_performance_indexes.php',
        ], $extra);
    }

    protected function runV2Migrations(array $extra = []): void
    {
        Artisan::call('migrate:fresh', [
            '--path' => $this->v2MigrationPaths($extra),
        ]);
    }

    protected function seedAdminUser(): User
    {
        $admin = User::first();
        \DB::table('users')->where('id', $admin->id)->update([
            'role' => 'admin',
            'is_password_changed' => true,
        ]);

        return $admin->fresh();
    }

    protected function seedTravelUser(?TravelCompany $travel = null): User
    {
        $travel ??= TravelCompany::first();
        $id = \DB::table('users')->insertGetId([
            'travel_id' => $travel->id,
            'username' => 'travel_'.uniqid(),
            'firstname' => 'Travel',
            'lastname' => 'User',
            'email' => 'travel_'.uniqid().'@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_password_changed' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::find($id);
    }

    protected function seedKabupatenUser(string $kabupaten = 'Lombok Barat'): User
    {
        $id = \DB::table('users')->insertGetId([
            'username' => 'kab_'.uniqid(),
            'firstname' => 'Admin',
            'lastname' => 'Kabupaten',
            'email' => 'kab_'.uniqid().'@test.com',
            'password' => Hash::make('password'),
            'role' => 'kabupaten',
            'kabupaten' => $kabupaten,
            'is_password_changed' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::find($id);
    }
}

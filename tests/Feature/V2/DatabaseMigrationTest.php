<?php

namespace Tests\Feature\V2;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    /** @var array<int, string> */
    private array $prerequisitePaths = [
        'database/migrations/2025_08_11_000001_create_admin_users_table.php',
        'database/migrations/2025_08_11_000002_create_travels_table.php',
    ];

    /** @var array<int, string> */
    private array $v2Paths = [
        'database/migrations/2026_07_02_100001_create_master_checklist_categories_table.php',
        'database/migrations/2026_07_02_100002_create_master_checklists_table.php',
        'database/migrations/2026_07_02_100003_create_master_checklist_options_table.php',
        'database/migrations/2026_07_02_100004_create_pengawasan_table.php',
        'database/migrations/2026_07_02_100005_create_pengawasan_checklists_table.php',
        'database/migrations/2026_07_02_100006_create_pengawasan_temuan_table.php',
        'database/migrations/2026_07_02_100007_create_pengawasan_photos_table.php',
        'database/migrations/2026_07_02_100008_create_pengawasan_followups_table.php',
        'database/migrations/2026_07_02_100009_create_pengawasan_followup_logs_table.php',
        'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
        'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
        'database/migrations/2026_07_02_100012_create_notifications_table.php',
    ];

    /** @var array<int, string> */
    private array $v2Tables = [
        'master_checklist_categories',
        'master_checklists',
        'master_checklist_options',
        'pengawasan',
        'pengawasan_checklists',
        'pengawasan_temuan',
        'pengawasan_photos',
        'pengawasan_followups',
        'pengawasan_followup_logs',
        'risk_scores',
        'audit_logs',
        'notifications',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => array_merge($this->prerequisitePaths, $this->v2Paths),
        ]);
    }

    public function test_v2_tables_exist_after_migration(): void
    {
        foreach ($this->v2Tables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table [{$table}] should exist after migration."
            );
        }
    }

    public function test_v2_foreign_key_chain_accepts_valid_data(): void
    {
        $travelId = \DB::table('travels')->value('id');
        $userId = \DB::table('users')->value('id');

        $this->assertNotNull($travelId);
        $this->assertNotNull($userId);

        $categoryId = \DB::table('master_checklist_categories')->insertGetId([
            'name' => 'Legalitas',
            'description' => 'Kategori legalitas',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $checklistId = \DB::table('master_checklists')->insertGetId([
            'category_id' => $categoryId,
            'code' => 'LEG-001',
            'title' => 'Izin Operasional',
            'description' => null,
            'input_type' => 'BOOLEAN',
            'weight' => 10,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $inspectionId = \DB::table('pengawasan')->insertGetId([
            'travel_id' => $travelId,
            'inspection_no' => 'PWG-2026-0001',
            'inspection_date' => '2026-07-02',
            'inspection_type' => 'ROUTINE',
            'overall_score' => null,
            'status' => 'DRAFT',
            'notes' => null,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('pengawasan_checklists')->insert([
            'inspection_id' => $inspectionId,
            'master_checklist_id' => $checklistId,
            'answer' => 'Ya',
            'score' => 100,
            'note' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $findingId = \DB::table('pengawasan_temuan')->insertGetId([
            'inspection_id' => $inspectionId,
            'category' => 'Legalitas',
            'severity' => 'MAJOR',
            'title' => 'Izin hampir kadaluarsa',
            'description' => 'Izin operasional akan habis dalam 30 hari.',
            'recommendation' => 'Perpanjang izin operasional segera.',
            'deadline' => '2026-08-02',
            'status' => 'OPEN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $followupId = \DB::table('pengawasan_followups')->insertGetId([
            'finding_id' => $findingId,
            'description' => 'Bukti perpanjangan izin diunggah.',
            'attachment' => 'followups/bukti.pdf',
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
            'verified_by' => null,
            'verified_at' => null,
            'remarks' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('pengawasan_followup_logs')->insert([
            'followup_id' => $followupId,
            'status' => 'SUBMITTED',
            'description' => 'Travel mengunggah bukti tindak lanjut.',
            'created_by' => $userId,
            'created_at' => now(),
        ]);

        \DB::table('risk_scores')->insert([
            'travel_id' => $travelId,
            'complaint_score' => 10,
            'inspection_score' => 20,
            'followup_score' => 5,
            'certificate_score' => 0,
            'bap_score' => 0,
            'activity_score' => 5,
            'total_score' => 40,
            'risk_level' => 'MEDIUM',
            'last_calculated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('audit_logs')->insert([
            'user_id' => $userId,
            'module' => 'pengawasan',
            'action' => 'create',
            'description' => 'Pengawasan baru dibuat.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'created_at' => now(),
        ]);

        $this->assertDatabaseCount('pengawasan', 1);
        $this->assertDatabaseCount('pengawasan_temuan', 1);
        $this->assertDatabaseCount('pengawasan_followups', 1);
        $this->assertDatabaseCount('risk_scores', 1);
    }

    public function test_v2_migrations_can_rollback(): void
    {
        foreach (array_reverse($this->v2Paths) as $path) {
            Artisan::call('migrate:rollback', [
                '--path' => $path,
            ]);
        }

        foreach ($this->v2Tables as $table) {
            $this->assertFalse(
                Schema::hasTable($table),
                "Table [{$table}] should be dropped after rollback."
            );
        }

        $this->assertTrue(Schema::hasTable('travels'));
        $this->assertTrue(Schema::hasTable('users'));
    }
}

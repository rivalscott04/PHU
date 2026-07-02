<?php

namespace Tests\Unit\V2;

use App\Models\AuditLog;
use App\Models\User;
use App\Repositories\AuditLogRepository;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class AuditLogRepositoryTest extends TestCase
{
    use RunsV2Migrations;

    private AuditLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->repository = new AuditLogRepository();
    }

    public function test_paginate_filters_by_module_and_search(): void
    {
        $admin = $this->seedAdminUser();

        AuditLog::create([
            'user_id' => $admin->id,
            'module' => 'pengawasan',
            'action' => 'create',
            'description' => 'menjadwalkan pengawasan baru PWG-2026-1001',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'module' => 'export',
            'action' => 'export',
            'description' => 'mengekspor daftar travel ke Excel',
            'created_at' => now(),
        ]);

        $filtered = $this->repository->paginate(['module' => 'export']);
        $this->assertSame(1, $filtered->total());

        $searched = $this->repository->paginate(['q' => 'pengawasan baru']);
        $this->assertSame(1, $searched->total());
        $this->assertStringContainsString('PWG-2026-1001', $searched->items()[0]->description);
    }

    public function test_paginate_scopes_kabupaten_logs(): void
    {
        $admin = $this->seedAdminUser();
        $kabUser = $this->seedKabupatenUser('Lombok Barat');
        $otherKab = $this->seedKabupatenUser('Lombok Tengah');

        AuditLog::create([
            'user_id' => $kabUser->id,
            'module' => 'followup',
            'action' => 'approve',
            'description' => 'menyetujui bukti tindak lanjut',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $otherKab->id,
            'module' => 'followup',
            'action' => 'approve',
            'description' => 'menyetujui bukti tindak lanjut wilayah lain',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'module' => 'pengawasan',
            'action' => 'create',
            'description' => 'menjadwalkan pengawasan',
            'created_at' => now(),
        ]);

        $scoped = $this->repository->paginate(['kabupaten' => 'Lombok Barat']);

        $this->assertSame(1, $scoped->total());
        $this->assertSame($kabUser->id, $scoped->items()[0]->user_id);
    }
}

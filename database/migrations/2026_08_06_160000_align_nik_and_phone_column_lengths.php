<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jamaah')) {
            DB::statement('ALTER TABLE jamaah MODIFY nik VARCHAR(16) NOT NULL');
            DB::statement('ALTER TABLE jamaah MODIFY nomor_hp VARCHAR(16) NOT NULL');
        }

        if (Schema::hasTable('jamaah_haji_khusus')) {
            DB::statement('ALTER TABLE jamaah_haji_khusus MODIFY no_ktp VARCHAR(16) NOT NULL');
            DB::statement('ALTER TABLE jamaah_haji_khusus MODIFY no_hp VARCHAR(16) NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jamaah')) {
            DB::statement('ALTER TABLE jamaah MODIFY nik VARCHAR(16) NOT NULL');
            DB::statement('ALTER TABLE jamaah MODIFY nomor_hp VARCHAR(13) NOT NULL');
        }

        if (Schema::hasTable('jamaah_haji_khusus')) {
            DB::statement('ALTER TABLE jamaah_haji_khusus MODIFY no_ktp VARCHAR(16) NOT NULL');
            DB::statement('ALTER TABLE jamaah_haji_khusus MODIFY no_hp VARCHAR(15) NOT NULL');
        }
    }
};

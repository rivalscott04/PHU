<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data operasional pusat dan cabang diisolasi penuh. Sebelumnya pemiliknya
     * hanya ditandai travel_id, sehingga begitu cabang ikut beroperasi, satu
     * travel_id dipakai bersama oleh pusat dan seluruh cabangnya: pusat melihat
     * data cabang, dan yang lebih buruk, antar cabang saling melihat.
     *
     * travel_id tetap ada dan tetap menunjuk travel pemegang izin PPIU, karena
     * jenis jamaah yang boleh dikelola ditentukan dari izin itu. cabang_id yang
     * menentukan siapa pemilik barisnya. NULL berarti milik kantor pusat.
     */
    public function up(): void
    {
        foreach (['jamaah', 'jamaah_haji_khusus', 'travel_packages'] as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'cabang_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->unsignedBigInteger('cabang_id')->nullable()->after('travel_id');
                $blueprint->index(['travel_id', 'cabang_id'], "{$table}_pemilik_index");
            });
        }

        // BA tidak punya travel_id, identitas PPIU-nya disimpan sebagai teks
        // ppiuname. BA milik cabang memakai nama PPIU pusat, karena izinnya
        // memang milik pusat, sehingga menyaring dengan ppiuname saja membuat
        // pusat ikut melihat keberangkatan cabangnya.
        if (Schema::hasTable('bap') && ! Schema::hasColumn('bap', 'cabang_id')) {
            Schema::table('bap', function (Blueprint $blueprint) {
                $blueprint->unsignedBigInteger('cabang_id')->nullable()->after('user_id');
                $blueprint->index('cabang_id', 'bap_pemilik_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bap') && Schema::hasColumn('bap', 'cabang_id')) {
            Schema::table('bap', function (Blueprint $blueprint) {
                $blueprint->dropIndex('bap_pemilik_index');
                $blueprint->dropColumn('cabang_id');
            });
        }

        foreach (['jamaah', 'jamaah_haji_khusus', 'travel_packages'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'cabang_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropIndex("{$table}_pemilik_index");
                $blueprint->dropColumn('cabang_id');
            });
        }
    }
};

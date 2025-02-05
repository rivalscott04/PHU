<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add all new columns except alamat_cabang
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->string('kabupaten')->after('travel_id');
            $table->string('pusat')->after('kabupaten');
            $table->string('pimpinan_pusat')->after('pusat');
            $table->string('alamat_pusat')->after('pimpinan_pusat');
        });

        // Step 2: Handle alamat to alamat_cabang conversion
        if (!Schema::hasColumn('travel_cabang', 'alamat_cabang')) {
            Schema::table('travel_cabang', function (Blueprint $table) {
                $table->string('alamat_cabang')->after('alamat');
            });

            // Now we can safely copy the data
            DB::statement('UPDATE travel_cabang SET alamat_cabang = alamat');

            Schema::table('travel_cabang', function (Blueprint $table) {
                $table->dropColumn('alamat');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add back the original alamat column
        if (!Schema::hasColumn('travel_cabang', 'alamat')) {
            Schema::table('travel_cabang', function (Blueprint $table) {
                $table->string('alamat')->after('SK_BA');
            });

            // Copy data back
            DB::statement('UPDATE travel_cabang SET alamat = alamat_cabang');
        }

        // Step 2: Remove all added columns
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropColumn([
                'kabupaten',
                'pusat',
                'pimpinan_pusat',
                'alamat_pusat',
                'alamat_cabang'
            ]);
        });
    }
};

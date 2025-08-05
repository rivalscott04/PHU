<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->string('kecamatan')->nullable()->after('kota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->dropColumn('kecamatan');
        });
    }
};

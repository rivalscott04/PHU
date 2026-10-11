<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            // Nomor berita acara pembukaan cabang yang diterbitkan Kanwil. Beda
            // dengan SK_BA, yang diisi pendaftar dari surat milik cabang sendiri.
            $table->string('nomor_berita_acara')->nullable()->unique()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropUnique(['nomor_berita_acara']);
            $table->dropColumn('nomor_berita_acara');
        });
    }
};

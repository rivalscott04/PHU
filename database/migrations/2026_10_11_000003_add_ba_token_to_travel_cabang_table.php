<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            // Token acak untuk QR tanda tangan berita acara; dibuka publik di halaman verifikasi.
            $table->string('ba_token', 40)->nullable()->unique()->after('nomor_berita_acara');
        });
    }

    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropUnique(['ba_token']);
            $table->dropColumn('ba_token');
        });
    }
};

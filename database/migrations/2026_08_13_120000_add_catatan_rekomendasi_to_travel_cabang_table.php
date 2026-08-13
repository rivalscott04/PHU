<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            // Catatan peninjauan Kabupaten/Kota adalah bagian dari rekomendasi dan
            // harus bertahan. registration_notes dipakai untuk alasan penolakan dan
            // dikosongkan saat disetujui, jadi tidak boleh dipakai bersama.
            $table->text('catatan_rekomendasi')->nullable()->after('dokumen_rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropColumn('catatan_rekomendasi');
        });
    }
};

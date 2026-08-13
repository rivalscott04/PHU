<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sertifikat yang sudah terbit tidak dihapus permanen, hanya ditandai
     * batal. Dua alasan: nomornya sudah beredar dan tidak boleh dipakai ulang
     * dokumen lain, dan registri dokumen resmi perlu jejak apa yang pernah
     * diterbitkan sekalipun kemudian dibatalkan.
     */
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

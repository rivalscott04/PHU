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
            // Add new fields
            $table->enum('pergi_haji', ['Belum', 'Sudah'])->nullable()->after('status_pernikahan');
            $table->enum('status_kawin', ['Belum Menikah', 'Menikah', 'Cerai'])->nullable()->after('pergi_haji');
            
            // Add surat keterangan field
            $table->string('surat_keterangan')->nullable()->after('dokumen_foto');
            
            // Remove medical-related fields
            $table->dropColumn(['riwayat_penyakit', 'dokumen_medical_check']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn(['pergi_haji', 'status_kawin', 'surat_keterangan']);
            
            // Add back medical-related fields
            $table->string('riwayat_penyakit')->nullable()->after('golongan_darah');
            $table->string('dokumen_medical_check')->nullable()->after('dokumen_foto');
        });
    }
};

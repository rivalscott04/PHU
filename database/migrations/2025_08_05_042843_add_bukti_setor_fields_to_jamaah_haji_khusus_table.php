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
            // Bukti setor bank fields
            $table->string('bukti_setor_bank')->nullable()->after('dokumen_medical_check');
            $table->enum('status_verifikasi_bukti', ['pending', 'verified', 'rejected'])->default('pending')->after('bukti_setor_bank');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi_bukti');
            $table->timestamp('tanggal_verifikasi')->nullable()->after('catatan_verifikasi');
            $table->unsignedBigInteger('verified_by')->nullable()->after('tanggal_verifikasi');
            
            // Add foreign key for verified_by
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'bukti_setor_bank',
                'status_verifikasi_bukti',
                'catatan_verifikasi',
                'tanggal_verifikasi',
                'verified_by'
            ]);
        });
    }
};

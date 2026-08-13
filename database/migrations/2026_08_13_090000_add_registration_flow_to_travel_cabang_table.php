<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            // Pusat dipilih dari daftar travel yang sudah disetujui, bukan diketik
            // bebas. Dari sini SK pusat ikut terbaca tanpa upload ulang.
            $table->foreignId('travel_id')->nullable()->after('id_cabang')->constrained('travels')->nullOnDelete();

            // Data cabang lama sudah dianggap sah, sama seperti default di travels.
            $table->string('registration_status', 20)->default('approved')->after('kabupaten');
            $table->text('registration_notes')->nullable()->after('registration_status');

            $table->string('dokumen_oss')->nullable()->after('registration_notes');
            $table->string('dokumen_akta')->nullable()->after('dokumen_oss');
            $table->string('dokumen_ktp_kepala')->nullable()->after('dokumen_akta');
            $table->string('dokumen_sk_du')->nullable()->after('dokumen_ktp_kepala');

            // Rekomendasi / BA laporan peninjauan yang diunggah Kabupaten/Kota.
            $table->string('dokumen_rekomendasi')->nullable()->after('dokumen_sk_du');
            $table->timestamp('recommended_at')->nullable()->after('dokumen_rekomendasi');
            $table->foreignId('recommended_by')->nullable()->after('recommended_at')->constrained('users')->nullOnDelete();

            $table->timestamp('verified_at')->nullable()->after('recommended_by');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();

            $table->index('registration_status');
        });
    }

    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropIndex(['registration_status']);
            $table->dropConstrainedForeignId('travel_id');
            $table->dropConstrainedForeignId('recommended_by');
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn([
                'registration_status',
                'registration_notes',
                'dokumen_oss',
                'dokumen_akta',
                'dokumen_ktp_kepala',
                'dokumen_sk_du',
                'dokumen_rekomendasi',
                'recommended_at',
                'verified_at',
            ]);
        });
    }
};

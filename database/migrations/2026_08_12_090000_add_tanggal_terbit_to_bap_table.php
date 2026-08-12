<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Narasi "Pada hari ini ..." pada Berita Acara sebelumnya dihitung dari
     * tanggal saat dicetak, sehingga dokumen yang sama berbunyi berbeda setiap
     * kali dicetak ulang. Tanggal terbitnya sekarang dibekukan saat BA
     * disetujui, sejalan dengan bulan/tahun pada nomor suratnya.
     */
    public function up(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->date('tanggal_terbit')->nullable()->after('nomor_surat');
        });

        // BA lama tidak menyimpan kapan disetujui. updated_at adalah perkiraan
        // terdekat yang tersedia, dan hanya dipakai untuk BA yang sudah bernomor.
        DB::table('bap')
            ->whereNotNull('nomor_surat')
            ->whereNull('tanggal_terbit')
            ->update(['tanggal_terbit' => DB::raw('DATE(updated_at)')]);
    }

    public function down(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->dropColumn('tanggal_terbit');
        });
    }
};

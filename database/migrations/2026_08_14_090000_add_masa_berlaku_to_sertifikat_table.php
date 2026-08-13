<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sertifikat PPIU berlaku sampai 1 Januari berikutnya, berapa pun bulan
     * terbitnya. Terbit Juni 2026 maupun Desember 2026 sama sama berakhir
     * 1 Januari 2027.
     */
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->date('tanggal_kadaluarsa')->nullable()->after('tanggal_diterbitkan');
            $table->timestamp('reminder_kadaluarsa_at')->nullable()->after('tanggal_kadaluarsa');
            $table->index('tanggal_kadaluarsa');
        });

        // Isi data lama dari tanggal terbitnya masing masing.
        DB::table('sertifikat')
            ->whereNotNull('tanggal_diterbitkan')
            ->update([
                'tanggal_kadaluarsa' => DB::raw("DATE_FORMAT(DATE_ADD(tanggal_diterbitkan, INTERVAL 1 YEAR), '%Y-01-01')"),
            ]);
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropIndex(['tanggal_kadaluarsa']);
            $table->dropColumn(['tanggal_kadaluarsa', 'reminder_kadaluarsa_at']);
        });
    }
};

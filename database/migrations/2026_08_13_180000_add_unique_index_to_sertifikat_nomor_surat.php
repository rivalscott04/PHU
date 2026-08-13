<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor surat sertifikat dikirim dari formulir dan tidak pernah diperiksa
     * keunikannya, sehingga dua sertifikat resmi bisa terbit dengan nomor sama.
     * Nomornya sudah memuat bulan dan tahun, jadi aman dijadikan unik global.
     */
    public function up(): void
    {
        $this->laporkanKembar();

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->unique('nomor_surat');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropUnique(['nomor_surat']);
        });
    }

    private function laporkanKembar(): void
    {
        $kembar = DB::table('sertifikat')
            ->select('nomor_surat', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('nomor_surat')
            ->groupBy('nomor_surat')
            ->having('jumlah', '>', 1)
            ->get();

        if ($kembar->isEmpty()) {
            return;
        }

        $daftar = $kembar
            ->map(fn ($row) => "{$row->nomor_surat} ({$row->jumlah} dokumen)")
            ->implode(', ');

        throw new RuntimeException(
            'Ada nomor surat sertifikat kembar di data lama, perbaiki dulu sebelum migrasi ini dijalankan: ' . $daftar
        );
    }
};

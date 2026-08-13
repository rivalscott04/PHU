<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor surat BA dibuat dengan pola baca lalu tulis tanpa penguncian, dan
     * kolomnya tidak pernah punya indeks unik. Dua persetujuan yang berjalan
     * bersamaan bisa menerbitkan nomor yang sama pada dua dokumen resmi.
     *
     * Indeks unik ini menjadi jaring pengaman terakhir: kalaupun logika aplikasi
     * bocor lagi suatu saat, database yang menolak.
     */
    public function up(): void
    {
        $this->reportDuplicates();

        Schema::table('bap', function (Blueprint $table) {
            $table->unique('nomor_surat');
        });
    }

    public function down(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->dropUnique(['nomor_surat']);
        });
    }

    /**
     * Migrasi akan gagal kalau data lama sudah terlanjur punya nomor kembar.
     * Cetak daftarnya supaya operator tahu persis mana yang harus dibetulkan,
     * bukan sekadar melihat pesan constraint dari MySQL.
     */
    private function reportDuplicates(): void
    {
        $duplicates = DB::table('bap')
            ->select('nomor_surat', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('nomor_surat')
            ->groupBy('nomor_surat')
            ->having('jumlah', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        $daftar = $duplicates
            ->map(fn ($row) => "{$row->nomor_surat} ({$row->jumlah} dokumen)")
            ->implode(', ');

        throw new RuntimeException(
            'Ada nomor surat BA kembar di data lama, perbaiki dulu sebelum migrasi ini dijalankan: ' . $daftar
        );
    }
};

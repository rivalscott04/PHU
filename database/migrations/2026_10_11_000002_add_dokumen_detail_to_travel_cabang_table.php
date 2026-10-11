<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Nomor dan tanggal dari setiap berkas pendaftaran, dipakai di berita acara cabang. */
    private const KOLOM = [
        'nib' => 'string', 'tanggal_nib' => 'date',
        'notaris_akta' => 'string', 'nomor_akta' => 'string', 'tanggal_akta' => 'date',
        'nik_kepala' => 'string', 'npwp_kepala' => 'string', 'dokumen_npwp_kepala' => 'string',
        'nomor_skdu' => 'string', 'tanggal_skdu' => 'date', 'penerbit_skdu' => 'string',
    ];

    public function up(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            foreach (self::KOLOM as $kolom => $tipe) {
                $table->{$tipe}($kolom)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('travel_cabang', fn (Blueprint $table) => $table->dropColumn(array_keys(self::KOLOM)));
    }
};

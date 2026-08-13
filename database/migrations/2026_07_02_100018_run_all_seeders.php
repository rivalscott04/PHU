<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Dulu migrasi ini memanggil DatabaseSeeder di tengah rangkaian migrasi.
     * Itu berbahaya: DatabaseSeeder terus bertambah isinya, sementara migrasi
     * ini berhenti di titik waktu tertentu. Begitu BapAirlineSeeder masuk ke
     * DatabaseSeeder, instalasi baru langsung gagal, karena tabel bap_airlines
     * baru dibuat oleh migrasi yang jauh lebih belakang.
     *
     * Sekarang dikosongkan. Seeding dijalankan terpisah setelah semua migrasi
     * selesai, lewat `php artisan db:seed` atau `php artisan migrate --seed`.
     * Lingkungan lama tidak terpengaruh karena migrasi ini sudah tercatat jalan.
     */
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};

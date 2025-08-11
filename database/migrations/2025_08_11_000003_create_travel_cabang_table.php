<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_cabang', function (Blueprint $table) {
            $table->id('id_cabang');
            $table->string('Penyelenggara');
            $table->string('SK_BA')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('pimpinan_cabang');
            $table->text('alamat_cabang');
            $table->string('telepon');
            $table->string('kabupaten');
            $table->string('pusat')->nullable();
            $table->string('pimpinan_pusat');
            $table->text('alamat_pusat');
            $table->timestamps();
        });

        // Create travel cabang using firstOrCreate
        $travelCabang = [
            [
                'Penyelenggara' => 'PT. Lombok Barat Travel',
                'SK_BA' => 'SK.001/LB/2024',
                'tanggal' => '2024-01-15',
                'pimpinan_cabang' => 'Ahmad Suryadi',
                'alamat_cabang' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'telepon' => '0370-123456',
                'kabupaten' => 'Lombok Barat',
                'pusat' => 'Gerung',
                'pimpinan_pusat' => 'Ahmad Suryadi',
                'alamat_pusat' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
            ],
            [
                'Penyelenggara' => 'PT. Lombok Tengah Travel',
                'SK_BA' => 'SK.002/LT/2024',
                'tanggal' => '2024-01-20',
                'pimpinan_cabang' => 'Budi Santoso',
                'alamat_cabang' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'telepon' => '0370-234567',
                'kabupaten' => 'Lombok Tengah',
                'pusat' => 'Praya',
                'pimpinan_pusat' => 'Budi Santoso',
                'alamat_pusat' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
            ],
            [
                'Penyelenggara' => 'PT. Lombok Timur Travel',
                'SK_BA' => 'SK.003/LT/2024',
                'tanggal' => '2024-01-25',
                'pimpinan_cabang' => 'Siti Nurhaliza',
                'alamat_cabang' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'telepon' => '0370-345678',
                'kabupaten' => 'Lombok Timur',
                'pusat' => 'Selong',
                'pimpinan_pusat' => 'Siti Nurhaliza',
                'alamat_pusat' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Travel',
                'SK_BA' => 'SK.004/SB/2024',
                'tanggal' => '2024-02-01',
                'pimpinan_cabang' => 'Rudi Hartono',
                'alamat_cabang' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'telepon' => '0371-456789',
                'kabupaten' => 'Sumbawa',
                'pusat' => 'Sumbawa Besar',
                'pimpinan_pusat' => 'Rudi Hartono',
                'alamat_pusat' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Barat Travel',
                'SK_BA' => 'SK.005/SB/2024',
                'tanggal' => '2024-02-05',
                'pimpinan_cabang' => 'Dewi Sartika',
                'alamat_cabang' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'telepon' => '0371-567890',
                'kabupaten' => 'Sumbawa Barat',
                'pusat' => 'Taliwang',
                'pimpinan_pusat' => 'Dewi Sartika',
                'alamat_pusat' => 'Jl. Raya Taliwang No. 654, Taliwang',
            ],
            [
                'Penyelenggara' => 'PT. Dompu Travel',
                'SK_BA' => 'SK.006/DP/2024',
                'tanggal' => '2024-02-10',
                'pimpinan_cabang' => 'Muhammad Rizki',
                'alamat_cabang' => 'Jl. Raya Dompu No. 987, Dompu',
                'telepon' => '0371-678901',
                'kabupaten' => 'Dompu',
                'pusat' => 'Dompu',
                'pimpinan_pusat' => 'Muhammad Rizki',
                'alamat_pusat' => 'Jl. Raya Dompu No. 987, Dompu',
            ],
            [
                'Penyelenggara' => 'PT. Bima Travel',
                'SK_BA' => 'SK.007/BM/2024',
                'tanggal' => '2024-02-15',
                'pimpinan_cabang' => 'Ahmad Fauzi',
                'alamat_cabang' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'telepon' => '0371-789012',
                'kabupaten' => 'Bima',
                'pusat' => 'Woha',
                'pimpinan_pusat' => 'Ahmad Fauzi',
                'alamat_pusat' => 'Jl. Raya Woha No. 147, Woha, Bima',
            ],
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'SK_BA' => 'SK.008/MT/2024',
                'tanggal' => '2024-02-20',
                'pimpinan_cabang' => 'Lina Marlina',
                'alamat_cabang' => 'Jl. Pejanggik No. 258, Mataram',
                'telepon' => '0370-890123',
                'kabupaten' => 'Kota Mataram',
                'pusat' => 'Mataram',
                'pimpinan_pusat' => 'Lina Marlina',
                'alamat_pusat' => 'Jl. Pejanggik No. 258, Mataram',
            ],
            [
                'Penyelenggara' => 'PT. Kota Bima Travel',
                'SK_BA' => 'SK.009/KB/2024',
                'tanggal' => '2024-02-25',
                'pimpinan_cabang' => 'Nurul Hidayati',
                'alamat_cabang' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'telepon' => '0371-901234',
                'kabupaten' => 'Kota Bima',
                'pusat' => 'Bima',
                'pimpinan_pusat' => 'Nurul Hidayati',
                'alamat_pusat' => 'Jl. Soekarno-Hatta No. 369, Bima',
            ],
        ];

        foreach ($travelCabang as $cabang) {
            DB::table('travel_cabang')->updateOrInsert(
                ['Penyelenggara' => $cabang['Penyelenggara']],
                $cabang
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_cabang');
    }
};

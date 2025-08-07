<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CabangTravel;
use Carbon\Carbon;

class CabangTravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cabang travel for NTB kabupaten/kota
        $cabangTravels = [
            [
                'Penyelenggara' => 'PT. Lombok Barat Travel',
                'kabupaten' => 'Lombok Barat',
                'pusat' => 'Gerung',
                'pimpinan_pusat' => 'Ahmad Suryadi',
                'alamat_pusat' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'SK_BA' => 'SK.001/LB/2024',
                'tanggal' => '2024-01-15',
                'pimpinan_cabang' => 'Ahmad Suryadi',
                'alamat_cabang' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'telepon' => '0370-123456',
            ],
            [
                'Penyelenggara' => 'PT. Lombok Tengah Travel',
                'kabupaten' => 'Lombok Tengah',
                'pusat' => 'Praya',
                'pimpinan_pusat' => 'Budi Santoso',
                'alamat_pusat' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'SK_BA' => 'SK.002/LT/2024',
                'tanggal' => '2024-01-20',
                'pimpinan_cabang' => 'Budi Santoso',
                'alamat_cabang' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'telepon' => '0370-234567',
            ],
            [
                'Penyelenggara' => 'PT. Lombok Timur Travel',
                'kabupaten' => 'Lombok Timur',
                'pusat' => 'Selong',
                'pimpinan_pusat' => 'Siti Nurhaliza',
                'alamat_pusat' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'SK_BA' => 'SK.003/LT/2024',
                'tanggal' => '2024-01-25',
                'pimpinan_cabang' => 'Siti Nurhaliza',
                'alamat_cabang' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'telepon' => '0370-345678',
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Travel',
                'kabupaten' => 'Sumbawa',
                'pusat' => 'Sumbawa Besar',
                'pimpinan_pusat' => 'Rudi Hartono',
                'alamat_pusat' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'SK_BA' => 'SK.004/SB/2024',
                'tanggal' => '2024-02-01',
                'pimpinan_cabang' => 'Rudi Hartono',
                'alamat_cabang' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'telepon' => '0371-456789',
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Barat Travel',
                'kabupaten' => 'Sumbawa Barat',
                'pusat' => 'Taliwang',
                'pimpinan_pusat' => 'Dewi Sartika',
                'alamat_pusat' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'SK_BA' => 'SK.005/SB/2024',
                'tanggal' => '2024-02-05',
                'pimpinan_cabang' => 'Dewi Sartika',
                'alamat_cabang' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'telepon' => '0371-567890',
            ],
            [
                'Penyelenggara' => 'PT. Dompu Travel',
                'kabupaten' => 'Dompu',
                'pusat' => 'Dompu',
                'pimpinan_pusat' => 'Muhammad Rizki',
                'alamat_pusat' => 'Jl. Raya Dompu No. 987, Dompu',
                'SK_BA' => 'SK.006/DP/2024',
                'tanggal' => '2024-02-10',
                'pimpinan_cabang' => 'Muhammad Rizki',
                'alamat_cabang' => 'Jl. Raya Dompu No. 987, Dompu',
                'telepon' => '0371-678901',
            ],
            [
                'Penyelenggara' => 'PT. Bima Travel',
                'kabupaten' => 'Bima',
                'pusat' => 'Woha',
                'pimpinan_pusat' => 'Ahmad Fauzi',
                'alamat_pusat' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'SK_BA' => 'SK.007/BM/2024',
                'tanggal' => '2024-02-15',
                'pimpinan_cabang' => 'Ahmad Fauzi',
                'alamat_cabang' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'telepon' => '0371-789012',
            ],
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'kabupaten' => 'Kota Mataram',
                'pusat' => 'Mataram',
                'pimpinan_pusat' => 'Lina Marlina',
                'alamat_pusat' => 'Jl. Pejanggik No. 258, Mataram',
                'SK_BA' => 'SK.008/MT/2024',
                'tanggal' => '2024-02-20',
                'pimpinan_cabang' => 'Lina Marlina',
                'alamat_cabang' => 'Jl. Pejanggik No. 258, Mataram',
                'telepon' => '0370-890123',
            ],
            [
                'Penyelenggara' => 'PT. Kota Bima Travel',
                'kabupaten' => 'Kota Bima',
                'pusat' => 'Bima',
                'pimpinan_pusat' => 'Nurul Hidayati',
                'alamat_pusat' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'SK_BA' => 'SK.009/KB/2024',
                'tanggal' => '2024-02-25',
                'pimpinan_cabang' => 'Nurul Hidayati',
                'alamat_cabang' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'telepon' => '0371-901234',
            ],
        ];

        foreach ($cabangTravels as $cabangData) {
            CabangTravel::create($cabangData);
        }

        $this->command->info('NTB Cabang Travel seeded successfully!');
        $this->command->info('Total cabang travel created: ' . count($cabangTravels));
        $this->command->info('Cabang travel correspond to NTB kabupaten/kota:');
        $this->command->info('- Lombok Barat');
        $this->command->info('- Lombok Tengah');
        $this->command->info('- Lombok Timur');
        $this->command->info('- Sumbawa');
        $this->command->info('- Sumbawa Barat');
        $this->command->info('- Dompu');
        $this->command->info('- Bima');
        $this->command->info('- Kota Mataram');
        $this->command->info('- Kota Bima');
    }
}

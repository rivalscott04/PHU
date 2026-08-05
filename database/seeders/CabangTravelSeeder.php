<?php

namespace Database\Seeders;

use App\Models\CabangTravel;
use Illuminate\Database\Seeder;

class CabangTravelSeeder extends Seeder
{
    public function run(): void
    {
        CabangTravel::updateOrCreate(
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'kabupaten' => 'Kota Mataram',
            ],
            [
                'pusat' => 'Mataram',
                'pimpinan_pusat' => 'Lina Marlina',
                'alamat_pusat' => 'Jl. Pejanggik No. 258, Mataram',
                'SK_BA' => 'SK.008/MT/2024',
                'tanggal' => '2024-02-20',
                'pimpinan_cabang' => 'Lina Marlina',
                'alamat_cabang' => 'Jl. Pejanggik No. 258, Mataram',
                'telepon' => '0370-890123',
            ]
        );

        $this->command->info('Cabang travel seeded: PT. Mataram Travel (Kota Mataram)');
    }
}

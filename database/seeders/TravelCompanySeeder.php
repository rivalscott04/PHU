<?php

namespace Database\Seeders;

use App\Models\TravelCompany;
use Illuminate\Database\Seeder;

class TravelCompanySeeder extends Seeder
{
    public function run(): void
    {
        $travelCompany = TravelCompany::updateOrCreate(
            ['Penyelenggara' => 'PT. Mataram Travel'],
            [
                'Pusat' => 'Mataram',
                'Tanggal' => '2024-02-20',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-20',
                'lembaga_akreditasi' => 'Kementerian Haji dan Umroh',
                'Pimpinan' => 'Lina Marlina',
                'alamat_kantor_lama' => 'Jl. Pejanggik No. 258, Mataram',
                'alamat_kantor_baru' => 'Jl. Pejanggik No. 258, Mataram',
                'Telepon' => '0370-890123',
                'Status' => 'PIHK',
                'kab_kota' => 'Kota Mataram',
                'can_haji' => true,
                'can_umrah' => true,
                'capabilities' => ['haji', 'umrah', 'haji_khusus'],
            ]
        );

        $travelCompany->setDefaultCapabilities();
        $travelCompany->save();

        $this->command->info('Travel company seeded: PT. Mataram Travel (Kota Mataram)');
    }
}

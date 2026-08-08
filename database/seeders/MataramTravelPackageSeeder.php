<?php

namespace Database\Seeders;

use App\Models\TravelCompany;
use App\Models\TravelPackage;
use Illuminate\Database\Seeder;

class MataramTravelPackageSeeder extends Seeder
{
    public function run(): void
    {
        $travel = TravelCompany::query()
            ->where('Penyelenggara', 'PT. Mataram Travel')
            ->first();

        if (! $travel) {
            $this->command->error('PT. Mataram Travel tidak ditemukan. Jalankan DevTravelSeeder atau TravelCompanySeeder dulu.');

            return;
        }

        $packages = [
            [
                'name' => 'Umrah Reguler 9 Hari',
                'price' => 25000000,
                'days' => 9,
                'default_airline' => 'Garuda Indonesia',
                'service_notes' => 'Konsumsi, transportasi, manasik, petugas, dan asuransi perjalanan.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Umrah Plus 12 Hari',
                'price' => 32000000,
                'days' => 12,
                'default_airline' => 'Lion Air',
                'service_notes' => 'Hotel bintang 4, ziarah tambahan, konsumsi, transportasi, manasik, petugas, dan asuransi.',
                'sort_order' => 2,
            ],
        ];

        foreach ($packages as $package) {
            TravelPackage::updateOrCreate(
                [
                    'travel_id' => $travel->id,
                    'name' => $package['name'],
                ],
                [
                    ...$package,
                    'travel_id' => $travel->id,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Paket demo PT. Mataram Travel: 2 paket aktif.');
    }
}

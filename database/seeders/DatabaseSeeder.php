<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            TravelCompanySeeder::class,
            UpdateTravelCapabilitiesSeeder::class,
            KabupatenSeeder::class,
            TravelUserSeeder::class,
            CabangTravelSeeder::class,
            BAPSeeder::class,
            JamaahUmrahSeeder::class,
            JamaahHajiKhususSeeder::class,
            PengaduanSeeder::class,
            V2MasterChecklistSeeder::class,
            PengawasSeeder::class,
            PimpinanSeeder::class,
        ]);
    }
}

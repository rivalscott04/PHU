<?php

namespace Database\Seeders;

use App\Models\JamaahHajiKhusus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            BAPSeeder::class,
            CabangTravelSeeder::class,
            JamaahHajiKhususSeeder::class,
            JamaahUmrahSeeder::class,
            KabupatenSeeder::class,
            TravelCompanySeeder::class,
            TravelUserSeeder::class,
            UpdateTravelCapabilitiesSeeder::class,
        ]);
    }
}

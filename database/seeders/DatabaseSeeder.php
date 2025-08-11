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
            TravelCompanySeeder::class,
            UpdateTravelCapabilitiesSeeder::class,
            KabupatenSeeder::class,
            TravelUserSeeder::class,
            CabangTravelSeeder::class,
            BAPSeeder::class,
            JamaahUmrahSeeder::class,
            JamaahHajiKhususSeeder::class,
        ]);
    }
}

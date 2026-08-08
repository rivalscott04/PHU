<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Data contoh travel untuk development / automated test saja.
 * Jangan jalankan di production — travel live dari registrasi mandiri.
 */
class DevTravelSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TravelCompanySeeder::class,
            UpdateTravelCapabilitiesSeeder::class,
            TravelUserSeeder::class,
            CabangTravelSeeder::class,
            MataramTravelPackageSeeder::class,
        ]);

        $this->command->warn('DevTravelSeeder: PT. Mataram Travel + mataram.travel@phu.com (development only).');
    }
}

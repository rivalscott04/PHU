<?php

namespace Database\Seeders;

use App\Models\TravelCompany;
use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;

class TravelUserSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $travelCompany = TravelCompany::query()
            ->where('kab_kota', 'Kota Mataram')
            ->first();

        if (! $travelCompany) {
            $this->command->error('Travel Kota Mataram tidak ditemukan. Jalankan TravelCompanySeeder dulu.');

            return;
        }

        $this->seedUser([
            'nama' => 'User PT Mataram Travel',
            'email' => 'mataram.travel@phu.com',
            'nomor_hp' => '081300000001',
            'role' => 'user',
            'travel_id' => $travelCompany->id,
            'kabupaten' => 'Kota Mataram',
            'city' => 'Mataram',
            'country' => 'Indonesia',
            'about' => "Akun travel {$travelCompany->Penyelenggara}",
        ]);

        $this->command->info('Travel user seeded: mataram.travel@phu.com / password123');
    }
}

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
        $travelCompanies = TravelCompany::all();

        if ($travelCompanies->isEmpty()) {
            $this->command->error('No travel companies found. Please run TravelCompanySeeder first.');

            return;
        }

        $travelUsers = [
            ['nama' => 'User PT Lombok Barat Travel', 'email' => 'lombokbarat.travel@phu.com', 'kab_kota' => 'Lombok Barat', 'city' => 'Gerung', 'nomor_hp' => '081300000001'],
            ['nama' => 'User PT Lombok Tengah Travel', 'email' => 'lomboktengah.travel@phu.com', 'kab_kota' => 'Lombok Tengah', 'city' => 'Praya', 'nomor_hp' => '081300000002'],
            ['nama' => 'User PT Lombok Timur Travel', 'email' => 'lomboktimur.travel@phu.com', 'kab_kota' => 'Lombok Timur', 'city' => 'Selong', 'nomor_hp' => '081300000003'],
            ['nama' => 'User PT Sumbawa Travel', 'email' => 'sumbawa.travel@phu.com', 'kab_kota' => 'Sumbawa', 'city' => 'Sumbawa Besar', 'nomor_hp' => '081300000004'],
            ['nama' => 'User PT Sumbawa Barat Travel', 'email' => 'sumbawabarat.travel@phu.com', 'kab_kota' => 'Sumbawa Barat', 'city' => 'Taliwang', 'nomor_hp' => '081300000005'],
            ['nama' => 'User PT Dompu Travel', 'email' => 'dompu.travel@phu.com', 'kab_kota' => 'Dompu', 'city' => 'Dompu', 'nomor_hp' => '081300000006'],
            ['nama' => 'User PT Bima Travel', 'email' => 'bima.travel@phu.com', 'kab_kota' => 'Bima', 'city' => 'Woha', 'nomor_hp' => '081300000007'],
            ['nama' => 'User PT Mataram Travel', 'email' => 'mataram.travel@phu.com', 'kab_kota' => 'Kota Mataram', 'city' => 'Mataram', 'nomor_hp' => '081300000008'],
            ['nama' => 'User PT Kota Bima Travel', 'email' => 'kotabima.travel@phu.com', 'kab_kota' => 'Kota Bima', 'city' => 'Bima', 'nomor_hp' => '081300000009'],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($travelUsers as $userData) {
            $travelCompany = $travelCompanies->firstWhere('kab_kota', $userData['kab_kota']);

            if (! $travelCompany) {
                $this->command->warn("Travel company not found for kab/kota: {$userData['kab_kota']}");
                $skippedCount++;

                continue;
            }

            $user = $this->seedUser([
                'nama' => $userData['nama'],
                'email' => $userData['email'],
                'nomor_hp' => $userData['nomor_hp'],
                'role' => 'user',
                'travel_id' => $travelCompany->id,
                'kabupaten' => $userData['kab_kota'],
                'city' => $userData['city'],
                'country' => 'Indonesia',
                'about' => "Akun travel {$travelCompany->Penyelenggara}",
            ]);

            if ($user->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->command->info("Travel users seeded ({$createdCount} baru, {$skippedCount} sudah ada). Password default: password123");
    }
}

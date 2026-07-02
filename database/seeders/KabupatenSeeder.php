<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;

class KabupatenSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $kabupatenUsers = [
            ['nama' => 'Admin Kabupaten Lombok Barat', 'email' => 'kabupaten.lombokbarat@phu.com', 'kabupaten' => 'Lombok Barat', 'city' => 'Gerung', 'postal' => '83362', 'nomor_hp' => '081200000001'],
            ['nama' => 'Admin Kabupaten Lombok Tengah', 'email' => 'kabupaten.lomboktengah@phu.com', 'kabupaten' => 'Lombok Tengah', 'city' => 'Praya', 'postal' => '83511', 'nomor_hp' => '081200000002'],
            ['nama' => 'Admin Kabupaten Lombok Timur', 'email' => 'kabupaten.lomboktimur@phu.com', 'kabupaten' => 'Lombok Timur', 'city' => 'Selong', 'postal' => '83611', 'nomor_hp' => '081200000003'],
            ['nama' => 'Admin Kabupaten Sumbawa', 'email' => 'kabupaten.sumbawa@phu.com', 'kabupaten' => 'Sumbawa', 'city' => 'Sumbawa Besar', 'postal' => '84311', 'nomor_hp' => '081200000004'],
            ['nama' => 'Admin Kabupaten Sumbawa Barat', 'email' => 'kabupaten.sumbawabarat@phu.com', 'kabupaten' => 'Sumbawa Barat', 'city' => 'Taliwang', 'postal' => '84455', 'nomor_hp' => '081200000005'],
            ['nama' => 'Admin Kabupaten Dompu', 'email' => 'kabupaten.dompu@phu.com', 'kabupaten' => 'Dompu', 'city' => 'Dompu', 'postal' => '84211', 'nomor_hp' => '081200000006'],
            ['nama' => 'Admin Kabupaten Bima', 'email' => 'kabupaten.bima@phu.com', 'kabupaten' => 'Bima', 'city' => 'Woha', 'postal' => '84151', 'nomor_hp' => '081200000007'],
            ['nama' => 'Admin Kota Mataram', 'email' => 'kota.mataram@phu.com', 'kabupaten' => 'Kota Mataram', 'city' => 'Mataram', 'postal' => '83111', 'nomor_hp' => '081200000008'],
            ['nama' => 'Admin Kota Bima', 'email' => 'kota.bima@phu.com', 'kabupaten' => 'Kota Bima', 'city' => 'Bima', 'postal' => '84111', 'nomor_hp' => '081200000009'],
        ];

        $createdCount = 0;

        foreach ($kabupatenUsers as $userData) {
            $user = $this->seedUser([
                ...$userData,
                'role' => 'kabupaten',
                'travel_id' => null,
                'country' => 'Indonesia',
                'about' => "Administrator {$userData['kabupaten']}",
            ]);

            if ($user->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("Kabupaten/kota users seeded ({$createdCount} baru). Password default: password123");
    }
}

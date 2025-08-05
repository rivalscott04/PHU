<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KabupatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create kabupaten/kota users
        $kabupatenUsers = [
            [
                'username' => 'kabupaten.lombokbarat',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Barat',
                'email' => 'kabupaten.lombokbarat@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kabupaten.lomboktengah',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Tengah',
                'email' => 'kabupaten.lomboktengah@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kabupaten.lomboktimur',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Timur',
                'email' => 'kabupaten.lomboktimur@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kabupaten.sumbawa',
                'firstname' => 'Kabupaten',
                'lastname' => 'Sumbawa',
                'email' => 'kabupaten.sumbawa@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kabupaten.sumbawabarat',
                'firstname' => 'Kabupaten',
                'lastname' => 'Sumbawa Barat',
                'email' => 'sumbawabarat@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kabupaten.dompu',
                'firstname' => 'Kabupaten',
                'lastname' => 'Dompu',
                'email' => 'dompu@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kabupaten.bima',
                'firstname' => 'Kabupaten',
                'lastname' => 'Bima',
                'email' => 'kabupaten.bima@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kota.mataram',
                'firstname' => 'Kota',
                'lastname' => 'Mataram',
                'email' => 'mataram@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
            [
                'username' => 'kota.bima',
                'firstname' => 'Kota',
                'lastname' => 'Bima',
                'email' => 'kobi@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
            ],
        ];

        foreach ($kabupatenUsers as $userData) {
            User::create($userData);
        }

        $this->command->info('NTB Kabupaten/Kota users seeded successfully!');
        $this->command->info('Total kabupaten/kota users created: ' . count($kabupatenUsers));
        $this->command->info('Default password for all users: password123');
        $this->command->info('Users must change their password on first login.');
    }
}

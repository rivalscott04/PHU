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
                'kabupaten' => 'Lombok Barat',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'city' => 'Gerung',
                'country' => 'Indonesia',
                'postal' => '83362',
                'about' => 'Administrator Kabupaten Lombok Barat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten.lomboktengah',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Tengah',
                'email' => 'kabupaten.lomboktengah@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Lombok Tengah',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'city' => 'Praya',
                'country' => 'Indonesia',
                'postal' => '83511',
                'about' => 'Administrator Kabupaten Lombok Tengah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten.lomboktimur',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Timur',
                'email' => 'kabupaten.lomboktimur@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Lombok Timur',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'city' => 'Selong',
                'country' => 'Indonesia',
                'postal' => '83611',
                'about' => 'Administrator Kabupaten Lombok Timur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten.sumbawa',
                'firstname' => 'Kabupaten',
                'lastname' => 'Sumbawa',
                'email' => 'kabupaten.sumbawa@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Sumbawa',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'city' => 'Sumbawa Besar',
                'country' => 'Indonesia',
                'postal' => '84311',
                'about' => 'Administrator Kabupaten Sumbawa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten.sumbawabarat',
                'firstname' => 'Kabupaten',
                'lastname' => 'Sumbawa Barat',
                'email' => 'kabupaten.sumbawabarat@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Sumbawa Barat',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'city' => 'Taliwang',
                'country' => 'Indonesia',
                'postal' => '84455',
                'about' => 'Administrator Kabupaten Sumbawa Barat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten.dompu',
                'firstname' => 'Kabupaten',
                'lastname' => 'Dompu',
                'email' => 'kabupaten.dompu@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Dompu',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Dompu No. 987, Dompu',
                'city' => 'Dompu',
                'country' => 'Indonesia',
                'postal' => '84211',
                'about' => 'Administrator Kabupaten Dompu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten.bima',
                'firstname' => 'Kabupaten',
                'lastname' => 'Bima',
                'email' => 'kabupaten.bima@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Bima',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'city' => 'Woha',
                'country' => 'Indonesia',
                'postal' => '84151',
                'about' => 'Administrator Kabupaten Bima',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kota.mataram',
                'firstname' => 'Kota',
                'lastname' => 'Mataram',
                'email' => 'kota.mataram@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Kota Mataram',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Pejanggik No. 258, Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Administrator Kota Mataram',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kota.bima',
                'firstname' => 'Kota',
                'lastname' => 'Bima',
                'email' => 'kota.bima@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Kota Bima',
                'travel_id' => null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'city' => 'Bima',
                'country' => 'Indonesia',
                'postal' => '84111',
                'about' => 'Administrator Kota Bima',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $createdCount = 0;
        foreach ($kabupatenUsers as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                $this->command->info("User already exists: " . $userData['email']);
                continue;
            }

            // Create user with proper password hashing
            User::create($userData);
            $createdCount++;
        }

        $this->command->info('NTB Kabupaten/Kota users seeded successfully!');
        $this->command->info('Total kabupaten/kota users created: ' . $createdCount);
        $this->command->info('Default password for all users: password123');
        $this->command->info('Users must change their password on first login.');
        $this->command->info('');
        $this->command->info('Kabupaten/Kota accounts:');
        $this->command->info('- Lombok Barat: kabupaten.lombokbarat@phu.com');
        $this->command->info('- Lombok Tengah: kabupaten.lomboktengah@phu.com');
        $this->command->info('- Lombok Timur: kabupaten.lomboktimur@phu.com');
        $this->command->info('- Sumbawa: kabupaten.sumbawa@phu.com');
        $this->command->info('- Sumbawa Barat: kabupaten.sumbawabarat@phu.com');
        $this->command->info('- Dompu: kabupaten.dompu@phu.com');
        $this->command->info('- Bima: kabupaten.bima@phu.com');
        $this->command->info('- Kota Mataram: kota.mataram@phu.com');
        $this->command->info('- Kota Bima: kota.bima@phu.com');
    }
}

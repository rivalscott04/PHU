<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TravelCompany;
use Illuminate\Support\Facades\Hash;

class TravelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get travel companies that were created
        $travelCompanies = TravelCompany::all();
        
        if ($travelCompanies->isEmpty()) {
            $this->command->error('No travel companies found. Please run TravelCompanySeeder first.');
            return;
        }

        $travelUsers = [
            [
                'username' => 'lombokbarat.travel',
                'firstname' => 'Lombok Barat',
                'lastname' => 'Travel',
                'email' => 'lombokbarat.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Lombok Barat')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'city' => 'Gerung',
                'country' => 'Indonesia',
                'postal' => '83362',
                'about' => 'Travel company for Lombok Barat area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'lomboktengah.travel',
                'firstname' => 'Lombok Tengah',
                'lastname' => 'Travel',
                'email' => 'lomboktengah.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Lombok Tengah')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'city' => 'Praya',
                'country' => 'Indonesia',
                'postal' => '83511',
                'about' => 'Travel company for Lombok Tengah area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'lomboktimur.travel',
                'firstname' => 'Lombok Timur',
                'lastname' => 'Travel',
                'email' => 'lomboktimur.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Lombok Timur')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'city' => 'Selong',
                'country' => 'Indonesia',
                'postal' => '83611',
                'about' => 'Travel company for Lombok Timur area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'sumbawa.travel',
                'firstname' => 'Sumbawa',
                'lastname' => 'Travel',
                'email' => 'sumbawa.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Sumbawa')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'city' => 'Sumbawa Besar',
                'country' => 'Indonesia',
                'postal' => '84311',
                'about' => 'Travel company for Sumbawa area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'sumbawabarat.travel',
                'firstname' => 'Sumbawa Barat',
                'lastname' => 'Travel',
                'email' => 'sumbawabarat.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Sumbawa Barat')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'city' => 'Taliwang',
                'country' => 'Indonesia',
                'postal' => '84455',
                'about' => 'Travel company for Sumbawa Barat area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'dompu.travel',
                'firstname' => 'Dompu',
                'lastname' => 'Travel',
                'email' => 'dompu.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Dompu')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Dompu No. 987, Dompu',
                'city' => 'Dompu',
                'country' => 'Indonesia',
                'postal' => '84211',
                'about' => 'Travel company for Dompu area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'bima.travel',
                'firstname' => 'Bima',
                'lastname' => 'Travel',
                'email' => 'bima.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Bima')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'city' => 'Woha',
                'country' => 'Indonesia',
                'postal' => '84151',
                'about' => 'Travel company for Bima area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'mataram.travel',
                'firstname' => 'Kota Mataram',
                'lastname' => 'Travel',
                'email' => 'mataram.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Kota Mataram')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Pejanggik No. 258, Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Travel company for Kota Mataram area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kotabima.travel',
                'firstname' => 'Kota Bima',
                'lastname' => 'Travel',
                'email' => 'kotabima.travel@phu.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'travel_id' => $travelCompanies->where('kab_kota', 'Kota Bima')->first()->id ?? null,
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'address' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'city' => 'Bima',
                'country' => 'Indonesia',
                'postal' => '84111',
                'about' => 'Travel company for Kota Bima area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $createdCount = 0;
        foreach ($travelUsers as $userData) {
            // Skip if travel_id is null (travel company not found)
            if ($userData['travel_id'] === null) {
                $this->command->warn("Travel company not found for: " . $userData['username']);
                continue;
            }

            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                $this->command->info("User already exists: " . $userData['email']);
                continue;
            }

            User::create($userData);
            $createdCount++;
        }

        $this->command->info('NTB Travel Users seeded successfully!');
        $this->command->info('Total travel users created: ' . $createdCount);
        $this->command->info('Travel users correspond to NTB kabupaten/kota:');
        $this->command->info('- Lombok Barat: lombokbarat.travel@phu.com');
        $this->command->info('- Lombok Tengah: lomboktengah.travel@phu.com');
        $this->command->info('- Lombok Timur: lomboktimur.travel@phu.com');
        $this->command->info('- Sumbawa: sumbawa.travel@phu.com');
        $this->command->info('- Sumbawa Barat: sumbawabarat.travel@phu.com');
        $this->command->info('- Dompu: dompu.travel@phu.com');
        $this->command->info('- Bima: bima.travel@phu.com');
        $this->command->info('- Kota Mataram: mataram.travel@phu.com');
        $this->command->info('- Kota Bima: kotabima.travel@phu.com');
        $this->command->info('Default password for all users: password123');
        $this->command->info('Users must change their password on first login.');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BAP;
use App\Models\User;
use Carbon\Carbon;

class BAPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user for testing
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'firstname' => 'Admin',
                'lastname' => 'User',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        // Get or create a travel user for testing
        $travelUser = User::firstOrCreate(
            ['email' => 'travel@example.com'],
            [
                'username' => 'traveluser',
                'firstname' => 'Travel',
                'lastname' => 'User',
                'password' => bcrypt('password'),
                'role' => 'user'
            ]
        );

        // Create sample BAP data dengan kabupaten/kota NTB
        BAP::create([
            'name' => 'Ahmad Fauzi',
            'jabatan' => 'Direktur',
            'ppiuname' => 'PT. Travel Umrah Sejahtera',
            'address_phone' => 'Jl. Udayana No. 123, Mataram - 081234567890',
            'kab_kota' => 'Kota Mataram',
            'people' => 25,
            'days' => 10, // 2025-03-25 - 2025-03-15 = 10 hari
            'price' => 25000000,
            'datetime' => '2025-03-15',
            'airlines' => 'Garuda Indonesia',
            'returndate' => '2025-03-25',
            'airlines2' => 'Garuda Indonesia',
            'user_id' => $travelUser->id,
            'status' => 'pending'
        ]);

        BAP::create([
            'name' => 'Siti Nurhaliza',
            'jabatan' => 'Manager',
            'ppiuname' => 'PT. Haji Umrah Berkah',
            'address_phone' => 'Jl. Pejanggik No. 456, Mataram - 081234567891',
            'kab_kota' => 'Kota Mataram',
            'people' => 30,
            'days' => 10, // 2025-04-20 - 2025-04-10 = 10 hari
            'price' => 30000000,
            'datetime' => '2025-04-10',
            'airlines' => 'Saudi Airlines',
            'returndate' => '2025-04-20',
            'airlines2' => 'Saudi Airlines',
            'user_id' => $travelUser->id,
            'status' => 'diajukan'
        ]);

        BAP::create([
            'name' => 'Muhammad Rizki',
            'jabatan' => 'CEO',
            'ppiuname' => 'PT. Travel Haji Indonesia',
            'address_phone' => 'Jl. Selaparang No. 789, Mataram - 081234567892',
            'kab_kota' => 'Kota Mataram',
            'people' => 40,
            'days' => 21, // 2025-06-10 - 2025-05-20 = 21 hari
            'price' => 45000000,
            'datetime' => '2025-05-20',
            'airlines' => 'Emirates',
            'returndate' => '2025-06-10',
            'airlines2' => 'Emirates',
            'user_id' => $travelUser->id,
            'status' => 'diproses',
            'nomor_surat' => 'B-001/Kw.18.04/2/Hj.00/08/2025'
        ]);

        BAP::create([
            'name' => 'Budi Santoso',
            'jabatan' => 'Manager',
            'ppiuname' => 'PT. Umrah Berkah Indonesia',
            'address_phone' => 'Jl. Raya Senggigi No. 789, Lombok Barat - 081234567894',
            'kab_kota' => 'Lombok Barat',
            'people' => 28,
            'days' => 10, // 2025-07-20 - 2025-07-10 = 10 hari
            'price' => 28000000,
            'datetime' => '2025-07-10',
            'airlines' => 'Garuda Indonesia',
            'returndate' => '2025-07-20',
            'airlines2' => 'Garuda Indonesia',
            'user_id' => $travelUser->id,
            'status' => 'diproses',
            'nomor_surat' => 'B-003/Kw.18.04/2/Hj.00/08/2025'
        ]);

        BAP::create([
            'name' => 'Ahmad Santoso',
            'jabatan' => 'Manager',
            'ppiuname' => 'PT. Umrah Berkah Sejahtera',
            'address_phone' => 'Jl. Raya Praya No. 456, Lombok Tengah - 081234567893',
            'kab_kota' => 'Lombok Tengah',
            'people' => 35,
            'days' => 10, // 2025-06-25 - 2025-06-15 = 10 hari
            'price' => 35000000,
            'datetime' => '2025-06-15',
            'airlines' => 'Saudi Airlines',
            'returndate' => '2025-06-25',
            'airlines2' => 'Saudi Airlines',
            'user_id' => $travelUser->id,
            'status' => 'diterima',
            'nomor_surat' => 'B-002/Kw.18.04/2/Hj.00/08/2025'
        ]);

        BAP::create([
            'name' => 'Ahmad Suryadi',
            'jabatan' => 'Direktur',
            'ppiuname' => 'PT. Lombok Barat Travel',
            'address_phone' => 'Jl. Raya Gerung No. 123, Lombok Barat - 0370-123456',
            'kab_kota' => 'Lombok Barat',
            'people' => 5,
            'days' => 10, // 2025-08-25 - 2025-08-15 = 10 hari
            'price' => 20000000,
            'datetime' => '2025-08-15',
            'airlines' => 'Lion Air',
            'returndate' => '2025-08-25',
            'airlines2' => 'Lion Air',
            'user_id' => $travelUser->id,
            'status' => 'diproses',
            'nomor_surat' => 'B-004/Kw.18.04/2/Hj.00/08/2025'
        ]);

        BAP::create([
            'name' => 'Siti Rahma',
            'jabatan' => 'Manager',
            'ppiuname' => 'PT. Lombok Timur Travel',
            'address_phone' => 'Jl. Raya Selong No. 456, Lombok Timur - 0376-123456',
            'kab_kota' => 'Lombok Timur',
            'people' => 15,
            'days' => 10, // 2025-09-20 - 2025-09-10 = 10 hari
            'price' => 32000000,
            'datetime' => '2025-09-10',
            'airlines' => 'Saudi Airlines',
            'returndate' => '2025-09-20',
            'airlines2' => 'Saudi Airlines',
            'user_id' => $travelUser->id,
            'status' => 'diajukan'
        ]);

        BAP::create([
            'name' => 'Budi Prasetyo',
            'jabatan' => 'CEO',
            'ppiuname' => 'PT. Sumbawa Travel',
            'address_phone' => 'Jl. Raya Sumbawa Besar No. 789, Sumbawa - 0371-123456',
            'kab_kota' => 'Sumbawa',
            'people' => 20,
            'days' => 10, // 2025-10-15 - 2025-10-05 = 10 hari
            'price' => 26000000,
            'datetime' => '2025-10-05',
            'airlines' => 'Garuda Indonesia',
            'returndate' => '2025-10-15',
            'airlines2' => 'Garuda Indonesia',
            'user_id' => $travelUser->id,
            'status' => 'pending'
        ]);
    }
}

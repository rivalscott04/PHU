<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BAP;
use App\Models\User;

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

        // Create sample BAP data
        BAP::create([
            'name' => 'Ahmad Fauzi',
            'jabatan' => 'Direktur',
            'ppiuname' => 'PT. Travel Umrah Sejahtera',
            'address_phone' => 'Jl. Sudirman No. 123, Jakarta - 081234567890',
            'kab_kota' => 'Jakarta Pusat',
            'people' => 25,
            'package' => 'Paket Umrah Reguler',
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
            'address_phone' => 'Jl. Thamrin No. 456, Jakarta - 081234567891',
            'kab_kota' => 'Jakarta Selatan',
            'people' => 30,
            'package' => 'Paket Umrah Plus',
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
            'address_phone' => 'Jl. Gatot Subroto No. 789, Jakarta - 081234567892',
            'kab_kota' => 'Jakarta Barat',
            'people' => 40,
            'package' => 'Paket Haji Khusus',
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
            'address_phone' => 'Jl. Sudirman No. 789, Jakarta - 081234567894',
            'kab_kota' => 'Jakarta Pusat',
            'people' => 28,
            'package' => 'Paket Umrah Reguler',
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
            'address_phone' => 'Jl. Sudirman No. 456, Jakarta - 081234567893',
            'kab_kota' => 'Jakarta Pusat',
            'people' => 35,
            'package' => 'Paket Umrah Premium',
            'price' => 35000000,
            'datetime' => '2025-06-15',
            'airlines' => 'Saudi Airlines',
            'returndate' => '2025-06-25',
            'airlines2' => 'Saudi Airlines',
            'user_id' => $travelUser->id,
            'status' => 'diterima',
            'nomor_surat' => 'B-002/Kw.18.04/2/Hj.00/08/2025'
        ]);
    }
}

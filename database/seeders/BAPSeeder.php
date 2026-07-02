<?php

namespace Database\Seeders;

use App\Models\BAP;
use App\Models\User;
use Illuminate\Database\Seeder;

class BAPSeeder extends Seeder
{
    public function run(): void
    {
        $travelUser = User::where('role', 'user')->first();

        if (! $travelUser) {
            $this->command->warn('BAP seeder dilewati: tidak ada user travel. Jalankan TravelUserSeeder dulu.');

            return;
        }

        if (BAP::query()->exists()) {
            $this->command->info('BAP seeder dilewati: data BAP sudah ada.');

            return;
        }

        $samples = [
            [
                'name' => 'Ahmad Fauzi',
                'jabatan' => 'Direktur',
                'ppiuname' => 'PT. Travel Umrah Sejahtera',
                'address_phone' => 'Jl. Udayana No. 123, Mataram - 081234567890',
                'kab_kota' => 'Kota Mataram',
                'people' => 25,
                'days' => 10,
                'price' => 25000000,
                'datetime' => '2025-03-15',
                'airlines' => 'Garuda Indonesia',
                'returndate' => '2025-03-25',
                'airlines2' => 'Garuda Indonesia',
                'status' => 'pending',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'jabatan' => 'Manager',
                'ppiuname' => 'PT. Haji Umrah Berkah',
                'address_phone' => 'Jl. Pejanggik No. 456, Mataram - 081234567891',
                'kab_kota' => 'Kota Mataram',
                'people' => 30,
                'days' => 10,
                'price' => 30000000,
                'datetime' => '2025-04-10',
                'airlines' => 'Saudi Airlines',
                'returndate' => '2025-04-20',
                'airlines2' => 'Saudi Airlines',
                'status' => 'diajukan',
            ],
            [
                'name' => 'Muhammad Rizki',
                'jabatan' => 'CEO',
                'ppiuname' => 'PT. Travel Haji Indonesia',
                'address_phone' => 'Jl. Selaparang No. 789, Mataram - 081234567892',
                'kab_kota' => 'Kota Mataram',
                'people' => 40,
                'days' => 21,
                'price' => 45000000,
                'datetime' => '2025-05-20',
                'airlines' => 'Emirates',
                'returndate' => '2025-06-10',
                'airlines2' => 'Emirates',
                'status' => 'diproses',
                'nomor_surat' => 'B-001/Kw.18.04/2/Hj.00/08/2025',
            ],
            [
                'name' => 'Budi Santoso',
                'jabatan' => 'Manager',
                'ppiuname' => 'PT. Umrah Berkah Indonesia',
                'address_phone' => 'Jl. Raya Senggigi No. 789, Lombok Barat - 081234567894',
                'kab_kota' => 'Lombok Barat',
                'people' => 28,
                'days' => 10,
                'price' => 28000000,
                'datetime' => '2025-07-10',
                'airlines' => 'Garuda Indonesia',
                'returndate' => '2025-07-20',
                'airlines2' => 'Garuda Indonesia',
                'status' => 'diproses',
                'nomor_surat' => 'B-003/Kw.18.04/2/Hj.00/08/2025',
            ],
            [
                'name' => 'Ahmad Santoso',
                'jabatan' => 'Manager',
                'ppiuname' => 'PT. Umrah Berkah Sejahtera',
                'address_phone' => 'Jl. Raya Praya No. 456, Lombok Tengah - 081234567893',
                'kab_kota' => 'Lombok Tengah',
                'people' => 35,
                'days' => 10,
                'price' => 35000000,
                'datetime' => '2025-06-15',
                'airlines' => 'Saudi Airlines',
                'returndate' => '2025-06-25',
                'airlines2' => 'Saudi Airlines',
                'status' => 'diterima',
                'nomor_surat' => 'B-002/Kw.18.04/2/Hj.00/08/2025',
            ],
            [
                'name' => 'Ahmad Suryadi',
                'jabatan' => 'Direktur',
                'ppiuname' => 'PT. Lombok Barat Travel',
                'address_phone' => 'Jl. Raya Gerung No. 123, Lombok Barat - 0370-123456',
                'kab_kota' => 'Lombok Barat',
                'people' => 5,
                'days' => 10,
                'price' => 20000000,
                'datetime' => '2025-08-15',
                'airlines' => 'Lion Air',
                'returndate' => '2025-08-25',
                'airlines2' => 'Lion Air',
                'status' => 'diproses',
                'nomor_surat' => 'B-004/Kw.18.04/2/Hj.00/08/2025',
            ],
        ];

        foreach ($samples as $sample) {
            BAP::create([
                ...$sample,
                'user_id' => $travelUser->id,
            ]);
        }

        $this->command->info('BAP sample data seeded: '.count($samples).' records');
    }
}

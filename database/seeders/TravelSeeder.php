<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\TravelCompany;
use App\Models\User;

class TravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $travelCompanies = [
            [
                'Penyelenggara' => 'PT. Wisata Sejahtera',
                'Pusat' => 'Jakarta',
                'Tanggal' => '2024-01-15',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-10',
                'lembaga_akreditasi' => 'Kementerian Pariwisata',
                'Pimpinan' => 'Ahmad Rizki',
                'alamat_kantor_lama' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'alamat_kantor_baru' => 'Jl. Thamrin No. 45, Jakarta Pusat',
                'Telepon' => '021-5550123',
                'Status' => 'PPIU',
                'kab_kota' => 'Jakarta Pusat',
                'can_haji' => 0,
                'can_umrah' => 1,
                'description' => null,
                'license_number' => null,
                'license_expiry' => null,
                'capabilities' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Penyelenggara' => 'PT. Haji Umrah Mandiri',
                'Pusat' => 'Bandung',
                'Tanggal' => '2024-02-20',
                'nilai_akreditasi' => 'B',
                'tanggal_akreditasi' => '2024-02-15',
                'lembaga_akreditasi' => 'Kementerian Pariwisata',
                'Pimpinan' => 'Siti Nurhaliza',
                'alamat_kantor_lama' => 'Jl. Asia Afrika No. 67, Bandung',
                'alamat_kantor_baru' => 'Jl. Braga No. 89, Bandung',
                'Telepon' => '022-5550456',
                'Status' => 'PIHK',
                'kab_kota' => 'Bandung',
                'can_haji' => 0,
                'can_umrah' => 1,
                'description' => null,
                'license_number' => null,
                'license_expiry' => null,
                'capabilities' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Penyelenggara' => 'PT. Travel Makmur Sejahtera',
                'Pusat' => 'Surabaya',
                'Tanggal' => '2024-03-10',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-03-05',
                'lembaga_akreditasi' => 'Kementerian Pariwisata',
                'Pimpinan' => 'Budi Santoso',
                'alamat_kantor_lama' => 'Jl. Tunjungan No. 234, Surabaya',
                'alamat_kantor_baru' => 'Jl. Basuki Rahmat No. 56, Surabaya',
                'Telepon' => '031-5550789',
                'Status' => 'PPIU',
                'kab_kota' => 'Surabaya',
                'can_haji' => 0,
                'can_umrah' => 1,
                'description' => null,
                'license_number' => null,
                'license_expiry' => null,
                'capabilities' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Penyelenggara' => 'PT. Haji Umrah Berkah',
                'Pusat' => 'Medan',
                'Tanggal' => '2024-04-05',
                'nilai_akreditasi' => 'B',
                'tanggal_akreditasi' => '2024-04-01',
                'lembaga_akreditasi' => 'Kementerian Pariwisata',
                'Pimpinan' => 'Rina Marlina',
                'alamat_kantor_lama' => 'Jl. Sudirman No. 345, Medan',
                'alamat_kantor_baru' => 'Jl. Gatot Subroto No. 78, Medan',
                'Telepon' => '061-5550112',
                'Status' => 'PIHK',
                'kab_kota' => 'Medan',
                'can_haji' => 0,
                'can_umrah' => 1,
                'description' => null,
                'license_number' => null,
                'license_expiry' => null,
                'capabilities' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Penyelenggara' => 'PT. Wisata Haji Indonesia',
                'Pusat' => 'Semarang',
                'Tanggal' => '2024-05-12',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-05-08',
                'lembaga_akreditasi' => 'Kementerian Pariwisata',
                'Pimpinan' => 'Dedi Kurniawan',
                'alamat_kantor_lama' => 'Jl. Pandanaran No. 456, Semarang',
                'alamat_kantor_baru' => 'Jl. Gajah Mada No. 90, Semarang',
                'Telepon' => '024-5550345',
                'Status' => 'PPIU',
                'kab_kota' => 'Semarang',
                'can_haji' => 0,
                'can_umrah' => 1,
                'description' => null,
                'license_number' => null,
                'license_expiry' => null,
                'capabilities' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert travel companies
        foreach ($travelCompanies as $travel) {
            $travelCompany = TravelCompany::create($travel);

            // Create corresponding user account for each travel company
            $user = User::create([
                'username' => $travel['Penyelenggara'],
                'firstname' => explode(' ', $travel['Pimpinan'])[0],
                'lastname' => isset(explode(' ', $travel['Pimpinan'])[1]) ? explode(' ', $travel['Pimpinan'])[1] : '',
                'email' => strtolower(str_replace(' ', '', $travel['Penyelenggara'])) . '@travel.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'travel_id' => $travelCompany->id,
                'address' => $travel['alamat_kantor_baru'] ?: $travel['alamat_kantor_lama'],
                'city' => $travel['kab_kota'],
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Akun travel untuk ' . $travel['Penyelenggara'],
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Travel companies and accounts seeded successfully!');
        $this->command->info('Default travel account credentials:');
        $this->command->info('- PT. Wisata Sejahtera: wisatasejahtera@travel.com / travel123');
        $this->command->info('- PT. Haji Umrah Mandiri: hajiumrahmandiri@travel.com / travel123');
        $this->command->info('- PT. Travel Makmur Sejahtera: travelmakmursejahtera@travel.com / travel123');
        $this->command->info('- PT. Haji Umrah Berkah: hajiumrahberkah@travel.com / travel123');
        $this->command->info('- PT. Wisata Haji Indonesia: wisatahajiindonesia@travel.com / travel123');
    }
} 
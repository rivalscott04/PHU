<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelCompany;
use Carbon\Carbon;

class TravelCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create travel companies for NTB kabupaten/kota
        $travelCompanies = [
            [
                'Penyelenggara' => 'PT. Lombok Barat Travel',
                'Pusat' => 'Gerung',
                'Tanggal' => '2024-01-15',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-15',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Ahmad Suryadi',
                'alamat_kantor_lama' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'alamat_kantor_baru' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'Telepon' => '0370-123456',
                'Status' => 'PPIU',
                'kab_kota' => 'Lombok Barat',
                'can_haji' => false,
                'can_umrah' => true,
                'capabilities' => ['umrah'],
            ],
            [
                'Penyelenggara' => 'PT. Lombok Tengah Travel',
                'Pusat' => 'Praya',
                'Tanggal' => '2024-01-20',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-20',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Budi Santoso',
                'alamat_kantor_lama' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'alamat_kantor_baru' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'Telepon' => '0370-234567',
                'Status' => 'PIHK',
                'kab_kota' => 'Lombok Tengah',
                'can_haji' => true,
                'can_umrah' => true,
                'capabilities' => ['haji', 'umrah', 'haji_khusus'],
            ],
            [
                'Penyelenggara' => 'PT. Lombok Timur Travel',
                'Pusat' => 'Selong',
                'Tanggal' => '2024-01-25',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-25',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Siti Nurhaliza',
                'alamat_kantor_lama' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'alamat_kantor_baru' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'Telepon' => '0370-345678',
                'Status' => 'PPIU',
                'kab_kota' => 'Lombok Timur',
                'can_haji' => false,
                'can_umrah' => true,
                'capabilities' => ['umrah'],
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Travel',
                'Pusat' => 'Sumbawa Besar',
                'Tanggal' => '2024-02-01',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-01',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Rudi Hartono',
                'alamat_kantor_lama' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'alamat_kantor_baru' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'Telepon' => '0371-456789',
                'Status' => 'PIHK',
                'kab_kota' => 'Sumbawa',
                'can_haji' => true,
                'can_umrah' => true,
                'capabilities' => ['haji', 'umrah', 'haji_khusus'],
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Barat Travel',
                'Pusat' => 'Taliwang',
                'Tanggal' => '2024-02-05',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-05',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Dewi Sartika',
                'alamat_kantor_lama' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'alamat_kantor_baru' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'Telepon' => '0371-567890',
                'Status' => 'PPIU',
                'kab_kota' => 'Sumbawa Barat',
                'can_haji' => false,
                'can_umrah' => true,
                'capabilities' => ['umrah'],
            ],
            [
                'Penyelenggara' => 'PT. Dompu Travel',
                'Pusat' => 'Dompu',
                'Tanggal' => '2024-02-10',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-10',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Muhammad Rizki',
                'alamat_kantor_lama' => 'Jl. Raya Dompu No. 987, Dompu',
                'alamat_kantor_baru' => 'Jl. Raya Dompu No. 987, Dompu',
                'Telepon' => '0371-678901',
                'Status' => 'PPIU',
                'kab_kota' => 'Dompu',
                'can_haji' => false,
                'can_umrah' => true,
                'capabilities' => ['umrah'],
            ],
            [
                'Penyelenggara' => 'PT. Bima Travel',
                'Pusat' => 'Woha',
                'Tanggal' => '2024-02-15',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-15',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Ahmad Fauzi',
                'alamat_kantor_lama' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'alamat_kantor_baru' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'Telepon' => '0371-789012',
                'Status' => 'PIHK',
                'kab_kota' => 'Bima',
                'can_haji' => true,
                'can_umrah' => true,
                'capabilities' => ['haji', 'umrah', 'haji_khusus'],
            ],
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'Pusat' => 'Mataram',
                'Tanggal' => '2024-02-20',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-20',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Lina Marlina',
                'alamat_kantor_lama' => 'Jl. Pejanggik No. 258, Mataram',
                'alamat_kantor_baru' => 'Jl. Pejanggik No. 258, Mataram',
                'Telepon' => '0370-890123',
                'Status' => 'PIHK',
                'kab_kota' => 'Kota Mataram',
                'can_haji' => true,
                'can_umrah' => true,
                'capabilities' => ['haji', 'umrah', 'haji_khusus'],
            ],
            [
                'Penyelenggara' => 'PT. Kota Bima Travel',
                'Pusat' => 'Bima',
                'Tanggal' => '2024-02-25',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-25',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Nurul Hidayati',
                'alamat_kantor_lama' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'alamat_kantor_baru' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'Telepon' => '0371-901234',
                'Status' => 'PPIU',
                'kab_kota' => 'Kota Bima',
                'can_haji' => false,
                'can_umrah' => true,
                'capabilities' => ['umrah'],
            ],
        ];

        foreach ($travelCompanies as $companyData) {
            $travelCompany = TravelCompany::updateOrCreate(
                ['Penyelenggara' => $companyData['Penyelenggara']],
                $companyData
            );
            
            // Set default capabilities based on status
            $travelCompany->setDefaultCapabilities();
            $travelCompany->save();
        }

        $this->command->info('NTB Travel Companies seeded successfully!');
        $this->command->info('Total travel companies created: ' . count($travelCompanies));
        $this->command->info('Travel companies correspond to NTB kabupaten/kota:');
        $this->command->info('- Lombok Barat (PPIU)');
        $this->command->info('- Lombok Tengah (PIHK)');
        $this->command->info('- Lombok Timur (PPIU)');
        $this->command->info('- Sumbawa (PIHK)');
        $this->command->info('- Sumbawa Barat (PPIU)');
        $this->command->info('- Dompu (PPIU)');
        $this->command->info('- Bima (PIHK)');
        $this->command->info('- Kota Mataram (PIHK)');
        $this->command->info('- Kota Bima (PPIU)');
    }
}

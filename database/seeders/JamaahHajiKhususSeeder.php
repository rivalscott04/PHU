<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JamaahHajiKhusus;
use App\Models\TravelCompany;

class JamaahHajiKhususSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get PIHK companies
        $pihkCompanies = TravelCompany::where('Status', 'PIHK')->take(3)->get();
        
        if ($pihkCompanies->isEmpty()) {
            $this->command->info('No PIHK companies found. Please run TravelCompanySeeder first.');
            return;
        }

        $statuses = ['pending', 'approved', 'rejected', 'completed'];
        $genders = ['L', 'P'];
        $bloodTypes = ['A', 'B', 'AB', 'O'];
        
        // NTB kabupaten/kota
        $ntbCities = ['Mataram', 'Lombok Barat', 'Lombok Tengah', 'Lombok Timur', 'Lombok Utara', 'Sumbawa', 'Sumbawa Barat', 'Dompu', 'Bima', 'Kota Bima'];

        $ktpCounter = 1;

        foreach ($pihkCompanies as $company) {
            // Create 5-8 jamaah for each PIHK
            $jamaahCount = rand(5, 8);
            
            for ($i = 1; $i <= $jamaahCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $gender = $genders[array_rand($genders)];
                $bloodType = $bloodTypes[array_rand($bloodTypes)];
                $city = $ntbCities[array_rand($ntbCities)];
                
                JamaahHajiKhusus::create([
                    'travel_id' => $company->id,
                    'nama_lengkap' => 'Jamaah ' . $i . ' - ' . $company->Penyelenggara,
                    'no_ktp' => '520123456789' . str_pad($ktpCounter, 4, '0', STR_PAD_LEFT),
                    'tempat_lahir' => $city,
                    'tanggal_lahir' => '1980-01-01',
                    'jenis_kelamin' => $gender,
                    'alamat' => 'Jl. Contoh No. ' . $i,
                    'kota' => $city,
                    'kecamatan' => 'Kecamatan ' . $city,
                    'provinsi' => 'Nusa Tenggara Barat',
                    'kode_pos' => '12345',
                    'no_hp' => '08123456789',
                    'email' => 'jamaah' . $ktpCounter . '@example.com',
                    'nama_ayah' => 'Ayah Jamaah ' . $i,
                    'pekerjaan' => 'PNS',
                    'pendidikan_terakhir' => 'S1',
                    'status_pernikahan' => 'Menikah',
                    'pergi_haji' => true,
                    'golongan_darah' => $bloodType,
                    'alergi' => null,
                    'no_paspor' => 'A12345678',
                    'tanggal_berlaku_paspor' => '2030-12-31',
                    'tempat_terbit_paspor' => $city,
                    'nomor_porsi' => '12345678',
                    'tahun_pendaftaran' => '2025',
                    'status_pendaftaran' => $status,
                    'catatan_khusus' => null,
                    'dokumen_ktp' => 'ktp.pdf',
                    'dokumen_kk' => 'kk.pdf',
                    'dokumen_paspor' => 'paspor.pdf',
                    'dokumen_foto' => 'foto.jpg',
                    'surat_keterangan' => 'surat.pdf',
                    'bukti_setor_bank' => 'bukti.pdf',
                    'status_verifikasi_bukti' => $status === 'approved' ? 'verified' : 'rejected',
                    'catatan_verifikasi' => null,
                    'tanggal_verifikasi' => $status === 'approved' ? now() : now(),
                    'verified_by' => null,
                ]);
                
                $ktpCounter++;
            }
        }

        $this->command->info('Jamaah Haji Khusus sample data created successfully!');
    }
}

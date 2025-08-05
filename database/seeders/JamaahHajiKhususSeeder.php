<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamaahHajiKhusus;
use App\Models\TravelCompany;

class JamaahHajiKhususSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get PIHK travel companies
        $pihkTravels = TravelCompany::where('Status', 'PIHK')->take(3)->get();

        if ($pihkTravels->isEmpty()) {
            $this->command->info('No PIHK travel companies found. Please run UpdateTravelCapabilitiesSeeder first.');
            return;
        }

        $sampleData = [
            [
                'nama_lengkap' => 'Ahmad Rizki Pratama',
                'no_ktp' => '3171234567890001',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-03-15',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Sudirman No. 123',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '12190',
                'no_hp' => '081234567890',
                'email' => 'ahmad.rizki@email.com',
                'nama_ayah' => 'Budi Santoso',
                'nama_ibu' => 'Siti Aminah',
                'pekerjaan' => 'Pengusaha',
                'pendidikan_terakhir' => 'S1',
                'status_pernikahan' => 'Menikah',
                'golongan_darah' => 'O',
                'riwayat_penyakit' => 'Tidak ada',
                'alergi' => 'Tidak ada',
                'no_paspor' => 'A1234567',
                'tanggal_berlaku_paspor' => '2026-12-31',
                'tempat_terbit_paspor' => 'Jakarta',
                'nomor_porsi' => '123456789',
                'tahun_pendaftaran' => '2023',
                'status_pendaftaran' => 'approved',
                'catatan_khusus' => 'Jamaah VIP - Prioritas tinggi',
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'no_ktp' => '3171234567890002',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-07-22',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asia Afrika No. 45',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '40111',
                'no_hp' => '081234567891',
                'email' => 'siti.nurhaliza@email.com',
                'nama_ayah' => 'Raden Mas Soekarno',
                'nama_ibu' => 'Fatimah Azzahra',
                'pekerjaan' => 'Dokter',
                'pendidikan_terakhir' => 'S2',
                'status_pernikahan' => 'Menikah',
                'golongan_darah' => 'A',
                'riwayat_penyakit' => 'Tidak ada',
                'alergi' => 'Tidak ada',
                'no_paspor' => 'B9876543',
                'tanggal_berlaku_paspor' => '2027-06-30',
                'tempat_terbit_paspor' => 'Bandung',
                'nomor_porsi' => '987654321',
                'tahun_pendaftaran' => '2022',
                'status_pendaftaran' => 'pending',
                'catatan_khusus' => 'Perlu medical check ulang',
            ],
            [
                'nama_lengkap' => 'Muhammad Fadhil Rahman',
                'no_ktp' => '3171234567890003',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1988-11-08',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Pemuda No. 67',
                'kota' => 'Surabaya',
                'provinsi' => 'Jawa Timur',
                'kode_pos' => '60111',
                'no_hp' => '081234567892',
                'email' => 'fadhil.rahman@email.com',
                'nama_ayah' => 'Abdul Rahman',
                'nama_ibu' => 'Aisyah',
                'pekerjaan' => 'Dosen',
                'pendidikan_terakhir' => 'S3',
                'status_pernikahan' => 'Menikah',
                'golongan_darah' => 'B',
                'riwayat_penyakit' => 'Tidak ada',
                'alergi' => 'Tidak ada',
                'no_paspor' => 'C5556666',
                'tanggal_berlaku_paspor' => '2028-03-15',
                'tempat_terbit_paspor' => 'Surabaya',
                'nomor_porsi' => '555666777',
                'tahun_pendaftaran' => '2024',
                'status_pendaftaran' => 'completed',
                'catatan_khusus' => 'Jamaah reguler - Semua dokumen lengkap',
            ],
            [
                'nama_lengkap' => 'Nurul Hidayati',
                'no_ktp' => '3171234567890004',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1992-04-12',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Malioboro No. 89',
                'kota' => 'Yogyakarta',
                'provinsi' => 'DI Yogyakarta',
                'kode_pos' => '55271',
                'no_hp' => '081234567893',
                'email' => 'nurul.hidayati@email.com',
                'nama_ayah' => 'Sugeng Riyadi',
                'nama_ibu' => 'Sri Wahyuni',
                'pekerjaan' => 'Guru',
                'pendidikan_terakhir' => 'S1',
                'status_pernikahan' => 'Belum Menikah',
                'golongan_darah' => 'AB',
                'riwayat_penyakit' => 'Tidak ada',
                'alergi' => 'Tidak ada',
                'no_paspor' => 'D1112222',
                'tanggal_berlaku_paspor' => '2026-09-20',
                'tempat_terbit_paspor' => 'Yogyakarta',
                'nomor_porsi' => '111222333',
                'tahun_pendaftaran' => '2023',
                'status_pendaftaran' => 'rejected',
                'catatan_khusus' => 'Dokumen tidak lengkap - perlu upload ulang',
            ],
            [
                'nama_lengkap' => 'Abdul Malik Al-Habib',
                'no_ktp' => '3171234567890005',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1983-09-25',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Gatot Subroto No. 12',
                'kota' => 'Medan',
                'provinsi' => 'Sumatera Utara',
                'kode_pos' => '20111',
                'no_hp' => '081234567894',
                'email' => 'abdul.malik@email.com',
                'nama_ayah' => 'Habib Umar',
                'nama_ibu' => 'Aminah',
                'pekerjaan' => 'Ustadz',
                'pendidikan_terakhir' => 'S2',
                'status_pernikahan' => 'Menikah',
                'golongan_darah' => 'O',
                'riwayat_penyakit' => 'Tidak ada',
                'alergi' => 'Tidak ada',
                'no_paspor' => 'E7778888',
                'tanggal_berlaku_paspor' => '2027-12-10',
                'tempat_terbit_paspor' => 'Medan',
                'nomor_porsi' => '777888999',
                'tahun_pendaftaran' => '2022',
                'status_pendaftaran' => 'approved',
                'catatan_khusus' => 'Jamaah khusus - Prioritas tinggi',
            ],
        ];

        foreach ($sampleData as $index => $data) {
            $travel = $pihkTravels[$index % $pihkTravels->count()];
            
            JamaahHajiKhusus::create([
                'travel_id' => $travel->id,
                ...$data
            ]);
        }

        $this->command->info('Sample jamaah haji khusus data created successfully!');
        $this->command->info('Created ' . count($sampleData) . ' sample records.');
    }
} 
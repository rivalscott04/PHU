<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sertifikat;
use Carbon\Carbon;

class SertifikatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample certificates for testing
        Sertifikat::create([
            'travel_id' => 1,
            'nama_ppiu' => 'PT. Travel Umrah Sejahtera',
            'nama_kepala' => 'Ahmad Hidayat',
            'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'tanggal_diterbitkan' => Carbon::now(),
            'tanggal_tandatangan' => Carbon::now(),
            'nomor_surat' => 'B-1/Kw.18.01/HJ.00/2/01/2025',
            'nomor_dokumen' => '001',
            'jenis' => 'PPIU',
            'jenis_lokasi' => 'pusat',
            'status' => 'active'
        ]);

        Sertifikat::create([
            'travel_id' => 2,
            'nama_ppiu' => 'PT. Haji Khusus Indonesia',
            'nama_kepala' => 'Siti Nurhaliza',
            'alamat' => 'Jl. Thamrin No. 456, Jakarta Selatan',
            'tanggal_diterbitkan' => Carbon::now(),
            'tanggal_tandatangan' => Carbon::now(),
            'nomor_surat' => 'B-2/Kw.18.01/HJ.00/2/01/2025',
            'nomor_dokumen' => '002',
            'jenis' => 'PPIU',
            'jenis_lokasi' => 'cabang',
            'status' => 'active'
        ]);

        Sertifikat::create([
            'travel_id' => 3,
            'nama_ppiu' => 'PT. Umrah Berkah',
            'nama_kepala' => 'Muhammad Rizki',
            'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta Barat',
            'tanggal_diterbitkan' => Carbon::now(),
            'tanggal_tandatangan' => Carbon::now(),
            'nomor_surat' => 'B-3/Kw.18.01/HJ.00/2/01/2025',
            'nomor_dokumen' => '003',
            'jenis' => 'PPIU',
            'jenis_lokasi' => 'pusat',
            'status' => 'active'
        ]);
    }
} 
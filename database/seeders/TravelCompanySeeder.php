<?php

namespace Database\Seeders;

use App\Enums\TravelRegistrationStatus;
use App\Models\TravelCompany;
use Database\Seeders\Concerns\SeedsSampleDocuments;
use Illuminate\Database\Seeder;

class TravelCompanySeeder extends Seeder
{
    use SeedsSampleDocuments;

    public function run(): void
    {
        $travelCompany = TravelCompany::updateOrCreate(
            ['Penyelenggara' => 'PT. Mataram Travel'],
            [
                // Kolom Pusat adalah nomor SK / NIB, bukan nama kota.
                'Pusat' => 'SK.401/PIHK/2024',
                'Tanggal' => '2024-02-20',
                'license_expiry' => now()->addYears(2)->toDateString(),
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-20',
                'lembaga_akreditasi' => 'Kementerian Haji dan Umroh',
                'Pimpinan' => 'Lina Marlina',
                'alamat_kantor_lama' => 'Jl. Pejanggik No. 258, Mataram',
                'alamat_kantor_baru' => 'Jl. Pejanggik No. 258, Mataram',
                'Telepon' => '0370-890123',
                'Status' => 'PIHK',
                'kab_kota' => 'Kota Mataram',
                'can_haji' => true,
                'can_umrah' => true,
                'capabilities' => ['haji', 'umrah', 'haji_khusus'],
                'registration_status' => TravelRegistrationStatus::Approved,
                // Dibutuhkan tombol pratinjau "SK Pusat" di layar verifikasi cabang.
                'dokumen_sk' => $this->sampleDocument('registrasi-travel/sk/contoh-sk-mataram-travel.pdf'),
            ]
        );

        $travelCompany->setDefaultCapabilities();
        $travelCompany->save();

        $this->command->info('Travel company seeded: PT. Mataram Travel (Kota Mataram)');
    }
}

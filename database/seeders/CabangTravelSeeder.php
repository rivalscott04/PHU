<?php

namespace Database\Seeders;

use App\Enums\TravelRegistrationStatus;
use App\Models\CabangTravel;
use App\Models\TravelCompany;
use Database\Seeders\Concerns\SeedsSampleDocuments;
use Illuminate\Database\Seeder;

class CabangTravelSeeder extends Seeder
{
    use SeedsSampleDocuments;

    public function run(): void
    {
        $pusat = TravelCompany::where('Penyelenggara', 'PT. Mataram Travel')->first();

        // Cabang lama yang sudah sah, mewakili data hasil input petugas.
        CabangTravel::updateOrCreate(
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'kabupaten' => 'Kota Mataram',
            ],
            [
                'travel_id' => $pusat?->id,
                'pusat' => $pusat?->Pusat,
                'pimpinan_pusat' => 'Lina Marlina',
                'alamat_pusat' => 'Jl. Pejanggik No. 258, Mataram',
                'SK_BA' => 'SK.008/MT/2024',
                'tanggal' => '2024-02-20',
                'pimpinan_cabang' => 'Lina Marlina',
                'alamat_cabang' => 'Jl. Pejanggik No. 258, Mataram',
                'telepon' => '0370-890123',
                'registration_status' => TravelRegistrationStatus::Approved,
                'verified_at' => now(),
            ]
        );

        // Cabang yang masih menunggu peninjauan, supaya alur verifikasi Kabko
        // bisa langsung dicoba tanpa harus mendaftar manual dulu.
        CabangTravel::updateOrCreate(
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'kabupaten' => 'Lombok Utara',
            ],
            [
                'travel_id' => $pusat?->id,
                'pusat' => $pusat?->Pusat,
                'pimpinan_pusat' => 'Lina Marlina',
                'alamat_pusat' => 'Jl. Pejanggik No. 258, Mataram',
                'SK_BA' => 'BA.014/MT/2026',
                'tanggal' => '2026-05-20',
                'pimpinan_cabang' => 'Hasan Basri',
                'alamat_cabang' => 'Jl. Raya Tanjung No. 10, Lombok Utara',
                'telepon' => '081805551234',
                'registration_status' => TravelRegistrationStatus::Pending,
                'dokumen_oss' => $this->sampleDocument('registrasi-cabang/oss/contoh-oss.pdf'),
                'dokumen_akta' => $this->sampleDocument('registrasi-cabang/akta/contoh-akta.pdf'),
                'dokumen_ktp_kepala' => $this->sampleDocument('registrasi-cabang/ktp_kepala/contoh-ktp.pdf'),
                'dokumen_sk_du' => $this->sampleDocument('registrasi-cabang/sk_du/contoh-sk-du.pdf'),
            ]
        );

        $this->command->info('Cabang travel seeded: Kota Mataram (disetujui) + Lombok Utara (menunggu verifikasi)');
    }
}

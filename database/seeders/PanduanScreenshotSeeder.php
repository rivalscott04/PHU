<?php

namespace Database\Seeders;

use App\Enums\TravelRegistrationStatus;
use App\Models\CabangTravel;
use App\Models\Pengaduan;
use App\Models\TravelCompany;
use App\Models\User;
use Database\Seeders\Concerns\SeedsSampleDocuments;
use Illuminate\Database\Seeder;

/**
 * Satu set data Mataram untuk screenshot panduan Travel + Admin Kabupaten.
 * Development only. Jangan jalankan di production.
 */
class PanduanScreenshotSeeder extends Seeder
{
    use SeedsSampleDocuments;

    public function run(): void
    {
        $this->call(DevTravelSeeder::class);

        User::query()
            ->whereIn('email', ['mataram.travel@pantau.kemenhaj.id', 'kota.mataram@pantau.kemenhaj.id'])
            ->update(['is_password_changed' => true]);

        $pusat = TravelCompany::query()
            ->where('Penyelenggara', 'PT. Mataram Travel')
            ->first();

        if ($pusat) {
            // Cabang pending di Kota Mataram agar admin kota.mataram bisa unggah rekomendasi.
            CabangTravel::query()->updateOrCreate(
                [
                    'travel_id' => $pusat->id,
                    'kabupaten' => 'Kota Mataram',
                    'SK_BA' => 'BA.PANDUAN/MT/2026',
                ],
                [
                    'Penyelenggara' => 'PT. Mataram Travel',
                    'pusat' => $pusat->Pusat,
                    'pimpinan_pusat' => $pusat->Pimpinan,
                    'alamat_pusat' => $pusat->alamat_kantor_baru ?? $pusat->alamat_kantor_lama,
                    'tanggal' => now()->toDateString(),
                    'pimpinan_cabang' => 'Hasan Basri',
                    'alamat_cabang' => 'Jl. Demo Cabang Panduan No. 5, Kota Mataram',
                    'telepon' => '081805559999',
                    'registration_status' => TravelRegistrationStatus::Pending,
                    'dokumen_oss' => $this->sampleDocument('registrasi-cabang/oss/contoh-oss.pdf'),
                    'dokumen_akta' => $this->sampleDocument('registrasi-cabang/akta/contoh-akta.pdf'),
                    'dokumen_ktp_kepala' => $this->sampleDocument('registrasi-cabang/ktp_kepala/contoh-ktp.pdf'),
                    'dokumen_sk_du' => $this->sampleDocument('registrasi-cabang/sk_du/contoh-sk-du.pdf'),
                ]
            );
        }

        Pengaduan::query()->updateOrCreate(
            [
                'public_token' => '11111111-1111-1111-1111-111111111111',
            ],
            [
                'nama_pengadu' => 'Warga Demo Panduan',
                'travels_id' => $pusat?->id,
                'hal_aduan' => 'Keterlambatan informasi keberangkatan (data contoh panduan).',
                'status' => 'pending',
            ]
        );

        $this->command->info('PanduanScreenshotSeeder siap: Mataram Travel + kota.mataram + cabang pending + 1 pengaduan.');
    }
}

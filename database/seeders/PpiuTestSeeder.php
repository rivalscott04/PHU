<?php

namespace Database\Seeders;

use App\Enums\TravelRegistrationStatus;
use App\Models\TravelCompany;
use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;

/**
 * Akun travel PPIU untuk uji alur: tambah jamaah umrah → BAP → upgrade PIHK → umrah lagi → BAP.
 * Jalankan manual: php artisan db:seed --class=PpiuTestSeeder
 */
class PpiuTestSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $travelCompany = TravelCompany::updateOrCreate(
            ['Penyelenggara' => 'PT. Demo PPIU NTB'],
            [
                'Pusat' => 'Mataram',
                'Tanggal' => '2025-01-15',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2025-01-15',
                'lembaga_akreditasi' => 'Kementerian Haji dan Umroh',
                'Pimpinan' => 'Budi Santoso',
                'alamat_kantor_lama' => 'Jl. Pejanggik No. 100, Mataram',
                'alamat_kantor_baru' => 'Jl. Pejanggik No. 100, Mataram',
                'Telepon' => '0370-111222',
                'Status' => 'PPIU',
                'kab_kota' => 'Kota Mataram',
                'registration_status' => TravelRegistrationStatus::Approved,
                'verified_at' => now(),
            ]
        );

        $travelCompany->setDefaultCapabilities();
        $travelCompany->description = $travelCompany->getTravelTypeDescription();
        $travelCompany->license_number = 'LIC-DPP-'.date('Y');
        $travelCompany->license_expiry = now()->addYears(2);
        $travelCompany->save();

        $this->seedUser([
            'nama' => 'User PT Demo PPIU NTB',
            'email' => 'ppiu.test@phu.com',
            'nomor_hp' => '081300000099',
            'role' => 'user',
            'travel_id' => $travelCompany->id,
            'kabupaten' => 'Kota Mataram',
            'city' => 'Mataram',
            'country' => 'Indonesia',
            'about' => 'Akun uji PPIU → PIHK workflow',
            'is_password_changed' => true,
        ]);

        $this->command->info('PPIU test account ready.');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Email', 'ppiu.test@phu.com'],
                ['Password', 'password123'],
                ['Travel', 'PT. Demo PPIU NTB'],
                ['Status', 'PPIU (approved)'],
                ['Kabupaten', 'Kota Mataram'],
            ]
        );
        $this->command->line('Alur uji: login → tambah jamaah umrah → buat BAP → admin ubah Status ke PIHK → tambah jamaah umrah → buat BAP lagi.');
    }
}

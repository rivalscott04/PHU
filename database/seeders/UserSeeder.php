<?php

namespace Database\Seeders;

use App\Enums\PengawasScopeMode;
use App\Enums\UserRole;
use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $accounts = [
            [
                'nama' => 'Super Admin Kanwil PHU',
                'email' => 'admin@pantau.kemenhaj.id',
                'nomor_hp' => '081100000001',
                'role' => UserRole::Admin->value,
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Super Admin / Admin Kanwil PANTAU',
                'jabatan' => 'Super Admin Kanwil PHU',
            ],
            [
                'nama' => 'Pengawas Kota Mataram',
                'email' => 'pengawas.mataram@pantau.kemenhaj.id',
                'nomor_hp' => '081200000001',
                'role' => UserRole::Pengawas->value,
                'pengawas_scope' => PengawasScopeMode::Single->value,
                'kabupaten' => 'Kota Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'about' => 'Pengawas digital modul V2, Kota Mataram',
            ],
            [
                'nama' => 'Baiq Migiwati',
                'email' => 'kota.mataram@pantau.kemenhaj.id',
                'nomor_hp' => '081200000002',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Kota Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Administrator Kota Mataram',
            ],
            [
                'nama' => 'Ahmad Afandi, S.Kom',
                'email' => 'ahmad.afandi@pantau.kemenhaj.id',
                'nomor_hp' => '081200000004',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Lombok Tengah',
                'city' => 'Praya',
                'country' => 'Indonesia',
                'nip' => '199401232023211015',
                'jabatan' => 'Ahli Pertama Pranata Komputer',
            ],
            [
                'nama' => 'Alwan Wijaya, SE',
                'email' => 'alwan.wijaya@pantau.kemenhaj.id',
                'nomor_hp' => '081200000005',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Lombok Timur',
                'city' => 'Selong',
                'country' => 'Indonesia',
                'nip' => '198301012005011003',
                'jabatan' => 'Analis Data',
            ],
            [
                'nama' => 'Evi Fahlevi, S.IP',
                'email' => 'evi.fahlevi@pantau.kemenhaj.id',
                'nomor_hp' => '081200000006',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Sumbawa Barat',
                'city' => 'Taliwang',
                'country' => 'Indonesia',
                'nip' => '198505152025212012',
                'jabatan' => 'Penata Layanan Operasional',
            ],
            [
                'nama' => 'Kurniawan',
                'email' => 'kurniawan.sumbawa@pantau.kemenhaj.id',
                'nomor_hp' => '081200000007',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Sumbawa',
                'city' => 'Sumbawa Besar',
                'country' => 'Indonesia',
                'nip' => '199205052025211018',
                'jabatan' => 'Operator Layanan Operasional',
            ],
            [
                'nama' => 'Ade Nurma Dahlan',
                'email' => 'ade.nurma.dahlan@pantau.kemenhaj.id',
                'nomor_hp' => '081200000008',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Dompu',
                'city' => 'Dompu',
                'country' => 'Indonesia',
                'jabatan' => 'PTT Kantor Kemenhaj Kabupaten Dompu',
            ],
            [
                'nama' => 'Nursholeh Rifdiati',
                'email' => 'nursholeh.rifdiati@pantau.kemenhaj.id',
                'nomor_hp' => '081200000009',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Kota Bima',
                'city' => 'Bima',
                'country' => 'Indonesia',
                'jabatan' => 'PTT Kantor Kemenhaj Kota Bima',
            ],
            [
                'nama' => 'Baiq Intan Puspa Gading, SH',
                'email' => 'baiq.intan@pantau.kemenhaj.id',
                'nomor_hp' => '081200000010',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Lombok Barat',
                'city' => 'Gerung',
                'country' => 'Indonesia',
            ],
            [
                'nama' => 'Alfi Yusrina, SP',
                'email' => 'alfi.yusrina@pantau.kemenhaj.id',
                'nomor_hp' => '081200000011',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Lombok Utara',
                'city' => 'Tanjung',
                'country' => 'Indonesia',
            ],
            [
                'nama' => 'H. Sudirman, S.Sos',
                'email' => 'h.sudirman@pantau.kemenhaj.id',
                'nomor_hp' => '081200000012',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Bima',
                'city' => 'Woha',
                'country' => 'Indonesia',
                'nip' => '198510072007101002',
                'jabatan' => 'Penata Layanan Operasional',
            ],
            [
                'nama' => 'Kepala Kanwil NTB',
                'email' => 'pimpinan@pantau.kemenhaj.id',
                'nomor_hp' => '081200000003',
                'role' => UserRole::Pimpinan->value,
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'about' => 'Dashboard seluruh NTB',
            ],
        ];

        foreach ($accounts as $account) {
            $this->seedUser($account);
        }

        $this->command->info('Akun inti dan Admin Kabupaten berhasil di-seed (13 akun).');
        $this->command->table(
            ['Peran', 'Email'],
            collect($accounts)->map(fn (array $account) => [
                UserRole::tryFrom($account['role'])?->label().($account['kabupaten'] ?? '' ? ' - '.($account['kabupaten'] ?? '') : ''),
                $account['email'],
            ])->all()
        );
    }
}

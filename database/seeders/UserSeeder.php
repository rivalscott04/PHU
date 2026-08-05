<?php

namespace Database\Seeders;

use App\Enums\PengawasScopeMode;
use App\Enums\UserRole;
use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $accounts = [
            [
                'nama' => 'Super Admin',
                'email' => 'admin@phu.com',
                'nomor_hp' => '081100000001',
                'password' => Hash::make('admin123'),
                'role' => UserRole::Admin->value,
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Super Admin / Admin Kanwil PANTAU',
                'is_password_changed' => true,
            ],
            [
                'nama' => 'Pengawas Kota Mataram',
                'email' => 'pengawas.mataram@phu.local',
                'nomor_hp' => '081200000001',
                'role' => UserRole::Pengawas->value,
                'pengawas_scope' => PengawasScopeMode::Single->value,
                'kabupaten' => 'Kota Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'about' => 'Pengawas digital modul V2, Kota Mataram',
                'is_password_changed' => true,
            ],
            [
                'nama' => 'Admin Kota Mataram',
                'email' => 'kota.mataram@phu.com',
                'nomor_hp' => '081200000002',
                'role' => UserRole::Kabupaten->value,
                'kabupaten' => 'Kota Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Administrator Kota Mataram',
            ],
            [
                'nama' => 'Kepala Kanwil NTB',
                'email' => 'pimpinan@phu.local',
                'nomor_hp' => '081200000003',
                'role' => UserRole::Pimpinan->value,
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'about' => 'Dashboard seluruh NTB',
                'is_password_changed' => true,
            ],
        ];

        foreach ($accounts as $account) {
            $this->seedUser($account);
        }

        $this->command->info('Akun inti berhasil di-seed (4 akun).');
        $this->command->table(
            ['Peran', 'Email', 'Password'],
            [
                ['Super Admin / Admin Kanwil', 'admin@phu.com', 'admin123'],
                ['Pengawas', 'pengawas.mataram@phu.local', 'password123'],
                ['Admin Mataram', 'kota.mataram@phu.com', 'password123'],
                ['Kepala Kanwil', 'pimpinan@phu.local', 'password123'],
            ]
        );
    }
}

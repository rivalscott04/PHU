<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;

class PimpinanSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $this->seedUser([
            'nama' => 'Kepala Kanwil NTB',
            'email' => 'pimpinan@phu.local',
            'nomor_hp' => '081200000001',
            'role' => UserRole::Pimpinan->value,
            'country' => 'Indonesia',
            'about' => 'Dashboard seluruh NTB',
            'is_password_changed' => true,
        ]);

        $this->command->info('Akun pimpinan Kanwil dibuat.');
        $this->command->info('Login: pimpinan@phu.local / password123');
    }
}

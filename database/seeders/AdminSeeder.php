<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use SeedsUsers;

    public function run(): void
    {
        $this->seedUser([
            'nama' => 'Super Admin',
            'email' => 'admin@phu.com',
            'nomor_hp' => '081100000001',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'travel_id' => null,
            'address' => 'Jl. Admin No. 1',
            'city' => 'Mataram',
            'country' => 'Indonesia',
            'postal' => '83111',
            'about' => 'Super Administrator untuk PANTAU',
            'is_password_changed' => true,
        ]);

        $this->command->info('Admin users seeded successfully!');
        $this->command->info('Default admin: admin@phu.com / admin123');
    }
}

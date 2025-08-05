<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'username' => 'admin',
                'firstname' => 'Super',
                'lastname' => 'Admin',
                'email' => 'admin@phu.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'address' => 'Jl. Admin No. 1',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Super Administrator untuk sistem PHU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'admin2',
                'firstname' => 'Admin',
                'lastname' => 'Kedua',
                'email' => 'admin2@phu.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'address' => 'Jl. Admin No. 2',
                'city' => 'Bandung',
                'country' => 'Indonesia',
                'postal' => '54321',
                'about' => 'Administrator kedua untuk sistem PHU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabupaten',
                'firstname' => 'Admin',
                'lastname' => 'Kabupaten',
                'email' => 'kabupaten@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'address' => 'Jl. Kabupaten No. 1',
                'city' => 'Surabaya',
                'country' => 'Indonesia',
                'postal' => '67890',
                'about' => 'Administrator Kabupaten untuk sistem PHU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            DB::table('users')->insert($admin);
        }

        $this->command->info('Admin users seeded successfully!');
        $this->command->info('Default admin credentials:');
        $this->command->info('- Username: admin, Email: admin@phu.com, Password: admin123');
        $this->command->info('- Username: admin2, Email: admin2@phu.com, Password: admin123');
        $this->command->info('- Username: kabupaten, Email: kabupaten@phu.com, Password: kabupaten123');
    }
} 
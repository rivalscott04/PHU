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
                'travel_id' => null,
                'address' => 'Jl. Admin No. 1',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Super Administrator untuk sistem PHU',
                'is_password_changed' => 0,
                'email_verified_at' => null,
                'remember_token' => null,
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
    }
} 
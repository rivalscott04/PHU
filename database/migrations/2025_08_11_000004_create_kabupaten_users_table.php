<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create kabupaten users using firstOrCreate
        $kabupatenUsers = [
            [
                'username' => 'kabupaten.lombokbarat',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Barat',
                'email' => 'kabupaten.lombokbarat@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Lombok Barat',
                'address' => 'Jl. Raya Gerung No. 123, Gerung',
                'city' => 'Gerung',
                'country' => 'Indonesia',
                'postal' => '83363',
                'about' => 'Administrator Kabupaten Lombok Barat',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kabupaten.lomboktengah',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Tengah',
                'email' => 'kabupaten.lomboktengah@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Lombok Tengah',
                'address' => 'Jl. Raya Praya No. 456, Praya',
                'city' => 'Praya',
                'country' => 'Indonesia',
                'postal' => '83511',
                'about' => 'Administrator Kabupaten Lombok Tengah',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kabupaten.lomboktimur',
                'firstname' => 'Kabupaten',
                'lastname' => 'Lombok Timur',
                'email' => 'kabupaten.lomboktimur@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Lombok Timur',
                'address' => 'Jl. Raya Selong No. 789, Selong',
                'city' => 'Selong',
                'country' => 'Indonesia',
                'postal' => '83611',
                'about' => 'Administrator Kabupaten Lombok Timur',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kabupaten.sumbawa',
                'firstname' => 'Kabupaten',
                'lastname' => 'Sumbawa',
                'email' => 'kabupaten.sumbawa@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Sumbawa',
                'address' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'city' => 'Sumbawa Besar',
                'country' => 'Indonesia',
                'postal' => '84311',
                'about' => 'Administrator Kabupaten Sumbawa',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kabupaten.sumbawabarat',
                'firstname' => 'Kabupaten',
                'lastname' => 'Sumbawa Barat',
                'email' => 'kabupaten.sumbawabarat@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Sumbawa Barat',
                'address' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'city' => 'Taliwang',
                'country' => 'Indonesia',
                'postal' => '84455',
                'about' => 'Administrator Kabupaten Sumbawa Barat',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kabupaten.dompu',
                'firstname' => 'Kabupaten',
                'lastname' => 'Dompu',
                'email' => 'kabupaten.dompu@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Dompu',
                'address' => 'Jl. Raya Dompu No. 987, Dompu',
                'city' => 'Dompu',
                'country' => 'Indonesia',
                'postal' => '84211',
                'about' => 'Administrator Kabupaten Dompu',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kabupaten.bima',
                'firstname' => 'Kabupaten',
                'lastname' => 'Bima',
                'email' => 'kabupaten.bima@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Bima',
                'address' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'city' => 'Woha',
                'country' => 'Indonesia',
                'postal' => '84151',
                'about' => 'Administrator Kabupaten Bima',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kota.mataram',
                'firstname' => 'Kota',
                'lastname' => 'Mataram',
                'email' => 'kota.mataram@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Kota Mataram',
                'address' => 'Jl. Pejanggik No. 258, Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '83111',
                'about' => 'Administrator Kota Mataram',
                'is_password_changed' => false,
            ],
            [
                'username' => 'kota.bima',
                'firstname' => 'Kota',
                'lastname' => 'Bima',
                'email' => 'kota.bima@phu.com',
                'password' => Hash::make('kabupaten123'),
                'role' => 'kabupaten',
                'kabupaten' => 'Kota Bima',
                'address' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'city' => 'Bima',
                'country' => 'Indonesia',
                'postal' => '84111',
                'about' => 'Administrator Kota Bima',
                'is_password_changed' => false,
            ],
        ];

        foreach ($kabupatenUsers as $user) {
            DB::table('users')->firstOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'travel_id' => null,
                    'email_verified_at' => null,
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove kabupaten users
        $kabupatenEmails = [
            'kabupaten.lombokbarat@phu.com',
            'kabupaten.lomboktengah@phu.com',
            'kabupaten.lomboktimur@phu.com',
            'kabupaten.sumbawa@phu.com',
            'kabupaten.sumbawabarat@phu.com',
            'kabupaten.dompu@phu.com',
            'kabupaten.bima@phu.com',
            'kota.mataram@phu.com',
            'kota.bima@phu.com',
        ];

        DB::table('users')->whereIn('email', $kabupatenEmails)->delete();
    }
};

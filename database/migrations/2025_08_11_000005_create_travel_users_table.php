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
        // Get travel IDs for foreign key relationships
        $travelIds = [];
        $travels = DB::table('travels')->get();
        foreach ($travels as $travel) {
            $travelIds[$travel->Penyelenggara] = $travel->id;
        }

        // Create travel users using firstOrCreate
        $travelUsers = [
            [
                'username' => 'lombokbarat.travel',
                'firstname' => 'Travel',
                'lastname' => 'Lombok Barat',
                'email' => 'lombokbarat.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Lombok Barat',
                'address' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'city' => 'Gerung',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Lombok Barat Travel',
                'travel_id' => $travelIds['PT. Lombok Barat Travel'] ?? null,
            ],
            [
                'username' => 'lomboktengah.travel',
                'firstname' => 'Travel',
                'lastname' => 'Lombok Tengah',
                'email' => 'lomboktengah.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Lombok Tengah',
                'address' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'city' => 'Praya',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Lombok Tengah Travel',
                'travel_id' => $travelIds['PT. Lombok Tengah Travel'] ?? null,
            ],
            [
                'username' => 'lomboktimur.travel',
                'firstname' => 'Travel',
                'lastname' => 'Lombok Timur',
                'email' => 'lomboktimur.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Lombok Timur',
                'address' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'city' => 'Selong',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Lombok Timur Travel',
                'travel_id' => $travelIds['PT. Lombok Timur Travel'] ?? null,
            ],
            [
                'username' => 'sumbawa.travel',
                'firstname' => 'Travel',
                'lastname' => 'Sumbawa',
                'email' => 'sumbawa.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Sumbawa',
                'address' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'city' => 'Sumbawa Besar',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Sumbawa Travel',
                'travel_id' => $travelIds['PT. Sumbawa Travel'] ?? null,
            ],
            [
                'username' => 'sumbawabarat.travel',
                'firstname' => 'Travel',
                'lastname' => 'Sumbawa Barat',
                'email' => 'sumbawabarat.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Sumbawa Barat',
                'address' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'city' => 'Taliwang',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Sumbawa Barat Travel',
                'travel_id' => $travelIds['PT. Sumbawa Barat Travel'] ?? null,
            ],
            [
                'username' => 'dompu.travel',
                'firstname' => 'Travel',
                'lastname' => 'Dompu',
                'email' => 'dompu.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Dompu',
                'address' => 'Jl. Raya Dompu No. 987, Dompu',
                'city' => 'Dompu',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Dompu Travel',
                'travel_id' => $travelIds['PT. Dompu Travel'] ?? null,
            ],
            [
                'username' => 'bima.travel',
                'firstname' => 'Travel',
                'lastname' => 'Bima',
                'email' => 'bima.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Bima',
                'address' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'city' => 'Woha',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Bima Travel',
                'travel_id' => $travelIds['PT. Bima Travel'] ?? null,
            ],
            [
                'username' => 'kotamataram.travel',
                'firstname' => 'Travel',
                'lastname' => 'Kota Mataram',
                'email' => 'kotamataram.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Kota Mataram',
                'address' => 'Jl. Pejanggik No. 258, Mataram',
                'city' => 'Mataram',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Mataram Travel',
                'travel_id' => $travelIds['PT. Mataram Travel'] ?? null,
            ],
            [
                'username' => 'kotabima.travel',
                'firstname' => 'Travel',
                'lastname' => 'Kota Bima',
                'email' => 'kotabima.travel@phu.com',
                'password' => Hash::make('travel123'),
                'role' => 'user',
                'kabupaten' => 'Kota Bima',
                'address' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'city' => 'Bima',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Travel user for PT. Kota Bima Travel',
                'travel_id' => $travelIds['PT. Kota Bima Travel'] ?? null,
            ],
        ];

        foreach ($travelUsers as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'email_verified_at' => null,
                    'is_password_changed' => false,
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
        // Remove travel users
        $travelEmails = [
            'lombokbarat.travel@phu.com',
            'lomboktengah.travel@phu.com',
            'lomboktimur.travel@phu.com',
            'sumbawa.travel@phu.com',
            'sumbawabarat.travel@phu.com',
            'dompu.travel@phu.com',
            'bima.travel@phu.com',
            'kotamataram.travel@phu.com',
            'kotabima.travel@phu.com',
        ];

        DB::table('users')->whereIn('email', $travelEmails)->delete();
    }
};

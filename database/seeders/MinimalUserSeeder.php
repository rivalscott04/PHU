<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MinimalUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        \App\Models\User::create([
            "nama" => "Super Admin",
            "email" => "admin@phu.com",
            "nomor_hp" => "081100000001",
            "password" => Hash::make("admin123"),
            "role" => UserRole::Admin->value,
            "kabupaten" => "Mataram",
            "city" => "Mataram",
            "country" => "Indonesia",
            "postal" => "83111",
            "about" => "Super Admin PANTAU",
            "is_password_changed" => true,
        ]);

        // 10 Admin Kabupaten/Kota se-NTB
        $kabkota = [
            ["Kota Mataram", "kota.mataram@phu.com", "83111"],
            ["Kab. Lombok Barat", "kab.lobar@phu.com", "83311"],
            ["Kab. Lombok Tengah", "kab.loteng@phu.com", "83511"],
            ["Kab. Lombok Timur", "kab.lotim@phu.com", "83611"],
            ["Kab. Lombok Utara", "kab.lo@phu.com", "83711"],
            ["Kab. Sumbawa", "kab.sumbawa@phu.com", "84311"],
            ["Kab. Sumbawa Barat", "kab.ksb@phu.com", "84411"],
            ["Kab. Dompu", "kab.dompu@phu.com", "84211"],
            ["Kab. Bima", "kab.bima@phu.com", "84111"],
            ["Kota Bima", "kota.bima@phu.com", "84112"],
        ];

        foreach ($kabkota as [$nama, $email, $postal]) {
            \App\Models\User::create([
                "nama" => $nama,
                "email" => $email,
                "nomor_hp" => rand(1000000000, 9999999999),
                "password" => Hash::make("password123"),
                "role" => UserRole::Kabupaten->value,
                "city" => $nama,
                "kabupaten" => $nama,
                "country" => "Indonesia",
                "postal" => $postal,
                "about" => "Admin Kabupaten/Kota PANTAU",
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\PengawasScopeMode;
use App\Support\NtbKabupatenMap;
use Database\Seeders\Concerns\SeedsUsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PengawasSeeder extends Seeder
{
    use SeedsUsers;

    /** @var array<string, string> */
    private array $emailSlugs = [
        'Lombok Barat' => 'lombokbarat',
        'Lombok Tengah' => 'lomboktengah',
        'Lombok Timur' => 'lomboktimur',
        'Sumbawa' => 'sumbawa',
        'Sumbawa Barat' => 'sumbawabarat',
        'Dompu' => 'dompu',
        'Bima' => 'bima',
        'Kota Mataram' => 'mataram',
        'Kota Bima' => 'kotabima',
    ];

    public function run(): void
    {
        $createdCount = 0;
        $counter = 1;

        foreach (array_keys(NtbKabupatenMap::centroids()) as $kabupaten) {
            $slug = $this->emailSlugs[$kabupaten] ?? Str::slug($kabupaten, '');

            $user = $this->seedUser([
                'nama' => 'Pengawas '.$kabupaten,
                'email' => 'pengawas.'.$slug.'@phu.local',
                'nomor_hp' => '0813'.str_pad((string) $counter, 8, '0', STR_PAD_LEFT),
                'role' => UserRole::Pengawas->value,
                'pengawas_scope' => PengawasScopeMode::Single->value,
                'kabupaten' => $kabupaten,
                'country' => 'Indonesia',
                'about' => 'Pengawas digital modul V2, '.$kabupaten,
                'is_password_changed' => true,
            ]);

            if ($user->wasRecentlyCreated) {
                $createdCount++;
            }

            $counter++;
        }

        $this->command->info("Pengawas users seeded ({$createdCount} baru). Password default: password123");
        $this->command->info('Contoh login: pengawas.lombokbarat@phu.local / password123');
    }
}

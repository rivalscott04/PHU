<?php

namespace Database\Seeders;

use App\Models\BAP;
use App\Models\Jamaah;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data demo cepat: jamaah + BA diterima dengan tanggal dekat (kalender keberangkatan).
 * Tidak di-call dari DatabaseSeeder, jalankan manual saat demo.
 */
class DemoKeberangkatanSeeder extends Seeder
{
    public function run(): void
    {
        $travels = TravelCompany::query()
            ->whereIn('Penyelenggara', [
                'PT. Mataram Travel',
                'PT. Lombok Barat Travel',
                'PT. Lombok Timur Travel',
                'Sejahtera Travel',
            ])
            ->get()
            ->keyBy('Penyelenggara');

        if ($travels->isEmpty()) {
            $this->call(DevTravelSeeder::class);
            $travels = TravelCompany::query()->orderBy('id')->limit(4)->get()->keyBy('Penyelenggara');
        }

        $travelUser = User::query()->where('email', 'mataram.travel@phu.com')->first()
            ?? User::query()->where('role', 'user')->whereNotNull('travel_id')->first();

        $jamaahNames = [
            ['Ahmad Fauzi', 'umrah'],
            ['Siti Aminah', 'umrah'],
            ['Muhammad Rizki', 'umrah'],
            ['Nurul Aini', 'umrah'],
            ['Hasan Basri', 'haji'],
            ['Fatimah Zahra', 'haji'],
            ['Abdul Rahman', 'umrah'],
            ['Dewi Lestari', 'umrah'],
        ];

        $mataram = $travels->get('PT. Mataram Travel') ?? $travels->first();
        $jamaahIds = [];

        foreach ($jamaahNames as $i => [$nama, $jenis]) {
            $jamaah = Jamaah::updateOrCreate(
                [
                    'nik' => '5201'.str_pad((string) (9000000 + $i), 8, '0', STR_PAD_LEFT),
                ],
                [
                    'nama' => $nama,
                    'alamat' => 'Jl. Demo No. '.($i + 1).', Kota Mataram',
                    'nomor_hp' => '0812'.str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
                    'jenis_jamaah' => $jenis,
                    'travel_id' => $mataram->id,
                    'user_id' => $travelUser?->id,
                ]
            );
            $jamaahIds[] = $jamaah->id;
        }

        $departures = [
            [
                'key' => 'DEMO-BAP-MTR-1',
                'travel' => 'PT. Mataram Travel',
                'datetime' => now()->addDays(5)->toDateString(),
                'returndate' => now()->addDays(14)->toDateString(),
                'people' => 4,
                'days' => 9,
                'price' => 32000000,
                'airlines' => 'Garuda Indonesia GA-908',
                'airlines2' => 'Garuda Indonesia GA-909',
                'jamaah_slice' => [0, 4],
            ],
            [
                'key' => 'DEMO-BAP-MTR-2',
                'travel' => 'PT. Mataram Travel',
                'datetime' => now()->addDays(18)->toDateString(),
                'returndate' => now()->addDays(30)->toDateString(),
                'people' => 2,
                'days' => 12,
                'price' => 45000000,
                'airlines' => 'Saudi Airlines SV-839',
                'airlines2' => 'Saudi Airlines SV-840',
                'jamaah_slice' => [4, 6],
            ],
            [
                'key' => 'DEMO-BAP-LBR',
                'travel' => 'PT. Lombok Barat Travel',
                'datetime' => now()->addDays(10)->toDateString(),
                'returndate' => now()->addDays(20)->toDateString(),
                'people' => 25,
                'days' => 10,
                'price' => 28000000,
                'airlines' => 'Lion Air JT-380',
                'airlines2' => 'Lion Air JT-381',
                'jamaah_slice' => null,
            ],
            [
                'key' => 'DEMO-BAP-LTM',
                'travel' => 'PT. Lombok Timur Travel',
                'datetime' => now()->addDays(25)->toDateString(),
                'returndate' => now()->addDays(35)->toDateString(),
                'people' => 18,
                'days' => 10,
                'price' => 29500000,
                'airlines' => 'Batik Air ID-7120',
                'airlines2' => 'Batik Air ID-7121',
                'jamaah_slice' => null,
            ],
            [
                'key' => 'DEMO-BAP-SJH',
                'travel' => 'Sejahtera Travel',
                'datetime' => now()->addDays(7)->toDateString(),
                'returndate' => now()->addDays(16)->toDateString(),
                'people' => 12,
                'days' => 9,
                'price' => 31000000,
                'airlines' => 'Garuda Indonesia GA-910',
                'airlines2' => 'Garuda Indonesia GA-911',
                'jamaah_slice' => null,
            ],
        ];

        $created = 0;

        foreach ($departures as $row) {
            $travel = $travels->get($row['travel']);
            if (! $travel) {
                continue;
            }

            $owner = User::query()
                ->where('travel_id', $travel->id)
                ->where('role', 'user')
                ->first()
                ?? $travelUser;

            $bap = BAP::updateOrCreate(
                ['nomor_surat' => $row['key']],
                [
                    'name' => $travel->Pimpinan ?: 'PIC Demo',
                    'jabatan' => 'Pimpinan Travel',
                    'ppiuname' => $travel->Penyelenggara,
                    'address_phone' => ($travel->alamat_kantor_baru ?: $travel->alamat_kantor_lama ?: '-').' / '.($travel->Telepon ?: '-'),
                    'kab_kota' => $travel->kab_kota,
                    'people' => $row['people'],
                    'days' => $row['days'],
                    'price' => $row['price'],
                    'datetime' => $row['datetime'],
                    'airlines' => $row['airlines'],
                    'returndate' => $row['returndate'],
                    'airlines2' => $row['airlines2'],
                    'status' => 'diterima',
                    'user_id' => $owner?->id,
                    'travel_token' => substr('t'.md5($row['key']), 0, 20),
                    'kanwil_token' => substr('k'.md5($row['key']), 0, 20),
                ]
            );

            if ($row['jamaah_slice'] !== null) {
                [$from, $to] = $row['jamaah_slice'];
                $bap->jamaah()->sync(array_slice($jamaahIds, $from, $to - $from));
            }

            $created++;
        }

        $this->command->info("Demo keberangkatan siap: {$created} BA diterima, ".count($jamaahIds).' jamaah demo (Mataram).');
        $this->command->line('Cek kalender: /keberangkatan (hanya status diterima).');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jamaah;
use App\Models\TravelCompany;
use Illuminate\Support\Facades\Hash;

class JamaahUmrahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all travel companies
        $travelCompanies = TravelCompany::all();
        
        if ($travelCompanies->isEmpty()) {
            $this->command->error('No travel companies found. Please run TravelCompanySeeder first.');
            return;
        }

        // Get travel users (users with role 'user')
        $travelUsers = \App\Models\User::where('role', 'user')->get();
        
        if ($travelUsers->isEmpty()) {
            $this->command->error('No travel users found. Please run TravelUserSeeder first.');
            return;
        }

        $jamaahData = [
            // Lombok Barat
            [
                'nama' => 'Ahmad Rizki',
                'nik' => '5201234567890001',
                'alamat' => 'Jl. Raya Gerung No. 45, Gerung, Lombok Barat',
                'nomor_hp' => '081234567890',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nik' => '5201234567890002',
                'alamat' => 'Jl. Lembar No. 23, Lembar, Lombok Barat',
                'nomor_hp' => '081234567891',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Muhammad Fadli',
                'nik' => '5201234567890003',
                'alamat' => 'Jl. Kediri No. 67, Kediri, Lombok Barat',
                'nomor_hp' => '081234567892',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Nurul Hidayati',
                'nik' => '5201234567890004',
                'alamat' => 'Jl. Narmada No. 89, Narmada, Lombok Barat',
                'nomor_hp' => '081234567893',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Abdul Rahman',
                'nik' => '5201234567890005',
                'alamat' => 'Jl. Sekotong No. 12, Sekotong, Lombok Barat',
                'nomor_hp' => '081234567894',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Lombok Tengah
            [
                'nama' => 'Budi Santoso',
                'nik' => '5202234567890001',
                'alamat' => 'Jl. Raya Praya No. 34, Praya, Lombok Tengah',
                'nomor_hp' => '081234567895',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Dewi Sartika',
                'nik' => '5202234567890002',
                'alamat' => 'Jl. Kopang No. 56, Kopang, Lombok Tengah',
                'nomor_hp' => '081234567896',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Rudi Hartono',
                'nik' => '5202234567890003',
                'alamat' => 'Jl. Batukliang No. 78, Batukliang, Lombok Tengah',
                'nomor_hp' => '081234567897',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Maya Indah',
                'nik' => '5202234567890004',
                'alamat' => 'Jl. Jonggat No. 90, Jonggat, Lombok Tengah',
                'nomor_hp' => '081234567898',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Eko Prasetyo',
                'nik' => '5202234567890005',
                'alamat' => 'Jl. Pringgarata No. 45, Pringgarata, Lombok Tengah',
                'nomor_hp' => '081234567899',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Lombok Timur
            [
                'nama' => 'Sukarno',
                'nik' => '5203234567890001',
                'alamat' => 'Jl. Raya Selong No. 67, Selong, Lombok Timur',
                'nomor_hp' => '081234567900',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Sri Wahyuni',
                'nik' => '5203234567890002',
                'alamat' => 'Jl. Labuhan Haji No. 89, Labuhan Haji, Lombok Timur',
                'nomor_hp' => '081234567901',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Hendra Gunawan',
                'nik' => '5203234567890003',
                'alamat' => 'Jl. Keruak No. 23, Keruak, Lombok Timur',
                'nomor_hp' => '081234567902',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Ratna Sari',
                'nik' => '5203234567890004',
                'alamat' => 'Jl. Sakra No. 45, Sakra, Lombok Timur',
                'nomor_hp' => '081234567903',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Agus Setiawan',
                'nik' => '5203234567890005',
                'alamat' => 'Jl. Terara No. 78, Terara, Lombok Timur',
                'nomor_hp' => '081234567904',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Sumbawa
            [
                'nama' => 'Bambang Sutejo',
                'nik' => '5204234567890001',
                'alamat' => 'Jl. Raya Sumbawa Besar No. 34, Sumbawa Besar',
                'nomor_hp' => '081234567905',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Siti Aminah',
                'nik' => '5204234567890002',
                'alamat' => 'Jl. Alas No. 56, Alas, Sumbawa',
                'nomor_hp' => '081234567906',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Dedi Kurniawan',
                'nik' => '5204234567890003',
                'alamat' => 'Jl. Utan No. 78, Utan, Sumbawa',
                'nomor_hp' => '081234567907',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Nina Marlina',
                'nik' => '5204234567890004',
                'alamat' => 'Jl. Moyo Hilir No. 90, Moyo Hilir, Sumbawa',
                'nomor_hp' => '081234567908',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Roni Prasetyo',
                'nik' => '5204234567890005',
                'alamat' => 'Jl. Moyo Hulu No. 12, Moyo Hulu, Sumbawa',
                'nomor_hp' => '081234567909',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Sumbawa Barat
            [
                'nama' => 'Joko Widodo',
                'nik' => '5205234567890001',
                'alamat' => 'Jl. Raya Taliwang No. 45, Taliwang, Sumbawa Barat',
                'nomor_hp' => '081234567910',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Iriana',
                'nik' => '5205234567890002',
                'alamat' => 'Jl. Seteluk No. 67, Seteluk, Sumbawa Barat',
                'nomor_hp' => '081234567911',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Gibran Rakabuming',
                'nik' => '5205234567890003',
                'alamat' => 'Jl. Brang Rea No. 89, Brang Rea, Sumbawa Barat',
                'nomor_hp' => '081234567912',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Kahiyang Ayu',
                'nik' => '5205234567890004',
                'alamat' => 'Jl. Poto Tano No. 23, Poto Tano, Sumbawa Barat',
                'nomor_hp' => '081234567913',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Kaesang Pangarep',
                'nik' => '5205234567890005',
                'alamat' => 'Jl. Sekongkang No. 56, Sekongkang, Sumbawa Barat',
                'nomor_hp' => '081234567914',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Dompu
            [
                'nama' => 'Ahmad Dahlan',
                'nik' => '5206234567890001',
                'alamat' => 'Jl. Raya Dompu No. 78, Dompu',
                'nomor_hp' => '081234567915',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Fatimah Azzahra',
                'nik' => '5206234567890002',
                'alamat' => 'Jl. Woja No. 90, Woja, Dompu',
                'nomor_hp' => '081234567916',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Muhammad Rizki',
                'nik' => '5206234567890003',
                'alamat' => 'Jl. Pajo No. 12, Pajo, Dompu',
                'nomor_hp' => '081234567917',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Aisyah Putri',
                'nik' => '5206234567890004',
                'alamat' => 'Jl. Kilo No. 34, Kilo, Dompu',
                'nomor_hp' => '081234567918',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Abdullah Rahman',
                'nik' => '5206234567890005',
                'alamat' => 'Jl. Kempo No. 56, Kempo, Dompu',
                'nomor_hp' => '081234567919',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Bima
            [
                'nama' => 'Sultan Hasanuddin',
                'nik' => '5207234567890001',
                'alamat' => 'Jl. Raya Woha No. 67, Woha, Bima',
                'nomor_hp' => '081234567920',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Ratu Aisyah',
                'nik' => '5207234567890002',
                'alamat' => 'Jl. Belo No. 89, Belo, Bima',
                'nomor_hp' => '081234567921',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Muhammad Ali',
                'nik' => '5207234567890003',
                'alamat' => 'Jl. Palibelo No. 23, Palibelo, Bima',
                'nomor_hp' => '081234567922',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Nurul Hidayah',
                'nik' => '5207234567890004',
                'alamat' => 'Jl. Wawo No. 45, Wawo, Bima',
                'nomor_hp' => '081234567923',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'nik' => '5207234567890005',
                'alamat' => 'Jl. Sape No. 78, Sape, Bima',
                'nomor_hp' => '081234567924',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Kota Mataram
            [
                'nama' => 'Budi Santoso',
                'nik' => '5271234567890001',
                'alamat' => 'Jl. Pejanggik No. 34, Mataram',
                'nomor_hp' => '081234567925',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nik' => '5271234567890002',
                'alamat' => 'Jl. Selaparang No. 56, Mataram',
                'nomor_hp' => '081234567926',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Rudi Hartono',
                'nik' => '5271234567890003',
                'alamat' => 'Jl. Sandubaya No. 78, Mataram',
                'nomor_hp' => '081234567927',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Maya Indah',
                'nik' => '5271234567890004',
                'alamat' => 'Jl. Cakranegara No. 90, Mataram',
                'nomor_hp' => '081234567928',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Eko Prasetyo',
                'nik' => '5271234567890005',
                'alamat' => 'Jl. Ampenan No. 12, Mataram',
                'nomor_hp' => '081234567929',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            // Kota Bima
            [
                'nama' => 'Sultan Abdul',
                'nik' => '5272234567890001',
                'alamat' => 'Jl. Soekarno-Hatta No. 45, Bima',
                'nomor_hp' => '081234567930',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Ratu Fatimah',
                'nik' => '5272234567890002',
                'alamat' => 'Jl. Rasanae Timur No. 67, Bima',
                'nomor_hp' => '081234567931',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Muhammad Rizki',
                'nik' => '5272234567890003',
                'alamat' => 'Jl. Rasanae Barat No. 89, Bima',
                'nomor_hp' => '081234567932',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Nurul Aisyah',
                'nik' => '5272234567890004',
                'alamat' => 'Jl. Asakota No. 23, Bima',
                'nomor_hp' => '081234567933',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
            [
                'nama' => 'Ahmad Fadli',
                'nik' => '5272234567890005',
                'alamat' => 'Jl. Mpunda No. 56, Bima',
                'nomor_hp' => '081234567934',
                'jenis_jamaah' => 'umrah',
                'travel_id' => null,
            ],
        ];

        $createdCount = 0;
        $skippedCount = 0;
        
        // Map kabupaten to travel companies
        $kabupatenTravelMap = [];
        foreach ($travelCompanies as $travel) {
            $kabupatenTravelMap[$travel->kab_kota] = $travel;
        }
        
        foreach ($jamaahData as $jamaahInfo) {
            // Find travel company based on kabupaten from address
            $kabupaten = $this->extractKabupatenFromAddress($jamaahInfo['alamat']);
            $travelCompany = $kabupatenTravelMap[$kabupaten] ?? null;
            
            if (!$travelCompany) {
                $this->command->warn("Travel company not found for kabupaten: " . $kabupaten);
                $skippedCount++;
                continue;
            }
            
            // Set travel_id
            $jamaahInfo['travel_id'] = $travelCompany->id;
            
            // Find travel user for this travel company
            $travelUser = $travelUsers->where('travel_id', $travelCompany->id)->first();
            if (!$travelUser) {
                $this->command->warn("Travel user not found for travel company: " . $travelCompany->Penyelenggara);
                $skippedCount++;
                continue;
            }
            
            // Set user_id
            $jamaahInfo['user_id'] = $travelUser->id;
            
            // Check if jamaah already exists
            $existingJamaah = Jamaah::where('nik', $jamaahInfo['nik'])->first();
            if ($existingJamaah) {
                $this->command->info("Jamaah already exists: " . $jamaahInfo['nama'] . " (NIK: " . $jamaahInfo['nik'] . ")");
                $skippedCount++;
                continue;
            }

            Jamaah::create($jamaahInfo);
            $createdCount++;
        }

        $this->command->info('Jamaah Umrah seeded successfully!');
        $this->command->info('Total jamaah created: ' . $createdCount);
        $this->command->info('Total jamaah skipped: ' . $skippedCount);
        $this->command->info('');
        $this->command->info('Jamaah Umrah created for NTB kabupaten/kota:');
        $this->command->info('- Lombok Barat: 5 jamaah');
        $this->command->info('- Lombok Tengah: 5 jamaah');
        $this->command->info('- Lombok Timur: 5 jamaah');
        $this->command->info('- Sumbawa: 5 jamaah');
        $this->command->info('- Sumbawa Barat: 5 jamaah');
        $this->command->info('- Dompu: 5 jamaah');
        $this->command->info('- Bima: 5 jamaah');
        $this->command->info('- Kota Mataram: 5 jamaah');
        $this->command->info('- Kota Bima: 5 jamaah');
        $this->command->info('Total: 45 jamaah umrah');
    }
    
    private function extractKabupatenFromAddress($address)
    {
        if (strpos($address, 'Lombok Barat') !== false) return 'Lombok Barat';
        if (strpos($address, 'Lombok Tengah') !== false) return 'Lombok Tengah';
        if (strpos($address, 'Lombok Timur') !== false) return 'Lombok Timur';
        if (strpos($address, 'Sumbawa') !== false && strpos($address, 'Barat') === false) return 'Sumbawa';
        if (strpos($address, 'Sumbawa Barat') !== false) return 'Sumbawa Barat';
        if (strpos($address, 'Dompu') !== false) return 'Dompu';
        if (strpos($address, 'Bima') !== false && strpos($address, 'Kota') === false) return 'Bima';
        if (strpos($address, 'Mataram') !== false) return 'Kota Mataram';
        if (strpos($address, 'Kota Bima') !== false) return 'Kota Bima';
        
        return null;
    }
}

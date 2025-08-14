<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengaduan;
use App\Models\TravelCompany;
use App\Models\User;

class PengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get travel companies and admin user
        $travelCompanies = TravelCompany::limit(5)->get();
        $adminUser = User::where('role', 'admin')->first();

        if ($travelCompanies->isEmpty() || !$adminUser) {
            $this->command->warn('Tidak dapat membuat data pengaduan: Travel companies atau admin user tidak ditemukan');
            return;
        }

        // Create sample pengaduan data
        $pengaduanData = [
            [
                'nama_pengadu' => 'Ahmad Fauzi',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Pembatalan keberangkatan mendadak tanpa pemberitahuan yang jelas. Jamaah sudah siap berangkat namun dibatalkan di detik terakhir.',
                'status' => 'pending',
                'berkas_aduan' => null,
            ],
            [
                'nama_pengadu' => 'Siti Nurhaliza',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Keterlambatan pengembalian uang deposit setelah pembatalan paket umrah. Sudah 2 bulan tidak ada kejelasan.',
                'status' => 'in_progress',
                'berkas_aduan' => null,
                'processed_by' => $adminUser->id,
                'admin_notes' => 'Sedang menunggu konfirmasi dari travel terkait proses pengembalian dana.',
            ],
            [
                'nama_pengadu' => 'Muhammad Rizki',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Fasilitas hotel tidak sesuai dengan yang dijanjikan dalam brosur. Hotel yang diberikan berbeda lokasi dan kualitas.',
                'status' => 'completed',
                'berkas_aduan' => null,
                'processed_by' => $adminUser->id,
                'admin_notes' => 'Masalah telah diselesaikan. Travel memberikan kompensasi dan upgrade hotel untuk jamaah.',
                'completed_at' => now()->subDays(5),
            ],
            [
                'nama_pengadu' => 'Fatimah Zahra',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Pelayanan tour guide yang kurang profesional dan tidak informatif selama perjalanan umrah.',
                'status' => 'pending',
                'berkas_aduan' => null,
            ],
            [
                'nama_pengadu' => 'Abdullah Rahman',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Masalah transportasi dari hotel ke Masjidil Haram yang sering terlambat dan tidak nyaman.',
                'status' => 'in_progress',
                'berkas_aduan' => null,
                'processed_by' => $adminUser->id,
                'admin_notes' => 'Sedang dilakukan investigasi terhadap kualitas transportasi yang disediakan travel.',
            ],
            [
                'nama_pengadu' => 'Khadijah Amini',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Makanan yang disediakan tidak halal dan tidak sesuai dengan standar yang dijanjikan.',
                'status' => 'completed',
                'berkas_aduan' => null,
                'processed_by' => $adminUser->id,
                'admin_notes' => 'Travel telah meminta maaf dan memberikan jaminan sertifikasi halal untuk paket selanjutnya.',
                'completed_at' => now()->subDays(10),
            ],
            [
                'nama_pengadu' => 'Omar Sharif',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Ketidakjelasan informasi visa dan dokumen perjalanan yang menyebabkan keterlambatan keberangkatan.',
                'status' => 'rejected',
                'berkas_aduan' => null,
                'processed_by' => $adminUser->id,
                'admin_notes' => 'Setelah investigasi, ditemukan bahwa jamaah tidak melengkapi dokumen sesuai instruksi yang telah diberikan.',
            ],
            [
                'nama_pengadu' => 'Aisyah Putri',
                'travels_id' => $travelCompanies->random()->id,
                'hal_aduan' => 'Biaya tambahan yang tidak dijelaskan di awal kontrak dan muncul saat perjalanan.',
                'status' => 'pending',
                'berkas_aduan' => null,
            ],
        ];

        foreach ($pengaduanData as $data) {
            Pengaduan::create($data);
        }

        $this->command->info('Data pengaduan berhasil dibuat!');
    }
}
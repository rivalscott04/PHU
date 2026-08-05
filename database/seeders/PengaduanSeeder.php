<?php

namespace Database\Seeders;

use App\Models\Pengaduan;
use App\Models\TravelCompany;
use App\Models\User;
use App\Services\WorkQueueService;
use Illuminate\Database\Seeder;

class PengaduanSeeder extends Seeder
{
    public function run(): void
    {
        $travels = TravelCompany::query()->orderBy('id')->get()->keyBy('kab_kota');
        $adminUser = User::where('role', 'admin')->first();

        if ($travels->isEmpty() || ! $adminUser) {
            $this->command->warn('Tidak dapat membuat data pengaduan: travel atau admin tidak ditemukan');

            return;
        }

        if (Pengaduan::query()->exists()) {
            $this->command->info('Pengaduan seeder dilewati: data sudah ada.');

            return;
        }

        $created = 0;
        $queue = app(WorkQueueService::class);

        foreach ($this->catalogue() as $row) {
            $travel = $travels->get($row['kab_kota']) ?? $travels->first();

            $pengaduan = Pengaduan::create([
                'nama_pengadu' => $row['nama_pengadu'],
                'travels_id' => $travel->id,
                'hal_aduan' => $row['hal_aduan'],
                'status' => $row['status'],
                'berkas_aduan' => null,
                'processed_by' => in_array($row['status'], ['in_progress', 'completed', 'rejected'], true)
                    ? $adminUser->id
                    : null,
                'admin_notes' => $row['admin_notes'] ?? null,
                'completed_at' => ($row['status'] ?? null) === 'completed'
                    ? now()->subDays($row['days_ago'] ?? 5)
                    : null,
                'created_at' => now()->subDays($row['days_ago'] ?? 3),
                'updated_at' => now()->subDays(max(0, ($row['days_ago'] ?? 3) - 1)),
            ]);

            // Pending & in_progress masuk antrian (mirip flow submit nyata).
            if (in_array($pengaduan->status, ['pending', 'in_progress'], true)) {
                $queue->handlePengaduanCreated($pengaduan);
            }

            $created++;
        }

        $this->command->info("Data pengaduan berhasil dibuat ({$created} record, tersebar ke {$travels->count()} travel).");
    }

    /**
     * @return list<array{
     *     kab_kota: string,
     *     nama_pengadu: string,
     *     hal_aduan: string,
     *     status: string,
     *     days_ago?: int,
     *     admin_notes?: string
     * }>
     */
    private function catalogue(): array
    {
        return [
            // Lombok Barat — beberapa pengaduan (rating turun)
            [
                'kab_kota' => 'Lombok Barat',
                'nama_pengadu' => 'Ahmad Fauzi',
                'hal_aduan' => 'Pembatalan keberangkatan mendadak tanpa pemberitahuan yang jelas. Jamaah sudah siap berangkat namun dibatalkan di detik terakhir.',
                'status' => 'pending',
                'days_ago' => 2,
            ],
            [
                'kab_kota' => 'Lombok Barat',
                'nama_pengadu' => 'Siti Nurhaliza',
                'hal_aduan' => 'Keterlambatan pengembalian uang deposit setelah pembatalan paket umrah. Sudah 2 bulan tidak ada kejelasan.',
                'status' => 'in_progress',
                'days_ago' => 18,
                'admin_notes' => 'Sedang menunggu konfirmasi dari travel terkait proses pengembalian dana.',
            ],
            [
                'kab_kota' => 'Lombok Barat',
                'nama_pengadu' => 'Aisyah Putri',
                'hal_aduan' => 'Biaya tambahan yang tidak dijelaskan di awal kontrak dan muncul saat perjalanan.',
                'status' => 'pending',
                'days_ago' => 5,
            ],
            [
                'kab_kota' => 'Lombok Barat',
                'nama_pengadu' => 'Rudi Hartono',
                'hal_aduan' => 'Jadwal manasik diubah berulang tanpa konfirmasi tertulis kepada jamaah.',
                'status' => 'completed',
                'days_ago' => 40,
                'admin_notes' => 'Travel telah mengirim surat klarifikasi dan jadwal resmi terbaru.',
            ],

            // Lombok Tengah — sedikit
            [
                'kab_kota' => 'Lombok Tengah',
                'nama_pengadu' => 'Dewi Sartika',
                'hal_aduan' => 'Informasi hotel di Madinah berbeda dari brosur, mesin AC sering mati.',
                'status' => 'in_progress',
                'days_ago' => 9,
                'admin_notes' => 'Menunggu laporan perbaikan dari travel.',
            ],
            [
                'kab_kota' => 'Lombok Tengah',
                'nama_pengadu' => 'Eko Prasetyo',
                'hal_aduan' => 'Pendamping tidak siap saat jamaah lanjut usia membutuhkan bantuan kursi roda.',
                'status' => 'completed',
                'days_ago' => 55,
                'admin_notes' => 'Travel menempatkan pendamping tambahan pada keberangkatan berikutnya.',
            ],

            // Lombok Timur
            [
                'kab_kota' => 'Lombok Timur',
                'nama_pengadu' => 'Fatimah Zahra',
                'hal_aduan' => 'Pelayanan tour guide yang kurang profesional dan tidak informatif selama perjalanan umrah.',
                'status' => 'pending',
                'days_ago' => 4,
            ],
            [
                'kab_kota' => 'Lombok Timur',
                'nama_pengadu' => 'Hendra Gunawan',
                'hal_aduan' => 'Koper jamaah hilang di transit dan travel lambat mengurus klaim airline.',
                'status' => 'rejected',
                'days_ago' => 25,
                'admin_notes' => 'Setelah investigasi, klaim airline sudah diproses; dokumen jamaah tidak lengkap saat lapor.',
            ],

            // Sumbawa — banyak (rating lebih rendah)
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Abdullah Rahman',
                'hal_aduan' => 'Masalah transportasi dari hotel ke Masjidil Haram yang sering terlambat dan tidak nyaman.',
                'status' => 'in_progress',
                'days_ago' => 7,
                'admin_notes' => 'Sedang dilakukan investigasi terhadap kualitas transportasi.',
            ],
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Nina Marlina',
                'hal_aduan' => 'Makanan tidak sesuai komitmen paket; banyak keluhan keracunan ringan.',
                'status' => 'pending',
                'days_ago' => 3,
            ],
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Bambang Sutejo',
                'hal_aduan' => 'Visa keluar terlambat seminggu dari janji, keluarga sudah cuti kerja.',
                'status' => 'pending',
                'days_ago' => 1,
            ],
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Siti Aminah',
                'hal_aduan' => 'Pembayaran cicilan tidak tercatat, travel meminta bayar ulang.',
                'status' => 'in_progress',
                'days_ago' => 12,
                'admin_notes' => 'Rekonsiliasi bukti transfer sedang berjalan.',
            ],
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Dedi Kurniawan',
                'hal_aduan' => 'Hotel overlapping: jamaah digeser ke hotel lebih jauh tanpa kompensasi.',
                'status' => 'completed',
                'days_ago' => 30,
                'admin_notes' => 'Travel memberikan refund sebagian dan surat permintaan maaf.',
            ],
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Roni Prasetyo',
                'hal_aduan' => 'Tidak ada penjelasan asuransi perjalanan saat signing kontrak.',
                'status' => 'pending',
                'days_ago' => 6,
            ],
            [
                'kab_kota' => 'Sumbawa',
                'nama_pengadu' => 'Maya Indah',
                'hal_aduan' => 'Pembimbing tidak hadir di airport keberangkatan.',
                'status' => 'completed',
                'days_ago' => 48,
                'admin_notes' => 'SOP keberangkatan sudah diperketat oleh travel.',
            ],

            // Sumbawa Barat
            [
                'kab_kota' => 'Sumbawa Barat',
                'nama_pengadu' => 'Omar Sharif',
                'hal_aduan' => 'Ketidakjelasan informasi visa dan dokumen perjalanan yang menyebabkan keterlambatan keberangkatan.',
                'status' => 'rejected',
                'days_ago' => 20,
                'admin_notes' => 'Jamaah tidak melengkapi dokumen sesuai instruksi yang telah diberikan.',
            ],
            [
                'kab_kota' => 'Sumbawa Barat',
                'nama_pengadu' => 'Kahiyang Ayu',
                'hal_aduan' => 'Kursi pesawat tidak sesuai permintaan special meal untuk lansia.',
                'status' => 'pending',
                'days_ago' => 8,
            ],

            // Dompu — bersih hampir (rating tinggi)
            [
                'kab_kota' => 'Dompu',
                'nama_pengadu' => 'Khadijah Amini',
                'hal_aduan' => 'Makanan yang disediakan sempat diragukan sertifikasi halalnya.',
                'status' => 'completed',
                'days_ago' => 60,
                'admin_notes' => 'Travel memberikan jaminan sertifikasi halal vendor katering.',
            ],

            // Bima
            [
                'kab_kota' => 'Bima',
                'nama_pengadu' => 'Muhammad Rizki',
                'hal_aduan' => 'Fasilitas hotel tidak sesuai dengan yang dijanjikan dalam brosur.',
                'status' => 'completed',
                'days_ago' => 15,
                'admin_notes' => 'Travel memberikan kompensasi dan upgrade hotel.',
            ],
            [
                'kab_kota' => 'Bima',
                'nama_pengadu' => 'Nurul Hidayah',
                'hal_aduan' => 'Penjemputan bandara Jeddah molor 4 jam tanpa update.',
                'status' => 'in_progress',
                'days_ago' => 5,
                'admin_notes' => 'Travel diminta kirim laporan kronologi dan SOP penjemputan.',
            ],

            // Kota Mataram
            [
                'kab_kota' => 'Kota Mataram',
                'nama_pengadu' => 'Budi Santoso',
                'hal_aduan' => 'Customer service sulit dihubungi saat ada pertanyaan jadwal manasik.',
                'status' => 'pending',
                'days_ago' => 3,
            ],
            [
                'kab_kota' => 'Kota Mataram',
                'nama_pengadu' => 'Sri Wahyuni',
                'hal_aduan' => 'Perubahan hotel Mekkah diberitahu H-1 lewat WhatsApp grup saja.',
                'status' => 'in_progress',
                'days_ago' => 11,
                'admin_notes' => 'Travel diminta kirim surat resmi ke seluruh jamaah.',
            ],
            [
                'kab_kota' => 'Kota Mataram',
                'nama_pengadu' => 'Agus Setiawan',
                'hal_aduan' => 'Double booking kamar untuk 2 keluarga berbeda di hotel yang sama.',
                'status' => 'completed',
                'days_ago' => 35,
                'admin_notes' => 'Masalah sudah selesai dengan relokasi kamar.',
            ],

            // Kota Bima — zero active issues ideal (cukup 1 completed lama)
            [
                'kab_kota' => 'Kota Bima',
                'nama_pengadu' => 'Sultan Abdul',
                'hal_aduan' => 'Salah cetak nama di tiket; sudah diperbaiki sebelum keberangkatan.',
                'status' => 'completed',
                'days_ago' => 90,
                'admin_notes' => 'Travel mengganti tiket tanpa biaya tambahan.',
            ],
        ];
    }
}

}
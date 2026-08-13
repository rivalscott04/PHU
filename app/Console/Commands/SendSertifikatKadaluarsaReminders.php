<?php

namespace App\Console\Commands;

use App\Models\Sertifikat;
use App\Models\User;
use App\Notifications\V2\SertifikatKadaluarsaNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Sertifikat PPIU berakhir setiap 1 Januari. Perintah ini dijalankan harian dan
 * hanya bekerja pada hari kedaluwarsanya, lalu memberi tahu akun pemilik
 * sertifikat bahwa masa berlakunya habis.
 *
 * Dijalankan harian, bukan hanya sekali di 1 Januari, supaya server yang mati
 * atau cron yang terlewat pada hari itu tetap menyusul mengirimkannya. Kolom
 * reminder_kadaluarsa_at menjaga agar tidak terkirim dua kali.
 */
class SendSertifikatKadaluarsaReminders extends Command
{
    protected $signature = 'sertifikat:reminder-kadaluarsa
                            {--pada= : Jalankan seolah hari ini tanggal tersebut, format Y-m-d}';

    protected $description = 'Ingatkan pemilik sertifikat PPIU yang masa berlakunya berakhir';

    public function handle(NotificationService $notificationService): int
    {
        $hariIni = $this->option('pada')
            ? \Carbon\Carbon::parse($this->option('pada'))
            : now();

        $terkirim = 0;

        Sertifikat::query()
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', $hariIni->toDateString())
            ->whereNull('reminder_kadaluarsa_at')
            ->with(['travel', 'cabang'])
            ->chunkById(50, function ($daftar) use ($notificationService, $hariIni, &$terkirim) {
                foreach ($daftar as $sertifikat) {
                    $penerima = $this->pemilikAkun($sertifikat);

                    foreach ($penerima as $user) {
                        $notificationService->safeNotify(
                            $user,
                            new SertifikatKadaluarsaNotification($sertifikat)
                        );
                        $terkirim++;
                    }

                    // Ditandai walaupun tidak ada akun penerima, supaya
                    // sertifikat tanpa PIC tidak diperiksa ulang tiap hari.
                    $sertifikat->forceFill(['reminder_kadaluarsa_at' => $hariIni])->save();
                }
            });

        $this->info("Pengingat kedaluwarsa sertifikat terkirim: {$terkirim}");

        return self::SUCCESS;
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    private function pemilikAkun(Sertifikat $sertifikat)
    {
        return User::query()
            ->where('role', 'user')
            ->when(
                $sertifikat->cabang_id,
                fn ($query) => $query->where('cabang_id', $sertifikat->cabang_id),
                fn ($query) => $query->where('travel_id', $sertifikat->travel_id)->whereNull('cabang_id')
            )
            ->get();
    }
}

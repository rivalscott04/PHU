<?php

namespace App\Notifications\V2;

use App\Models\Sertifikat;

class SertifikatKadaluarsaNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly Sertifikat $sertifikat,
    ) {}

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $tanggal = $this->sertifikat->tanggal_kadaluarsa?->format('d/m/Y');

        return [
            'title' => 'Masa Berlaku Sertifikat Berakhir',
            'message' => "Sertifikat PPIU {$this->sertifikat->nomor_surat} berakhir masa berlakunya "
                . "pada {$tanggal}. Ajukan perpanjangan ke Kanwil agar sertifikat Anda tetap berlaku.",
            'module' => 'sertifikat',
            'action' => 'expired',
            'url' => $this->actionUrl('travel.certificates'),
            'meta' => [
                'sertifikat_id' => $this->sertifikat->id,
                'nomor_surat' => $this->sertifikat->nomor_surat,
                'tanggal_kadaluarsa' => $this->sertifikat->tanggal_kadaluarsa?->toDateString(),
            ],
        ];
    }
}

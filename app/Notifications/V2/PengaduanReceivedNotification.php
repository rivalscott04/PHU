<?php

namespace App\Notifications\V2;

use App\Models\Pengaduan;

class PengaduanReceivedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly Pengaduan $pengaduan,
    ) {
    }

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $travel = $this->pengaduan->travel;

        return [
            'title' => 'Pengaduan Baru',
            'message' => "Pengaduan dari {$this->pengaduan->nama_pengadu} terhadap {$travel?->Penyelenggara}.",
            'module' => 'antrian',
            'action' => 'pengaduan',
            'url' => $this->actionUrl('v2.antrian.index', ['type' => 'pengaduan']),
            'meta' => [
                'pengaduan_id' => $this->pengaduan->id,
                'travel_id' => $this->pengaduan->travels_id,
            ],
        ];
    }
}

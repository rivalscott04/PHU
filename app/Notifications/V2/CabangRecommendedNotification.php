<?php

namespace App\Notifications\V2;

use App\Models\CabangTravel;

class CabangRecommendedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly CabangTravel $cabang,
    ) {}

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        return [
            'title' => 'Rekomendasi Cabang Masuk',
            'message' => "{$this->cabang->Penyelenggara} sudah direkomendasikan {$this->cabang->kabupaten} dan menunggu keputusan Kanwil.",
            'module' => 'travel',
            'action' => 'recommended',
            'url' => $this->actionUrl('cabang.travel', ['filter' => 'menunggu_kanwil']),
            'meta' => [
                'cabang_id' => $this->cabang->id_cabang,
                'kabupaten' => $this->cabang->kabupaten,
            ],
        ];
    }
}

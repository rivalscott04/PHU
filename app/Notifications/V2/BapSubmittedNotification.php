<?php

namespace App\Notifications\V2;

use App\Models\BAP;

class BapSubmittedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly BAP $bap,
    ) {}

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        return [
            'title' => 'BA Pemberangkatan Baru',
            'message' => "{$this->bap->ppiuname} mengajukan BA Pemberangkatan dan menunggu verifikasi.",
            'module' => 'bap',
            'action' => 'submitted',
            'url' => $this->actionUrl('bap'),
            'meta' => [
                'bap_id' => $this->bap->id,
                'kabupaten' => $this->bap->kab_kota,
            ],
        ];
    }
}

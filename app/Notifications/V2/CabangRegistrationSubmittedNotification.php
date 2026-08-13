<?php

namespace App\Notifications\V2;

use App\Models\CabangTravel;

class CabangRegistrationSubmittedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly CabangTravel $cabang,
    ) {}

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        return [
            'title' => 'Registrasi Cabang Baru',
            'message' => "{$this->cabang->Penyelenggara} mendaftar sebagai cabang dan menunggu peninjauan Kabupaten/Kota.",
            'module' => 'travel',
            'action' => 'registered',
            'url' => $this->actionUrl('cabang.travel', ['filter' => 'pending']),
            'meta' => [
                'cabang_id' => $this->cabang->id_cabang,
                'kabupaten' => $this->cabang->kabupaten,
            ],
        ];
    }
}

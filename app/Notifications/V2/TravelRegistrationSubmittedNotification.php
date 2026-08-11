<?php

namespace App\Notifications\V2;

use App\Models\TravelCompany;

class TravelRegistrationSubmittedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly TravelCompany $travel,
    ) {}

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        return [
            'title' => 'Registrasi Travel Baru',
            'message' => "{$this->travel->Penyelenggara} mendaftar dan menunggu verifikasi Kanwil.",
            'module' => 'travel',
            'action' => 'registered',
            'url' => $this->actionUrl('travel', ['filter' => 'pending']),
            'meta' => [
                'travel_id' => $this->travel->id,
                'kabupaten' => $this->travel->kab_kota,
            ],
        ];
    }
}

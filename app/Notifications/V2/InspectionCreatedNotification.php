<?php

namespace App\Notifications\V2;

use App\Models\Inspection;

class InspectionCreatedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly Inspection $inspection,
    ) {
    }

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $travel = $this->inspection->travel;

        return [
            'title' => 'Pengawasan Baru',
            'message' => "Pengawasan {$this->inspection->inspection_no} dijadwalkan untuk {$travel?->Penyelenggara}.",
            'module' => 'pengawasan',
            'action' => 'created',
            'url' => route('v2.pengawasan.show', $this->inspection),
            'meta' => [
                'inspection_id' => $this->inspection->id,
                'travel_id' => $this->inspection->travel_id,
            ],
        ];
    }
}

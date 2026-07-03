<?php

namespace App\Notifications\V2;

use App\Models\Followup;

class FollowupUploadedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly Followup $followup,
    ) {
    }

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $finding = $this->followup->finding;
        $travel = $finding?->inspection?->travel;

        return [
            'title' => 'Bukti Tindak Lanjut Diunggah',
            'message' => "{$travel?->Penyelenggara} mengunggah bukti tindak lanjut untuk temuan \"{$finding?->title}\".",
            'module' => 'followup',
            'action' => 'uploaded',
            'url' => $this->actionUrl('v2.followup.show', $this->followup),
            'meta' => [
                'followup_id' => $this->followup->id,
                'finding_id' => $finding?->id,
                'travel_id' => $travel?->id,
            ],
        ];
    }
}

<?php

namespace App\Notifications\V2;

use App\Models\Followup;

class FollowupApprovedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly Followup $followup,
    ) {
    }

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $finding = $this->followup->finding;

        return [
            'title' => 'Tindak Lanjut Diverifikasi',
            'message' => "Tindak lanjut untuk temuan \"{$finding?->title}\" telah disetujui.",
            'module' => 'followup',
            'action' => 'approved',
            'url' => route('v2.followup.show', $this->followup),
            'meta' => [
                'followup_id' => $this->followup->id,
                'finding_id' => $finding?->id,
            ],
        ];
    }
}

<?php

namespace App\Notifications\V2;

use App\Models\Followup;

class FollowupRevisionNotification extends V2DatabaseNotification
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
            'title' => 'Revisi Tindak Lanjut Diminta',
            'message' => "Pengawas meminta revisi untuk temuan \"{$finding?->title}\".",
            'module' => 'followup',
            'action' => 'revision',
            'url' => $this->actionUrl('v2.followup.show', $this->followup),
            'meta' => [
                'followup_id' => $this->followup->id,
                'finding_id' => $finding?->id,
            ],
        ];
    }
}

<?php

namespace App\Notifications\V2;

class RegistrationRevisionRequestedNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly string $travelName,
        private readonly string $notes,
        private readonly bool $isCabang = false,
    ) {}

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $jenis = $this->isCabang ? 'cabang' : 'travel';

        return [
            'title' => 'Perlu Perbaikan Pendaftaran',
            'message' => "Pendaftaran {$jenis} {$this->travelName} perlu diperbaiki. {$this->notes}",
            'module' => 'travel',
            'action' => 'revision_requested',
            'url' => $this->actionUrl('registration.revision.edit'),
            'meta' => [
                'notes' => $this->notes,
            ],
        ];
    }
}

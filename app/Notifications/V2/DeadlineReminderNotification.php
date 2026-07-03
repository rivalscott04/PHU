<?php

namespace App\Notifications\V2;

use App\Models\InspectionFinding;

class DeadlineReminderNotification extends V2DatabaseNotification
{
    public function __construct(
        private readonly InspectionFinding $finding,
        private readonly string $reminderType,
    ) {
    }

    /** @return array<string, mixed> */
    protected function payload(object $notifiable): array
    {
        $travel = $this->finding->inspection?->travel;
        $deadline = $this->finding->deadline?->format('d/m/Y');

        return [
            'title' => $this->title(),
            'message' => $this->message($travel?->Penyelenggara, $deadline),
            'module' => 'followup',
            'action' => 'reminder',
            'url' => $this->actionUrl('v2.pengawasan.show', $this->finding->inspection_id),
            'meta' => [
                'finding_id' => $this->finding->id,
                'travel_id' => $travel?->id,
                'reminder_type' => $this->reminderType,
                'deadline' => $this->finding->deadline?->toDateString(),
            ],
        ];
    }

    private function title(): string
    {
        return match ($this->reminderType) {
            'h_minus_7' => 'Reminder Deadline (H-7)',
            'h_minus_3' => 'Reminder Deadline (H-3)',
            'h_day' => 'Reminder Deadline (Hari H)',
            'h_plus_7' => 'Deadline Terlewati (H+7)',
            'h_plus_30' => 'Deadline Terlewati (H+30)',
            'overdue' => 'Deadline Terlewati',
            default => 'Reminder Deadline',
        };
    }

    private function message(?string $travelName, ?string $deadline): string
    {
        $subject = "temuan \"{$this->finding->title}\"";

        return match ($this->reminderType) {
            'h_minus_7' => "Deadline {$subject} ({$travelName}) jatuh tempo dalam 7 hari ({$deadline}).",
            'h_minus_3' => "Deadline {$subject} ({$travelName}) jatuh tempo dalam 3 hari ({$deadline}).",
            'h_day' => "Deadline {$subject} ({$travelName}) jatuh tempo hari ini ({$deadline}).",
            'h_plus_7', 'h_plus_30', 'overdue' => "Deadline {$subject} ({$travelName}) telah terlewati ({$deadline}).",
            default => "Perhatian deadline {$subject} ({$travelName}) pada {$deadline}.",
        };
    }
}

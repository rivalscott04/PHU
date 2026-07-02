<?php

namespace App\Console\Commands;

use App\Enums\FindingStatus;
use App\Models\InspectionFinding;
use App\Notifications\V2\DeadlineReminderNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    protected $signature = 'followup:send-deadline-reminders';

    protected $description = 'Kirim reminder deadline tindak lanjut (H-7, H-3, H, H+7, H+30, terlambat)';

    /** @var array<int, string> */
    private const REMINDER_DAYS = [
        7 => 'h_minus_7',
        3 => 'h_minus_3',
        0 => 'h_day',
        -7 => 'h_plus_7',
        -30 => 'h_plus_30',
    ];

    public function handle(NotificationService $notificationService): int
    {
        $sent = 0;

        InspectionFinding::query()
            ->whereNotIn('status', [
                FindingStatus::Closed->value,
                FindingStatus::Verified->value,
            ])
            ->whereNotNull('deadline')
            ->with('inspection.travel')
            ->chunkById(50, function ($findings) use ($notificationService, &$sent) {
                foreach ($findings as $finding) {
                    $sent += $this->dispatchReminder($finding, $notificationService);
                }
            });

        $this->info("Reminder deadline terkirim: {$sent}");

        return self::SUCCESS;
    }

    private function dispatchReminder(InspectionFinding $finding, NotificationService $notificationService): int
    {
        $travel = $finding->inspection?->travel;
        if (! $travel) {
            return 0;
        }

        $daysUntil = (int) now()->startOfDay()->diffInDays($finding->deadline->startOfDay(), false);
        $reminderType = self::REMINDER_DAYS[$daysUntil] ?? null;

        if ($reminderType === null) {
            if ($finding->deadline->isPast()) {
                return $this->notifySupervisorsOnce($finding, $notificationService, 'overdue');
            }

            return 0;
        }

        if (in_array($reminderType, ['h_plus_7', 'h_plus_30'], true)) {
            return $this->notifySupervisorsOnce($finding, $notificationService, $reminderType);
        }

        return $this->notifyTravelUsersOnce($finding, $notificationService, $reminderType);
    }

    private function notifyTravelUsersOnce(
        InspectionFinding $finding,
        NotificationService $notificationService,
        string $reminderType,
    ): int {
        $travel = $finding->inspection->travel;
        $notification = new DeadlineReminderNotification($finding, $reminderType);
        $sent = 0;

        foreach ($notificationService->usersForTravel($travel->id) as $user) {
            if ($notificationService->alreadySentToday($user, DeadlineReminderNotification::class, [
                'meta' => [
                    'finding_id' => $finding->id,
                    'reminder_type' => $reminderType,
                ],
            ])) {
                continue;
            }

            $user->notify($notification);
            $sent++;
        }

        return $sent;
    }

    private function notifySupervisorsOnce(
        InspectionFinding $finding,
        NotificationService $notificationService,
        string $reminderType,
    ): int {
        $travel = $finding->inspection->travel;
        $notification = new DeadlineReminderNotification($finding, $reminderType);
        $sent = 0;

        foreach ($notificationService->supervisorsForTravel($travel) as $user) {
            if ($notificationService->alreadySentToday($user, DeadlineReminderNotification::class, [
                'meta' => [
                    'finding_id' => $finding->id,
                    'reminder_type' => $reminderType,
                ],
            ])) {
                continue;
            }

            $user->notify($notification);
            $sent++;
        }

        return $sent;
    }
}

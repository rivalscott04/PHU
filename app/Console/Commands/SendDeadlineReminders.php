<?php

namespace App\Console\Commands;

use App\Enums\FindingStatus;
use App\Models\InspectionFinding;
use App\Models\TravelCompany;
use App\Models\User;
use App\Notifications\V2\DeadlineReminderNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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

    /** @var Collection<int, User>|null */
    private ?Collection $adminAndPengawas = null;

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
                $sent += $this->processChunk($findings, $notificationService);
            });

        $this->info("Reminder deadline terkirim: {$sent}");

        return self::SUCCESS;
    }

    /** @param  Collection<int, InspectionFinding>  $findings */
    private function processChunk(Collection $findings, NotificationService $notificationService): int
    {
        $travelIds = $findings
            ->map(fn (InspectionFinding $finding) => $finding->inspection?->travel_id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $usersByTravel = $notificationService->usersGroupedByTravel($travelIds);
        $adminAndPengawas = $this->adminAndPengawasUsers($notificationService);

        $recipientIds = $usersByTravel
            ->flatten()
            ->pluck('id')
            ->merge($adminAndPengawas->pluck('id'))
            ->unique()
            ->values();

        $sentKeys = $notificationService->deadlineReminderSentKeysToday($recipientIds);
        $sent = 0;

        foreach ($findings as $finding) {
            $sent += $this->dispatchReminder(
                $finding,
                $notificationService,
                $usersByTravel,
                $adminAndPengawas,
                $sentKeys,
            );
        }

        return $sent;
    }

    private function dispatchReminder(
        InspectionFinding $finding,
        NotificationService $notificationService,
        Collection $usersByTravel,
        Collection $adminAndPengawas,
        array &$sentKeys,
    ): int {
        $travel = $finding->inspection?->travel;
        if (! $travel) {
            return 0;
        }

        $daysUntil = (int) now()->startOfDay()->diffInDays($finding->deadline->startOfDay(), false);
        $reminderType = self::REMINDER_DAYS[$daysUntil] ?? null;

        if ($reminderType === null) {
            if ($finding->deadline->isPast()) {
                return $this->notifySupervisorsOnce(
                    $finding,
                    $notificationService,
                    'overdue',
                    $adminAndPengawas,
                    $sentKeys,
                );
            }

            return 0;
        }

        if (in_array($reminderType, ['h_plus_7', 'h_plus_30'], true)) {
            return $this->notifySupervisorsOnce(
                $finding,
                $notificationService,
                $reminderType,
                $adminAndPengawas,
                $sentKeys,
            );
        }

        return $this->notifyTravelUsersOnce(
            $finding,
            $notificationService,
            $reminderType,
            $usersByTravel->get($travel->id, collect()),
            $sentKeys,
        );
    }

    /** @param  Collection<int, User>  $travelUsers */
    private function notifyTravelUsersOnce(
        InspectionFinding $finding,
        NotificationService $notificationService,
        string $reminderType,
        Collection $travelUsers,
        array &$sentKeys,
    ): int {
        if ($travelUsers->isEmpty()) {
            return 0;
        }

        $notification = new DeadlineReminderNotification($finding, $reminderType);
        $sent = 0;

        foreach ($travelUsers as $user) {
            if ($notificationService->wasDeadlineReminderSentToday(
                $sentKeys,
                $user->id,
                $finding->id,
                $reminderType,
            )) {
                continue;
            }

            $user->notify($notification);
            $sentKeys["{$user->id}|{$finding->id}|{$reminderType}"] = true;
            $sent++;
        }

        return $sent;
    }

    private function notifySupervisorsOnce(
        InspectionFinding $finding,
        NotificationService $notificationService,
        string $reminderType,
        Collection $adminAndPengawas,
        array &$sentKeys,
    ): int {
        $travel = $finding->inspection?->travel;
        if (! $travel instanceof TravelCompany) {
            return 0;
        }

        $supervisors = $notificationService->supervisorsForTravelFromPool($travel, $adminAndPengawas);
        if ($supervisors->isEmpty()) {
            return 0;
        }

        $notification = new DeadlineReminderNotification($finding, $reminderType);
        $sent = 0;

        foreach ($supervisors as $user) {
            if ($notificationService->wasDeadlineReminderSentToday(
                $sentKeys,
                $user->id,
                $finding->id,
                $reminderType,
            )) {
                continue;
            }

            $user->notify($notification);
            $sentKeys["{$user->id}|{$finding->id}|{$reminderType}"] = true;
            $sent++;
        }

        return $sent;
    }

    /** @return Collection<int, User> */
    private function adminAndPengawasUsers(NotificationService $notificationService): Collection
    {
        return $this->adminAndPengawas ??= $notificationService->adminAndPengawasUsers();
    }
}

<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\TravelCompany;
use App\Models\User;
use App\Notifications\V2\DeadlineReminderNotification;
use App\Support\NtbKabupatenMap;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{
    /** @return Collection<int, User> */
    public function usersForTravel(int $travelId): Collection
    {
        return User::query()
            ->where('travel_id', $travelId)
            ->get();
    }

    /**
     * @param  list<int>  $travelIds
     * @return Collection<int, Collection<int, User>>
     */
    public function usersGroupedByTravel(array $travelIds): Collection
    {
        if ($travelIds === []) {
            return collect();
        }

        return User::query()
            ->whereIn('travel_id', $travelIds)
            ->get()
            ->groupBy('travel_id');
    }

    /** @return Collection<int, User> */
    public function adminAndPengawasUsers(): Collection
    {
        return User::query()
            ->whereIn('role', ['admin', 'pengawas'])
            ->get();
    }

    /**
     * @param  Collection<int, User>  $adminAndPengawas
     * @return Collection<int, User>
     */
    public function supervisorsForTravelFromPool(TravelCompany $travel, Collection $adminAndPengawas): Collection
    {
        return $adminAndPengawas
            ->filter(function (User $user) use ($travel): bool {
                if ($user->role === 'admin') {
                    return true;
                }

                return $user->role === 'pengawas'
                    && $user->canAccessKabupaten($travel->kab_kota);
            })
            ->values();
    }

    /** @return Collection<int, User> */
    public function supervisorsForTravel(TravelCompany $travel): Collection
    {
        return User::query()
            ->where(function ($query) use ($travel) {
                $query->where('role', 'admin')
                    ->orWhere(function ($scoped) use ($travel) {
                        $scoped->pengawasForKabupaten($travel->kab_kota);
                    });
            })
            ->get();
    }

    /**
     * Semua pihak yang harus menindaklanjuti kiriman travel: admin, pengawas
     * berwenang, dan petugas kabupaten wilayah travel tersebut.
     *
     * @return Collection<int, User>
     */
    public function reviewersForKabupaten(?string $kabupaten): Collection
    {
        $kabupatens = NtbKabupatenMap::queryValues($kabupaten);

        return User::query()
            ->where(function ($query) use ($kabupaten, $kabupatens) {
                $query->where('role', UserRole::Admin->value)
                    ->orWhere(fn ($scoped) => $scoped->pengawasForKabupaten($kabupaten));

                if ($kabupatens !== []) {
                    $query->orWhere(fn ($kab) => $kab
                        ->where('role', UserRole::Kabupaten->value)
                        ->whereIn('kabupaten', $kabupatens));
                }
            })
            ->get();
    }

    public function reviewersForTravel(TravelCompany $travel): Collection
    {
        return $this->reviewersForKabupaten($travel->kab_kota);
    }

    public function notifyReviewers(TravelCompany $travel, Notification $notification): void
    {
        $this->notifyReviewersInKabupaten($travel->kab_kota, $notification);
    }

    public function notifyReviewersInKabupaten(?string $kabupaten, Notification $notification): void
    {
        $this->reviewersForKabupaten($kabupaten)->each(
            fn (User $user) => $this->safeNotify($user, $notification)
        );
    }

    public function notifyAdmins(Notification $notification): void
    {
        User::query()
            ->where('role', UserRole::Admin->value)
            ->get()
            ->each(fn (User $user) => $this->safeNotify($user, $notification));
    }

    public function notifyTravelUsers(int $travelId, Notification $notification): void
    {
        $this->usersForTravel($travelId)->each(
            fn (User $user) => $this->safeNotify($user, $notification)
        );
    }

    public function notifySupervisors(TravelCompany $travel, Notification $notification): void
    {
        $this->supervisorsForTravel($travel)->each(
            fn (User $user) => $this->safeNotify($user, $notification)
        );
    }

    public function alreadySentToday(User $user, string $notificationClass, array $dataMatch = []): bool
    {
        $query = $user->notifications()
            ->where('type', $notificationClass)
            ->whereDate('created_at', today());

        foreach ($dataMatch as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    $query->where("data->{$key}->{$nestedKey}", $nestedValue);
                }
                continue;
            }

            $query->where("data->{$key}", $value);
        }

        return $query->exists();
    }

    /**
     * @param  iterable<int>  $userIds
     * @return array<string, true>
     */
    public function deadlineReminderSentKeysToday(iterable $userIds): array
    {
        $userIds = collect($userIds)->unique()->values()->all();

        if ($userIds === []) {
            return [];
        }

        $keys = [];

        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->whereIn('notifiable_id', $userIds)
            ->where('type', DeadlineReminderNotification::class)
            ->whereDate('created_at', today())
            ->select(['notifiable_id', 'data'])
            ->orderBy('id')
            ->lazy()
            ->each(function ($row) use (&$keys): void {
                $data = json_decode($row->data, true) ?? [];
                $findingId = $data['meta']['finding_id'] ?? null;
                $reminderType = $data['meta']['reminder_type'] ?? null;

                if ($findingId === null || $reminderType === null) {
                    return;
                }

                $keys["{$row->notifiable_id}|{$findingId}|{$reminderType}"] = true;
            });

        return $keys;
    }

    public function wasDeadlineReminderSentToday(
        array $sentKeys,
        int $userId,
        int $findingId,
        string $reminderType,
    ): bool {
        return isset($sentKeys["{$userId}|{$findingId}|{$reminderType}"]);
    }

    /**
     * Never let a broken Reverb/broadcast path block the main business action
     * (pengaduan, pengawasan, followup, etc.).
     */
    public function safeNotify(User $user, Notification $notification): void
    {
        try {
            $user->notify($notification);
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim notifikasi pengguna', [
                'user_id' => $user->id,
                'notification' => $notification::class,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

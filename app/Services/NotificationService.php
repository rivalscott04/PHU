<?php

namespace App\Services;

use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NotificationService
{
    /** @return Collection<int, User> */
    public function usersForTravel(int $travelId): Collection
    {
        return User::query()
            ->where('travel_id', $travelId)
            ->get();
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

    public function notifyTravelUsers(int $travelId, Notification $notification): void
    {
        $this->usersForTravel($travelId)->each(
            fn (User $user) => $user->notify($notification)
        );
    }

    public function notifySupervisors(TravelCompany $travel, Notification $notification): void
    {
        $this->supervisorsForTravel($travel)->each(
            fn (User $user) => $user->notify($notification)
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
}

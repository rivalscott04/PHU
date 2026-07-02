<?php

namespace App\Policies;

use App\Models\Followup;
use App\Models\User;

class FollowupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten', 'user'], true);
    }

    public function view(User $user, Followup $followup): bool
    {
        $travelId = $followup->finding?->inspection?->travel_id;

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'kabupaten') {
            return $followup->finding?->inspection?->travel?->kab_kota === $user->getKabupaten();
        }

        return $user->travel_id === $travelId;
    }

    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    public function update(User $user, Followup $followup): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'kabupaten') {
            return $followup->finding?->inspection?->travel?->kab_kota === $user->getKabupaten();
        }

        return $user->travel_id === $followup->finding?->inspection?->travel_id;
    }

    public function approve(User $user, Followup $followup): bool
    {
        return in_array($user->role, ['admin', 'kabupaten'], true)
            && $this->view($user, $followup);
    }
}

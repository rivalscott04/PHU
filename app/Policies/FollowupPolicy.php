<?php

namespace App\Policies;

use App\Enums\FollowupStatus;
use App\Models\Followup;
use App\Models\User;

class FollowupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas', 'user'], true);
    }

    public function view(User $user, Followup $followup): bool
    {
        $travelId = $followup->finding?->inspection?->travel_id;

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($followup->finding?->inspection?->travel?->kab_kota);
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

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($followup->finding?->inspection?->travel?->kab_kota);
        }

        return $user->travel_id === $followup->finding?->inspection?->travel_id;
    }

    public function approve(User $user, Followup $followup): bool
    {
        if (! in_array($user->role, ['admin', 'pengawas'], true) || ! $this->view($user, $followup)) {
            return false;
        }

        $status = $followup->status?->value ?? $followup->status;

        return in_array($status, [
            FollowupStatus::Submitted->value,
            FollowupStatus::Pending->value,
        ], true);
    }
}

<?php

namespace App\Policies;

use App\Models\Inspection;
use App\Models\User;

class InspectionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten', 'user'], true);
    }

    public function view(User $user, Inspection $inspection): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'kabupaten') {
            return $inspection->travel?->kab_kota === $user->getKabupaten();
        }

        return $user->travel_id === $inspection->travel_id
            && ! in_array($inspection->status?->value ?? $inspection->status, ['DRAFT', 'SCHEDULED'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten'], true);
    }

    public function update(User $user, Inspection $inspection): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'kabupaten') {
            return $inspection->travel?->kab_kota === $user->getKabupaten();
        }

        return false;
    }
}

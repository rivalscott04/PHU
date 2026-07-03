<?php

namespace App\Policies;

use App\Models\Inspection;
use App\Models\User;

class InspectionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas', 'user'], true);
    }

    public function view(User $user, Inspection $inspection): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($inspection->travel?->kab_kota);
        }

        return $user->travel_id === $inspection->travel_id
            && ! in_array($inspection->status?->value ?? $inspection->status, ['DRAFT', 'SCHEDULED'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas'], true);
    }

    public function update(User $user, Inspection $inspection): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($inspection->travel?->kab_kota);
        }

        return false;
    }
}

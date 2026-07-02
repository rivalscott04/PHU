<?php

namespace App\Policies;

use App\Models\TravelCompany;
use App\Models\User;

class CompliancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten', 'user'], true);
    }

    public function view(User $user, TravelCompany $travel): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'kabupaten') {
            return $travel->kab_kota === $user->getKabupaten();
        }

        return $user->travel_id === $travel->id;
    }
}

<?php

namespace App\Policies;

use App\Models\TravelCompany;
use App\Models\User;
use App\Support\NtbKabupatenMap;

class CompliancePolicy
{
    public function viewAny(User $user): bool
    {
        // PIC cabang tidak punya travel_id, jadi tidak punya profil kepatuhan sendiri.
        return in_array($user->role, ['admin', 'pengawas', 'kabupaten'], true)
            || ($user->role === 'user' && $user->travel_id);
    }

    public function view(User $user, TravelCompany $travel): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($travel->kab_kota);
        }

        if ($user->role === 'kabupaten') {
            return NtbKabupatenMap::matches($user->kabupaten, $travel->kab_kota);
        }

        return $user->travel_id === $travel->id;
    }
}

<?php

namespace App\Policies;

use App\Models\RiskScore;
use App\Models\User;

class RiskPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas', 'user'], true);
    }

    public function view(User $user, RiskScore $riskScore): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($riskScore->travel?->kab_kota);
        }

        return $user->travel_id === $riskScore->travel_id;
    }

    public function recalculate(User $user): bool
    {
        return $user->role === 'admin';
    }
}

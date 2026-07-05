<?php

namespace App\Policies;

use App\Models\TravelCompany;
use App\Models\User;

class MonitoringPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pimpinan', 'pengawas'], true);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function viewTravelPengaduan(User $user, TravelCompany $travel): bool
    {
        if (! $this->view($user)) {
            return false;
        }

        if (in_array($user->role, ['admin', 'pimpinan'], true)) {
            return true;
        }

        if ($user->role === 'pengawas') {
            return $user->canAccessKabupaten($travel->kab_kota);
        }

        return false;
    }
}

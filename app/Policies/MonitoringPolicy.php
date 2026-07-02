<?php

namespace App\Policies;

use App\Models\User;

class MonitoringPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten', 'user'], true);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }
}

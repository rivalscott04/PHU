<?php

namespace App\Policies;

use App\Models\SupervisionWorkQueue;
use App\Models\User;

class WorkQueuePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas'], true);
    }

    public function view(User $user, SupervisionWorkQueue $item): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'pengawas'
            && $user->canAccessKabupaten($item->kabupaten);
    }

    public function update(User $user, SupervisionWorkQueue $item): bool
    {
        return $this->view($user, $item) && $item->isActionable();
    }
}

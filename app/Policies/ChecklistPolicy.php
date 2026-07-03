<?php

namespace App\Policies;

use App\Models\Checklist;
use App\Models\User;

class ChecklistPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Checklist $checklist): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Checklist $checklist): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Checklist $checklist): bool
    {
        return $user->role === 'admin';
    }
}

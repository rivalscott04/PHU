<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pengawas'], true);
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'pengawas') {
            return false;
        }

        $actor = $auditLog->user;
        if (! $actor) {
            return false;
        }

        return $user->canAccessKabupaten($actor->kabupaten)
            || $user->canAccessKabupaten($actor->travel?->kab_kota);
    }
}

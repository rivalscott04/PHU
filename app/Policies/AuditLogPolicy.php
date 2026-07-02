<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten'], true);
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'kabupaten') {
            return false;
        }

        $actor = $auditLog->user;
        if (! $actor) {
            return false;
        }

        $kabupaten = $user->getKabupaten();

        if ($actor->kabupaten === $kabupaten) {
            return true;
        }

        return $actor->travel?->kab_kota === $kabupaten;
    }
}

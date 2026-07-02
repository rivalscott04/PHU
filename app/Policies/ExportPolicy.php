<?php

namespace App\Policies;

use App\Models\User;

class ExportPolicy
{
    public function export(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten', 'user'], true);
    }

    public function exportDashboard(User $user): bool
    {
        return $this->export($user);
    }

    public function exportPengawasan(User $user): bool
    {
        return in_array($user->role, ['admin', 'kabupaten', 'user'], true);
    }
}

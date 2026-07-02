<?php

namespace App\Repositories;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AuditLogRepository
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->with('user.travel')
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?AuditLog
    {
        return AuditLog::with('user.travel')->find($id);
    }

  /** @return Builder<AuditLog> */
    private function baseQuery(array $filters): Builder
    {
        $query = AuditLog::query();

        if ($module = $filters['module'] ?? null) {
            $query->where('module', $module);
        }

        if ($action = $filters['action'] ?? null) {
            $query->where('action', $action);
        }

        if ($userId = $filters['user_id'] ?? null) {
            $query->where('user_id', $userId);
        }

        if ($from = $filters['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($search = $filters['q'] ?? null) {
            $query->where(function (Builder $scoped) use ($search) {
                $scoped->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        if ($kabupaten = $filters['kabupaten'] ?? null) {
            $query->where(function (Builder $scoped) use ($kabupaten) {
                $scoped->whereHas('user', function (Builder $userQuery) use ($kabupaten) {
                    $userQuery->where('kabupaten', $kabupaten)
                        ->orWhereHas('travel', fn (Builder $travelQuery) => $travelQuery->where('kab_kota', $kabupaten));
                });
            });
        }

        return $query;
    }
}

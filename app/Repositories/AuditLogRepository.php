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
                        $userQuery->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('nomor_hp', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['kabupaten']) || ! empty($filters['kabupatens'])) {
            $kabupatens = $filters['kabupatens'] ?? [$filters['kabupaten']];
            $query->where(function (Builder $scoped) use ($kabupatens) {
                $scoped->whereHas('user', function (Builder $userQuery) use ($kabupatens) {
                    $userQuery->whereIn('kabupaten', $kabupatens)
                        ->orWhereHas('travel', fn (Builder $travelQuery) => $travelQuery->whereIn('kab_kota', $kabupatens));
                });
            });
        }

        return $query;
    }
}

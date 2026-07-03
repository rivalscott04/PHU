<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class KabupatenScopeFilter
{
    public static function applyOnColumn(Builder $query, array $filters, string $column): void
    {
        if (! empty($filters['kabupatens'])) {
            $query->whereIn($column, $filters['kabupatens']);

            return;
        }

        if (! empty($filters['kabupaten'])) {
            $query->where($column, $filters['kabupaten']);
        }
    }

    public static function applyOnTravelRelation(Builder $query, array $filters, string $relation = 'travel'): void
    {
        if (empty($filters['kabupaten']) && empty($filters['kabupatens'])) {
            return;
        }

        $query->whereHas($relation, function (Builder $travel) use ($filters): void {
            self::applyOnColumn($travel, $filters, 'kab_kota');
        });
    }

    /** @return array<string, mixed> */
    public static function pengawasFilters(User $user): array
    {
        if ($user->role !== 'pengawas') {
            return [];
        }

        $scoped = $user->getScopedKabupatens();

        if ($scoped === null) {
            return [];
        }

        if (count($scoped) === 1) {
            return ['kabupaten' => $scoped[0]];
        }

        return ['kabupatens' => $scoped];
    }
}

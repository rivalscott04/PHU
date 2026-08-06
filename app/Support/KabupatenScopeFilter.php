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
            $values = NtbKabupatenMap::queryValues($scoped[0]);

            return count($values) === 1
                ? ['kabupaten' => $values[0]]
                : ['kabupatens' => $values];
        }

        return ['kabupatens' => NtbKabupatenMap::expandKabupatenList($scoped)];
    }

    /** @return array<string, mixed> */
    public static function filtersForUser(User $user): array
    {
        if ($user->role === 'kabupaten') {
            $values = NtbKabupatenMap::queryValues($user->kabupaten);

            if ($values === []) {
                return ['kabupaten' => $user->kabupaten];
            }

            return count($values) === 1
                ? ['kabupaten' => $values[0]]
                : ['kabupatens' => $values];
        }

        return self::pengawasFilters($user);
    }

    public static function applyOnTravelOrCabangUser(Builder $query, User $user, string $userRelation = 'user'): void
    {
        $filters = self::filtersForUser($user);

        if ($filters === []) {
            return;
        }

        $query->whereHas($userRelation, function (Builder $subject) use ($filters): void {
            $subject->where(function (Builder $scoped) use ($filters): void {
                $scoped->whereHas('travel', function (Builder $travel) use ($filters): void {
                    self::applyOnColumn($travel, $filters, 'kab_kota');
                })->orWhereHas('cabang', function (Builder $cabang) use ($filters): void {
                    self::applyOnColumn($cabang, $filters, 'kabupaten');
                });
            });
        });
    }

    public static function applyOnSertifikat(Builder $query, User $user): void
    {
        $filters = self::filtersForUser($user);

        if ($filters === []) {
            return;
        }

        $query->where(function (Builder $scoped) use ($filters): void {
            $scoped->whereHas('travel', function (Builder $travel) use ($filters): void {
                self::applyOnColumn($travel, $filters, 'kab_kota');
            })->orWhereHas('cabang', function (Builder $cabang) use ($filters): void {
                self::applyOnColumn($cabang, $filters, 'kabupaten');
            });
        });
    }
}

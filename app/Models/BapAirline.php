<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BapAirline extends Model
{
    protected $fillable = [
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /** @return list<string> */
    public static function activeNames(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }

    public static function matchesName(string $value, string $candidate): bool
    {
        return mb_strtolower(trim($value)) === mb_strtolower(trim($candidate));
    }

    public static function isKnownName(string $value): bool
    {
        $needle = mb_strtolower(trim($value));

        if ($needle === '') {
            return false;
        }

        foreach (static::activeNames() as $name) {
            if (mb_strtolower($name) === $needle) {
                return true;
            }
        }

        return false;
    }
}

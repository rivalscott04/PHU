<?php

namespace App\Enums\Concerns;

trait ProvidesLocalizedLabels
{
    public static function labelFor(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return static::tryFrom($value)?->label() ?? 'Tidak Diketahui';
    }

    public static function badgeFor(?string $value): string
    {
        return static::tryFrom((string) $value)?->badgeColor() ?? 'secondary';
    }
}

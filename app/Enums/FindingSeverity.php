<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesLocalizedLabels;

enum FindingSeverity: string
{
    use ProvidesLocalizedLabels;

    case Minor = 'MINOR';
    case Major = 'MAJOR';
    case Critical = 'CRITICAL';

    public function label(): string
    {
        return match ($this) {
            self::Minor => 'Ringan',
            self::Major => 'Sedang',
            self::Critical => 'Berat',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Minor => 'info',
            self::Major => 'warning',
            self::Critical => 'danger',
        };
    }
}

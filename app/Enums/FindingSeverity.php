<?php

namespace App\Enums;

enum FindingSeverity: string
{
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
}

<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesLocalizedLabels;

enum BapStatus: string
{
    use ProvidesLocalizedLabels;

    case Pending = 'pending';
    case Diajukan = 'diajukan';
    case Diproses = 'diproses';
    case Diterima = 'diterima';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Draf',
            self::Diajukan => 'Diajukan',
            self::Diproses => 'Diproses',
            self::Diterima => 'Diterima',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Diajukan => 'primary',
            self::Diproses => 'warning',
            self::Diterima => 'success',
        };
    }
}

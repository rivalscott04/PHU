<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesLocalizedLabels;

enum PengaduanStatus: string
{
    use ProvidesLocalizedLabels;

    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::InProgress => 'Sedang Diproses',
            self::Completed => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::InProgress => 'info',
            self::Completed => 'success',
            self::Rejected => 'danger',
        };
    }
}

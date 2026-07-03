<?php

namespace App\Enums;

enum WorkQueueStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Menunggu',
            self::InProgress => 'Diproses',
            self::Resolved => 'Selesai',
            self::Dismissed => 'Diabaikan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Open => 'warning',
            self::InProgress => 'info',
            self::Resolved => 'success',
            self::Dismissed => 'secondary',
        };
    }
}

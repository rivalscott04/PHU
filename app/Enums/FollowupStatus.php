<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesLocalizedLabels;

enum FollowupStatus: string
{
    use ProvidesLocalizedLabels;

    case Submitted = 'SUBMITTED';
    case Pending = 'PENDING';
    case RevisionRequired = 'REVISION_REQUIRED';
    case Verified = 'VERIFIED';
    case Rejected = 'REJECTED';
    case Closed = 'CLOSED';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Diajukan',
            self::Pending => 'Menunggu',
            self::RevisionRequired => 'Perlu Revisi',
            self::Verified => 'Terverifikasi',
            self::Rejected => 'Ditolak',
            self::Closed => 'Selesai',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Submitted => 'info',
            self::Pending => 'warning',
            self::RevisionRequired => 'danger',
            self::Verified => 'success',
            self::Rejected => 'dark',
            self::Closed => 'secondary',
        };
    }
}

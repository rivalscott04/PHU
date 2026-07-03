<?php

namespace App\Enums;

enum InspectionStatus: string
{
    case Draft = 'DRAFT';
    case Scheduled = 'SCHEDULED';
    case OnProgress = 'ON_PROGRESS';
    case WaitingFollowup = 'WAITING_FOLLOWUP';
    case FollowupUploaded = 'FOLLOWUP_UPLOADED';
    case Verified = 'VERIFIED';
    case Closed = 'CLOSED';
    case Cancelled = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Scheduled => 'Terjadwal',
            self::OnProgress => 'Berlangsung',
            self::WaitingFollowup => 'Menunggu Tindak Lanjut',
            self::FollowupUploaded => 'Tindak Lanjut Diunggah',
            self::Verified => 'Terverifikasi',
            self::Closed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Scheduled => 'info',
            self::OnProgress => 'primary',
            self::WaitingFollowup => 'warning',
            self::FollowupUploaded => 'info',
            self::Verified => 'success',
            self::Closed => 'success',
            self::Cancelled => 'dark',
        };
    }
}

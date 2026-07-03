<?php

namespace App\Enums;

enum FindingStatus: string
{
    case Open = 'OPEN';
    case WaitingResponse = 'WAITING_RESPONSE';
    case FollowupUploaded = 'FOLLOWUP_UPLOADED';
    case RevisionRequired = 'REVISION_REQUIRED';
    case Verified = 'VERIFIED';
    case Closed = 'CLOSED';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Baru',
            self::WaitingResponse => 'Menunggu Respons Travel',
            self::FollowupUploaded => 'Tindak Lanjut Diunggah',
            self::RevisionRequired => 'Perlu Revisi',
            self::Verified => 'Terverifikasi',
            self::Closed => 'Selesai',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Open => 'secondary',
            self::WaitingResponse => 'warning',
            self::FollowupUploaded => 'info',
            self::RevisionRequired => 'danger',
            self::Verified => 'success',
            self::Closed => 'secondary',
        };
    }
}

<?php

namespace App\Enums;

enum InspectionType: string
{
    case Routine = 'ROUTINE';
    case SpotCheck = 'SPOT_CHECK';
    case ComplaintBased = 'COMPLAINT_BASED';
    case Special = 'SPECIAL';

    public function label(): string
    {
        return match ($this) {
            self::Routine => 'Rutin',
            self::SpotCheck => 'Pemeriksaan Mendadak',
            self::ComplaintBased => 'Berdasarkan Pengaduan',
            self::Special => 'Khusus',
        };
    }
}

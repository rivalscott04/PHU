<?php

namespace App\Enums;

use App\Enums\Concerns\ProvidesLocalizedLabels;

enum TravelRegistrationStatus: string
{
    use ProvidesLocalizedLabels;
    case Pending = 'pending';
    // Kabupaten sudah unggah rekomendasi/BA peninjauan dan meneruskan ke Kanwil.
    // Hanya dipakai alur cabang; pusat langsung pending -> approved oleh Kanwil.
    case MenungguKanwil = 'menunggu_kanwil';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::MenungguKanwil => 'Menunggu Kanwil',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::MenungguKanwil => 'bg-info',
            self::Approved => 'bg-success',
            self::Rejected => 'bg-danger',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::MenungguKanwil => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}

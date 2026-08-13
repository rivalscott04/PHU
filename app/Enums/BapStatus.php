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

    /**
     * Diterima adalah status akhir. Begitu BA disetujui, nomor suratnya terbit
     * dan dokumennya bisa dicetak serta diverifikasi lewat QR. Mengembalikannya
     * ke status sebelumnya membuat dokumen yang sudah beredar mendadak tidak
     * valid, tanpa jejak alasan apa pun.
     *
     * Selain itu status boleh bergerak bebas, termasuk mundur, karena petugas
     * memang kadang perlu mengembalikan BA ke travel untuk diperbaiki.
     */
    public static function canTransition(?string $from, ?string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        return $from !== self::Diterima->value;
    }
}

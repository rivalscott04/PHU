<?php

namespace App\Enums;

enum WorkQueueType: string
{
    case Pengaduan = 'pengaduan';
    case RisikoTinggi = 'risiko_tinggi';
    case DeadlineTemuan = 'deadline_temuan';
    case VerifikasiFollowup = 'verifikasi_followup';

    public function label(): string
    {
        return match ($this) {
            self::Pengaduan => 'Pengaduan Baru',
            self::RisikoTinggi => 'Skor Risiko Tinggi',
            self::DeadlineTemuan => 'Deadline Temuan',
            self::VerifikasiFollowup => 'Verifikasi Tindak Lanjut',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pengaduan => 'bx-message-square-dots',
            self::RisikoTinggi => 'bx-error',
            self::DeadlineTemuan => 'bx-time-five',
            self::VerifikasiFollowup => 'bx-task',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pengaduan => 'danger',
            self::RisikoTinggi => 'warning',
            self::DeadlineTemuan => 'dark',
            self::VerifikasiFollowup => 'info',
        };
    }
}

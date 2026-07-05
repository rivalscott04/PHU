<?php

namespace App\Enums;

enum RiskLevel: string
{
    case Low = 'LOW';
    case Medium = 'MEDIUM';
    case High = 'HIGH';
    case Critical = 'CRITICAL';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
            self::Critical => 'Kritis',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Low => 'success',
            self::Medium => 'info',
            self::High => 'warning',
            self::Critical => 'danger',
        };
    }

    public static function labelFor(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return self::tryFrom($value)?->label() ?? $value;
    }

    public static function badgeFor(?string $value): string
    {
        return self::tryFrom((string) $value)?->badgeColor() ?? 'secondary';
    }
}

<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Pimpinan = 'pimpinan';
    case Kabupaten = 'kabupaten';
    case Pengawas = 'pengawas';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Super Admin',
            self::Pimpinan => 'Pimpinan Kanwil',
            self::Kabupaten => 'Admin Kabupaten',
            self::Pengawas => 'Pengawas',
            self::User => 'User Travel',
        };
    }

    public function domainDescription(): string
    {
        return match ($this) {
            self::Admin => 'Seluruh NTB',
            self::Pimpinan => 'Seluruh NTB',
            self::Kabupaten => 'Satu kabupaten',
            self::Pengawas => 'Wilayah bisa diatur',
            self::User => 'Satu PPIU',
        };
    }

    /** @return list<self> */
    public static function assignableByAdmin(): array
    {
        return [self::Pimpinan, self::Kabupaten, self::Pengawas, self::User];
    }

    public function requiresKabupaten(): bool
    {
        return in_array($this, [self::Kabupaten, self::Pengawas], true);
    }

    public function requiresTravel(): bool
    {
        return $this === self::User;
    }

    public function usesPengawasanModule(): bool
    {
        return in_array($this, [self::Admin, self::Pengawas], true);
    }

    public function usesExecutiveModule(): bool
    {
        return in_array($this, [self::Admin, self::Pimpinan], true);
    }

    public function usesOperationalModule(): bool
    {
        return in_array($this, [self::Admin, self::Kabupaten, self::User], true);
    }

    public static function tryFromString(?string $role): ?self
    {
        return $role !== null ? self::tryFrom($role) : null;
    }
}

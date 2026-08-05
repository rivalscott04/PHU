<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\BAP;
use App\Models\CabangTravel;
use App\Models\Jamaah;
use App\Models\JamaahHajiKhusus;
use App\Models\Pengaduan;
use App\Models\Pengunduran;
use App\Models\Sertifikat;
use App\Models\TravelCompany;
use App\Models\User;

final class KabupatenResourceGuard
{
    public static function isAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    public static function canAccessWilayah(User $user, ?string $kabupaten): bool
    {
        if (self::isAdmin($user)) {
            return true;
        }

        if ($user->role === UserRole::Kabupaten->value) {
            return NtbKabupatenMap::matches($user->kabupaten, $kabupaten);
        }

        if ($user->role === UserRole::Pengawas->value) {
            return $user->canAccessKabupaten($kabupaten);
        }

        return false;
    }

    public static function authorizeWilayah(User $user, ?string $kabupaten): void
    {
        ResourceAccess::denyUnless(self::canAccessWilayah($user, $kabupaten));
    }

    public static function requireAdmin(User $user): void
    {
        ResourceAccess::denyUnless(self::isAdmin($user));
    }

    public static function authorizeTravel(User $user, TravelCompany $travel): void
    {
        if ($user->role === UserRole::User->value && $user->travel_id === $travel->id) {
            return;
        }

        self::authorizeWilayah($user, $travel->kab_kota);
    }

    public static function authorizeCabang(User $user, CabangTravel $cabang): void
    {
        self::authorizeWilayah($user, $cabang->kabupaten);
    }

    public static function authorizeBap(User $user, BAP $bap): void
    {
        if ($user->role === UserRole::User->value && $bap->user_id === $user->id) {
            return;
        }

        self::authorizeWilayah($user, $bap->kab_kota);
    }

    public static function authorizeSertifikat(User $user, Sertifikat $sertifikat): void
    {
        if ($sertifikat->travel_id) {
            $sertifikat->loadMissing('travel');
            self::authorizeWilayah($user, $sertifikat->travel?->kab_kota);

            return;
        }

        if ($sertifikat->cabang_id) {
            $sertifikat->loadMissing('cabang');
            self::authorizeWilayah($user, $sertifikat->cabang?->kabupaten);

            return;
        }

        self::requireAdmin($user);
    }

    public static function authorizeJamaah(User $user, Jamaah $jamaah): void
    {
        if ($user->role === UserRole::User->value && $jamaah->travel_id === $user->travel_id) {
            return;
        }

        $jamaah->loadMissing('travel');
        self::authorizeWilayah($user, $jamaah->travel?->kab_kota);
    }

    public static function authorizeJamaahHajiKhusus(User $user, JamaahHajiKhusus $record): void
    {
        if ($user->role === UserRole::User->value && $record->travel_id === $user->travel_id) {
            return;
        }

        $record->loadMissing('travel');
        self::authorizeWilayah($user, $record->travel?->kab_kota);
    }

    public static function authorizePengaduan(User $user, Pengaduan $pengaduan): void
    {
        $pengaduan->loadMissing('travel');
        self::authorizeWilayah($user, $pengaduan->travel?->kab_kota);
    }

    public static function authorizePengunduran(User $user, Pengunduran $pengunduran): void
    {
        $pengunduran->loadMissing('user.travel', 'user.cabang');
        $subject = $pengunduran->user;

        if (! $subject) {
            ResourceAccess::denyUnless(self::isAdmin($user));

            return;
        }

        if ($subject->travel) {
            self::authorizeWilayah($user, $subject->travel->kab_kota);

            return;
        }

        if ($subject->cabang) {
            self::authorizeWilayah($user, $subject->cabang->kabupaten);

            return;
        }

        self::requireAdmin($user);
    }

    public static function scopedKabupatenValues(User $user): ?array
    {
        if (self::isAdmin($user)) {
            return null;
        }

        if ($user->role === UserRole::Kabupaten->value) {
            return NtbKabupatenMap::queryValues($user->kabupaten);
        }

        return $user->getScopedKabupatens();
    }
}

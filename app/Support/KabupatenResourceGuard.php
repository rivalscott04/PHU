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

    /**
     * Akses baca terhadap satu travel. Akun travel boleh melihat datanya
     * sendiri. JANGAN dipakai untuk jalur yang mengubah data izin, pakai
     * authorizeTravelAsStaff().
     */
    public static function authorizeTravel(User $user, TravelCompany $travel): void
    {
        if ($user->role === UserRole::User->value && $user->travel_id === $travel->id) {
            return;
        }

        self::authorizeWilayah($user, $travel->kab_kota);
    }

    /**
     * Perubahan data izin travel hanya boleh dilakukan petugas.
     *
     * Nomor izin, tanggal izin, masa berlaku, nilai akreditasi, jenis izin
     * PPIU atau PIHK, dan kabupaten pengawas semuanya fakta yang ditetapkan
     * regulator dan ditampilkan ke publik sebagai penanda travel resmi.
     * Membiarkan travel mengubahnya sendiri sama saja membiarkan pemegang izin
     * menaikkan kelas izinnya, memperpanjang masa berlakunya, atau berpindah
     * wilayah untuk lepas dari pengawasnya.
     */
    public static function authorizeTravelAsStaff(User $user, TravelCompany $travel): void
    {
        if (self::isAdmin($user)) {
            return;
        }

        // Hanya Kabupaten/Kota wilayah travel tersebut. Pengawas sengaja tidak
        // termasuk: tugasnya memeriksa kepatuhan, bukan menetapkan izin.
        ResourceAccess::denyUnless(
            $user->role === UserRole::Kabupaten->value
            && NtbKabupatenMap::matches($user->kabupaten, $travel->kab_kota)
        );
    }

    public static function authorizeCabang(User $user, CabangTravel $cabang): void
    {
        self::authorizeWilayah($user, $cabang->kabupaten);
    }

    /** Pasangan authorizeTravelAsStaff() untuk cabang. Lihat penjelasannya di sana. */
    public static function authorizeCabangAsStaff(User $user, CabangTravel $cabang): void
    {
        if (self::isAdmin($user)) {
            return;
        }

        ResourceAccess::denyUnless(
            $user->role === UserRole::Kabupaten->value
            && NtbKabupatenMap::matches($user->kabupaten, $cabang->kabupaten)
        );
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
        // Kepemilikan dinilai lewat OperatorScope, bukan travel_id saja. Untuk
        // PIC cabang, travel_id-nya kosong sehingga perbandingan langsung selalu
        // gagal dan dia terkunci dari data jamaahnya sendiri.
        if ($user->role === UserRole::User->value
            && OperatorScope::owns($user, $jamaah->travel_id, $jamaah->cabang_id)) {
            return;
        }

        $jamaah->loadMissing('travel');
        self::authorizeWilayah($user, $jamaah->travel?->kab_kota);
    }

    public static function authorizeJamaahHajiKhusus(User $user, JamaahHajiKhusus $record): void
    {
        if ($user->role === UserRole::User->value
            && OperatorScope::owns($user, $record->travel_id, $record->cabang_id)) {
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

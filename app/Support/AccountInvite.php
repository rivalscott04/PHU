<?php

namespace App\Support;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Satu jalur untuk semua pembuatan password: akun baru yang dibuat petugas,
 * tombol reset di daftar pengguna, dan lupa password mandiri. Petugas tidak
 * pernah mengetikkan password milik orang lain, dan tidak ada password default
 * yang bisa ditebak.
 */
final class AccountInvite
{
    /**
     * Password acak yang tidak diberitahukan ke siapa pun. Akun tetap punya
     * password yang sah di database, tapi satu satunya cara masuk adalah
     * membuat password sendiri lewat tautan.
     */
    public static function placeholderPassword(): string
    {
        return Hash::make(Str::password(32));
    }

    /** Tautan set password sekali pakai, umurnya mengikuti config/auth.php passwords.users.expire. */
    public static function link(User $user): string
    {
        $token = Password::createToken($user);

        app(AuditLogService::class)->log(
            'auth',
            'reset_link_issued',
            "Tautan set password diterbitkan untuk {$user->email}"
        );

        return route('password.reset', ['token' => $token, 'email' => $user->email]);
    }

    /** Nomor WhatsApp yang terdaftar pada akun, format 62, untuk tautan wa.me. */
    public static function whatsappNumber(User $user): ?string
    {
        $nomor = preg_replace('/\D/', '', (string) $user->nomor_hp);

        return str_starts_with($nomor, '0') ? '62'.substr($nomor, 1) : ($nomor ?: null);
    }

    /**
     * Data flash untuk layout: tautan, nomor terdaftar tujuan pengiriman, dan
     * pesan WhatsApp yang sudah terisi supaya petugas tidak salah kirim.
     */
    public static function flashPayload(User $user): array
    {
        $link = self::link($user);
        $nomor = self::whatsappNumber($user);

        return [
            'nama' => $user->nama,
            'email' => $user->email,
            'nomor_hp' => $user->nomor_hp,
            'link' => $link,
            'wa_url' => $nomor
                ? 'https://wa.me/'.$nomor.'?text='.rawurlencode(
                    "Halo {$user->nama}, silakan atur password akun PANTAU Anda. "
                    ."Buat password Anda lewat tautan berikut sebelum kedaluwarsa: {$link}"
                )
                : null,
        ];
    }
}

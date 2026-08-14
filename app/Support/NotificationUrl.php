<?php

namespace App\Support;

final class NotificationUrl
{
    /**
     * Kembalikan path lokal saja. Nilainya dipakai sebagai tujuan redirect,
     * jadi harus tidak mungkin mengarah ke luar aplikasi.
     *
     * Perhatikan bentuk "//host": diawali garis miring sehingga tampak seperti
     * path, tetapi browser membacanya sebagai alamat protokol relatif menuju
     * domain lain.
     */
    public static function normalize(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        if (str_starts_with($url, '//')) {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);

        return $path && str_starts_with($path, '/') ? $path : null;
    }
}

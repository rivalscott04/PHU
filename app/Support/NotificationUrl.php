<?php

namespace App\Support;

final class NotificationUrl
{
    public static function normalize(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return parse_url($url, PHP_URL_PATH) ?: $url;
    }
}

<?php

namespace App\Helpers;

class StorageHelper
{
    /**
     * Normalize a stored file path to a relative disk path (no domain, no /storage/ prefix).
     */
    public static function normalizePath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = trim($path);

        if (str_contains($path, '://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = is_string($parsed) && $parsed !== '' ? $parsed : $path;
        }

        if (str_starts_with($path, '/storage/')) {
            $path = substr($path, strlen('/storage/'));
        } elseif (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return ltrim($path, '/');
    }

    /**
     * Build a relative public URL for a file on the public disk.
     */
    public static function publicUrl(?string $path): ?string
    {
        $normalized = self::normalizePath($path);

        if ($normalized === null || $normalized === '') {
            return null;
        }

        return '/storage/'.$normalized;
    }

    /**
     * Build an absolute URL using the current request host (for QR codes / external links).
     */
    public static function absoluteUrl(string $relativePath): string
    {
        $path = str_starts_with($relativePath, '/') ? $relativePath : '/'.$relativePath;

        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return rtrim(config('app.url'), '/').$path;
        }

        return request()->getSchemeAndHttpHost().$path;
    }
}

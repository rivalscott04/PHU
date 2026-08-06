<?php

namespace App\Support;

use App\Models\TravelCompany;

final class ExportFilename
{
    public static function build(string $prefix, ?string $subject = null, string $extension = 'xlsx'): string
    {
        $parts = [self::sanitize($prefix)];

        if ($subject !== null && $subject !== '') {
            $parts[] = self::sanitize($subject);
        }

        $parts[] = now()->format('Ymd');

        return implode('_', $parts).'.'.$extension;
    }

    public static function jamaah(string $jenis, bool $isGlobal, ?TravelCompany $travel, string $extension): string
    {
        if ($isGlobal) {
            return self::build('jamaah_'.$jenis.'_semua', null, $extension);
        }

        return self::build('jamaah_'.$jenis, $travel?->Penyelenggara, $extension);
    }

    private static function sanitize(string $value): string
    {
        $value = preg_replace('/[^\pL\pN\s]/u', '', $value) ?? '';

        return str_replace(' ', '_', trim($value));
    }
}

<?php

namespace App\Support;

use App\Models\SystemSetting;

class KanwilContact
{
    public static function get(string $key, ?string $default = null): string
    {
        return (string) config("app.kanwil.{$key}", $default ?? '');
    }

    public static function supportPhone(): string
    {
        return SystemSetting::supportPhone();
    }

    public static function supportEmail(): string
    {
        return SystemSetting::supportEmail();
    }

    public static function letterheadTitleHtml(): string
    {
        return implode('<br>', [
            e(self::get('letterhead_ministry')),
            e(self::get('letterhead_office')),
            e(self::get('letterhead_province')),
        ]);
    }

    public static function letterheadContactHtml(): string
    {
        return e(self::get('address'))
            .' Telp. '.e(self::get('phone'))
            .'<br>Email: '.e(self::get('email'));
    }

    public static function exportSourceLabel(): string
    {
        return self::get('office_name');
    }
}

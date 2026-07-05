<?php

namespace App\Support;

class KanwilContact
{
    public static function get(string $key, ?string $default = null): string
    {
        return (string) config("app.kanwil.{$key}", $default ?? '');
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

<?php

namespace App\Enums;

enum PengawasScopeMode: string
{
    case Single = 'single';
    case Custom = 'custom';
    case All = 'all';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Satu kabupaten/kota',
            self::Custom => 'Beberapa kabupaten/kota',
            self::All => 'Seluruh NTB',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Single => 'Satu kabupaten/kota.',
            self::Custom => 'Pilih kabupaten yang diizinkan.',
            self::All => 'Semua kabupaten/kota NTB.',
        };
    }

    /** @return list<self> */
    public static function options(): array
    {
        return [self::Single, self::Custom, self::All];
    }
}

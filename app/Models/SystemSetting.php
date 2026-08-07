<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    private static ?self $cached = null;

    protected $fillable = [
        'support_phone',
        'support_email',
    ];

    public static function current(): self
    {
        if (self::$cached === null) {
            self::$cached = self::query()->first() ?? new self();
        }

        return self::$cached;
    }

    public static function resetCache(): void
    {
        self::$cached = null;
    }

    public static function supportPhone(): string
    {
        $phone = self::current()->support_phone;

        return ($phone !== null && trim($phone) !== '')
            ? trim($phone)
            : (string) config('app.kanwil.phone', '');
    }

    public static function supportEmail(): string
    {
        $email = self::current()->support_email;

        return ($email !== null && trim($email) !== '')
            ? trim($email)
            : (string) config('app.kanwil.email', '');
    }
}

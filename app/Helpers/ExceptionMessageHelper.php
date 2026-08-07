<?php

namespace App\Helpers;

use Illuminate\Database\QueryException;
use InvalidArgumentException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionMessageHelper
{
    public const GENERIC = 'Terjadi kesalahan. Silakan coba lagi. Jika masalah berlanjut, hubungi admin.';

    public const GENERIC_SAVE = 'Data tidak dapat disimpan. Periksa kembali isian Anda.';

    public const GENERIC_LOAD = 'Data tidak dapat dimuat. Silakan muat ulang halaman.';

    public const GENERIC_IMPORT = 'Import gagal. Periksa kembali file Excel Anda.';

    /**
     * Convert any exception to a user-safe Indonesian message.
     * Technical details are never returned; log them separately.
     */
    public static function forUser(Throwable $e, ?string $fallback = null): string
    {
        if (self::isSafeBusinessMessage($e)) {
            $message = trim($e->getMessage());

            if ($message !== '') {
                return $message;
            }
        }

        if (self::isDatabaseError($e)) {
            return self::databaseMessage($e);
        }

        if (self::looksTechnical($e->getMessage())) {
            return $fallback ?? self::GENERIC;
        }

        return $fallback ?? self::GENERIC;
    }

    /**
     * Row-level import errors with a friendly prefix.
     */
    public static function forImportRow(Throwable $e, int $rowNumber): string
    {
        return 'Baris '.$rowNumber.': '.self::forUser($e, 'Data tidak valid atau tidak dapat disimpan.');
    }

    public static function isSafeBusinessMessage(Throwable $e): bool
    {
        return $e instanceof InvalidArgumentException
            || ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500);
    }

    public static function isDatabaseError(Throwable $e): bool
    {
        if ($e instanceof QueryException || $e instanceof PDOException) {
            return true;
        }

        $message = $e->getMessage();

        return str_contains($message, 'SQLSTATE')
            || str_contains($message, 'PDO')
            || str_contains($message, 'Duplicate entry')
            || str_contains($message, 'foreign key constraint')
            || str_contains($message, 'Data too long')
            || str_contains($message, 'truncated');
    }

    private static function databaseMessage(Throwable $e): string
    {
        $message = $e->getMessage();

        if (preg_match('/Data too long|truncated|1406/i', $message)) {
            return 'Data yang Anda masukkan terlalu panjang. Periksa kembali isian formulir.';
        }

        if (preg_match('/Duplicate entry|1062/i', $message)) {
            return 'Data sudah terdaftar di sistem. Gunakan data lain.';
        }

        if (preg_match('/foreign key constraint|1451|1452/i', $message)) {
            return 'Data tidak dapat disimpan karena masih terkait dengan data lain.';
        }

        if (preg_match('/cannot be null|1048/i', $message)) {
            return 'Mohon lengkapi semua kolom wajib sebelum menyimpan.';
        }

        if (preg_match('/Incorrect .* value|1366|1292/i', $message)) {
            return 'Format data tidak valid. Periksa kembali isian formulir.';
        }

        return self::GENERIC_SAVE;
    }

    private static function looksTechnical(string $message): bool
    {
        if ($message === '') {
            return true;
        }

        return (bool) preg_match(
            '/SQLSTATE|PDOException|Stack trace|\/var\/|\/Users\/|app\\\\|\.php on line|Call to undefined|Class .* not found|syntax error/i',
            $message
        );
    }
}

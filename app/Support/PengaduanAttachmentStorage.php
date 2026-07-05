<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PengaduanAttachmentStorage
{
    /** @var array<string, string> */
    private const ALLOWED_MIMES = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public static function store(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        $realMime = self::detectMimeType($file->getRealPath());

        if ($realMime === null || ! isset(self::ALLOWED_MIMES[$realMime])) {
            throw ValidationException::withMessages([
                'berkas_aduan' => 'Isi file tidak sesuai dengan format PDF, JPG, atau PNG yang diizinkan.',
            ]);
        }

        $extension = self::ALLOWED_MIMES[$realMime];
        $filename = Str::uuid()->toString().'.'.$extension;

        return $file->storeAs('berkas_aduan', $filename, 'local');
    }

    public static function resolvePath(string $storedPath): ?string
    {
        if ($storedPath === '' || str_contains($storedPath, '..')) {
            return null;
        }

        $localPath = storage_path('app/'.$storedPath);
        if (is_file($localPath)) {
            return $localPath;
        }

        $legacyPublicPath = storage_path('app/public/'.$storedPath);
        if (is_file($legacyPublicPath)) {
            return $legacyPublicPath;
        }

        return null;
    }

    public static function contentType(string $absolutePath): string
    {
        $mime = self::detectMimeType($absolutePath);

        return $mime ?? 'application/octet-stream';
    }

    private static function detectMimeType(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        if (! is_string($mime) || $mime === '') {
            return null;
        }

        return $mime;
    }
}

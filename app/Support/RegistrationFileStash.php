<?php

namespace App\Support;

use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Penyimpanan sementara berkas pendaftaran.
 *
 * Browser tidak bisa mengisi ulang input file, jadi satu kesalahan kecil di
 * langkah terakhir (email sudah terpakai, misalnya) membuat pendaftar mengunggah
 * ulang semua berkasnya. Berkas yang lolos pemeriksaan dasar disimpan dulu di
 * folder sementara dan diingat lewat session, lalu dipindahkan ke tempat
 * permanennya begitu pendaftaran berhasil.
 */
class RegistrationFileStash
{
    private const SESSION_KEY = 'registrasi_berkas';
    private const DIREKTORI = 'registrasi-sementara';
    private const KEDALUWARSA_HARI = 2;

    /** Simpan berkas yang baru diunggah, menimpa simpanan sebelumnya. */
    public function capture(Request $request, array $fields, int $maxKb): void
    {
        $this->prune();

        foreach ($fields as $field) {
            $file = $request->file($field);

            if (! $file instanceof UploadedFile || ! $this->layakDisimpan($file, $maxKb)) {
                continue;
            }

            $this->forget($field);

            session()->put(self::SESSION_KEY.'.'.$field, [
                'path' => StorageHelper::normalizePath($file->store(self::DIREKTORI, 'public')),
                'nama' => $file->getClientOriginalName(),
            ]);
        }
    }

    public function has(string $field): bool
    {
        return $this->path($field) !== null;
    }

    /** Nama asli berkas per kolom, untuk ditampilkan di formulir. */
    public function names(): array
    {
        $names = [];

        foreach (session(self::SESSION_KEY, []) as $field => $simpanan) {
            if ($this->path($field) !== null) {
                $names[$field] = $simpanan['nama'];
            }
        }

        return $names;
    }

    /** Pindahkan ke lokasi permanen, kembalikan pathnya. */
    public function moveTo(string $field, string $directory): ?string
    {
        $path = $this->path($field);

        if ($path === null) {
            return null;
        }

        $tujuan = $directory.'/'.basename($path);
        Storage::disk('public')->move($path, $tujuan);
        session()->forget(self::SESSION_KEY.'.'.$field);

        return StorageHelper::normalizePath($tujuan);
    }

    /** Buang semua simpanan, dipakai setelah pendaftaran berhasil dikirim. */
    public function clear(): void
    {
        foreach (array_keys(session(self::SESSION_KEY, [])) as $field) {
            $this->forget($field);
        }

        session()->forget(self::SESSION_KEY);
    }

    /** Path simpanan yang berkasnya masih benar benar ada. */
    private function path(string $field): ?string
    {
        $path = session(self::SESSION_KEY.'.'.$field.'.path');

        return $path && Storage::disk('public')->exists($path) ? $path : null;
    }

    private function forget(string $field): void
    {
        if ($path = session(self::SESSION_KEY.'.'.$field.'.path')) {
            Storage::disk('public')->delete($path);
        }

        session()->forget(self::SESSION_KEY.'.'.$field);
    }

    /**
     * Pendaftar yang menyerah di tengah jalan meninggalkan berkasnya di sini.
     *
     * ponytail: disapu saat ada unggahan baru, bukan lewat penjadwal. Kalau
     * folder ini tumbuh besar, pindahkan ke scheduler.
     */
    private function prune(): void
    {
        $disk = Storage::disk('public');
        $batas = now()->subDays(self::KEDALUWARSA_HARI)->getTimestamp();

        foreach ($disk->files(self::DIREKTORI) as $path) {
            if ($disk->lastModified($path) < $batas) {
                $disk->delete($path);
            }
        }
    }

    /**
     * Hanya berkas yang juga akan lolos validasi yang boleh disimpan. Kalau
     * tidak, berkas yang ditolak aturan mimes bisa menyelinap lewat simpanan
     * pada kiriman berikutnya, karena di sana kolomnya sudah tidak wajib.
     */
    private function layakDisimpan(UploadedFile $file, int $maxKb): bool
    {
        return $file->isValid()
            && $file->getSize() <= $maxKb * 1024
            && in_array(strtolower($file->getClientOriginalExtension()), ['pdf', 'jpg', 'jpeg', 'png'], true)
            && in_array($file->getMimeType(), ['application/pdf', 'image/jpeg', 'image/png'], true);
    }
}

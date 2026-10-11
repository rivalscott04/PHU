<?php

namespace App\Support;

use App\Models\CabangTravel;
use App\Helpers\StorageHelper;
use App\Models\SertifikatSetting;
use App\Services\BapVerificationService;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * Berita acara laporan pembukaan PPIU cabang, diisi dari data cabang yang
 * sudah disetujui Kanwil. Dibuat ulang setiap diminta, jadi selalu mengikuti
 * data terakhir. Isian yang kosong (cabang lama, atau nomor surat permohonan
 * yang memang tidak dicatat) dibiarkan titik-titik untuk diisi di Word.
 */
class BeritaAcaraCabang
{
    public const TEMPLATE = 'templates/berita-acara-cabang.docx';

    private const KOSONG = '..............';

    /** @return array<string, string> */
    public static function values(CabangTravel $cabang): array
    {
        $nomor = $cabang->pastikanNomorBeritaAcara();
        $terbit = $cabang->verified_at;
        $skTanggal = $cabang->travel?->Tanggal;

        return [
            'nomor' => $nomor,
            'nama' => $cabang->Penyelenggara,
            'kabupaten' => self::wilayah($cabang->kabupaten),
            'pimpinan' => (string) $cabang->pimpinan_cabang,
            'alamat' => (string) $cabang->alamat_cabang,
            'telepon' => (string) $cabang->telepon,
            'pusat' => $cabang->travel?->Penyelenggara ?: $cabang->Penyelenggara,
            'sk_pusat' => ($cabang->travel?->Pusat ?: $cabang->pusat) ?: self::KOSONG,
            'sk_tahun' => $skTanggal?->format('Y') ?? '......',
            'sk_tanggal' => $skTanggal?->translatedFormat('j F Y') ?? self::KOSONG,
            ...self::detail($cabang),
            // Kop sama dengan BA pemberangkatan, dibaca dari data kontak Kanwil.
            'kop_kementerian' => KanwilContact::get('letterhead_ministry'),
            'kop_kantor' => KanwilContact::get('letterhead_office'),
            'kop_provinsi' => KanwilContact::get('letterhead_province'),
            'kop_alamat' => KanwilContact::get('address') . ' Telp. ' . KanwilContact::get('phone'),
            'kop_email' => 'Email: ' . KanwilContact::get('email'),
            'kota' => 'Mataram',
            'tanggal' => $terbit->translatedFormat('j F Y'),
            'penandatangan' => SertifikatSetting::signatory()->nama ?: '(                         )',
        ];
    }

    /** Nomor dan tanggal berkas yang diisi pendaftar; yang kosong tetap titik-titik. */
    private static function detail(CabangTravel $cabang): array
    {
        return collect(CabangTravel::detailDokumen())->mapWithKeys(function ($meta, $name) use ($cabang) {
            $nilai = $cabang->{$name};

            return [$name => $nilai instanceof \Carbon\CarbonInterface ? $nilai->translatedFormat('j F Y') : ((string) $nilai ?: self::KOSONG)];
        })->all();
    }

    /** Tulis dokumen ke file sementara dan kembalikan path-nya. */
    public static function generate(CabangTravel $cabang): string
    {
        Settings::setOutputEscapingEnabled(true);

        $doc = new TemplateProcessor(resource_path(self::TEMPLATE));
        $doc->setValues(self::values($cabang));

        $qr = tempnam(sys_get_temp_dir(), 'qr');
        $dataUri = self::qr($cabang);
        if ($dataUri) {
            file_put_contents($qr, base64_decode(substr($dataUri, strpos($dataUri, ',') + 1)));
            $doc->setImageValue('qr', ['path' => $qr, 'width' => 76, 'height' => 76, 'ratio' => false]);
        } else {
            $doc->setValue('qr', '');
        }

        $path = $doc->save();
        @unlink($qr);
        self::apungkanQr($path);

        return $path;
    }

    /**
     * PhpWord hanya menyisipkan gambar sebaris. Sebaris, QR menambah tinggi blok
     * tanda tangan dan mendorongnya ke halaman 2, jadi QR dibuat melayang di kiri
     * blok tanda tangan seperti cap.
     */
    private static function apungkanQr(string $path): void
    {
        $zip = new \ZipArchive();
        $zip->open($path);
        $xml = $zip->getFromName('word/document.xml');
        $xml = preg_replace(
            '/(<v:shape type="#_x0000_t75" style=")(width:[^;"]+;height:[^;"]+)"/',
            '$1position:absolute;margin-left:258pt;margin-top:-30pt;$2;z-index:251659264;'
            . 'mso-position-horizontal-relative:text;mso-position-vertical-relative:text"',
            $xml,
            1,
        );
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /** Halaman publik yang dibuka saat QR tanda tangan dipindai. */
    public static function verifikasiUrl(CabangTravel $cabang): string
    {
        return StorageHelper::absoluteUrl(route('cabang.berita-acara.verifikasi', $cabang->ba_token, absolute: false));
    }

    public static function qr(CabangTravel $cabang): ?string
    {
        $cabang->pastikanNomorBeritaAcara();

        return app(BapVerificationService::class)->qrDataUri(self::verifikasiUrl($cabang));
    }

    public static function filename(CabangTravel $cabang): string
    {
        return 'Berita Acara Cabang ' . preg_replace('/[^\pL\pN .-]+/u', '', $cabang->Penyelenggara)
            . ' - ' . $cabang->kabupaten . '.docx';
    }

    /** "Lombok Barat" menjadi "Kab. Lombok Barat"; "Kota Mataram" tetap. */
    private static function wilayah(?string $kabupaten): string
    {
        $kabupaten = trim((string) $kabupaten);

        return $kabupaten === '' || str_starts_with($kabupaten, 'Kota') ? $kabupaten : 'Kab. ' . $kabupaten;
    }
}

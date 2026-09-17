# Panduan Screenshot (Playwright)

Satu set data: **PT. Mataram Travel** + admin **Kota Mataram**.  
Skrip mengambil screenshot menu dan langkah isi/verifikasi untuk dua file panduan.

## Akun

| Peran | Email | Password |
|-------|-------|----------|
| Travel | `mataram.travel@pantau.kemenhaj.id` | `password123` |
| Admin Kabupaten | `kota.mataram@pantau.kemenhaj.id` | `password123` |

## Cara jalanin

Jalankan di **Terminal biasa** (Terminal.app atau terminal Cursor), **bukan** lewat agent chat.  
Agent Cursor jalan di sandbox dan tidak boleh membuka Chrome asli.

```bash
# 1) Siapkan DB + data panduan (boleh dari agent / terminal)
npm run panduan:prepare

# 2) Server (terminal lain)
php artisan serve --host=127.0.0.1 --port=8000

# 3) Screenshot di Google Chrome asli
npm run panduan:shots:headed
# atau:
bash scripts/panduan-shots-chrome.sh
```

`PW_CHANNEL=chrome` memaksa Playwright memakai **/Applications/Google Chrome.app**.  
`PANDUAN_FULLSCREEN=1` memakai viewport **1920×1080** (screenshot fullscreen halaman).

Hasil masuk ke:

- `docs/assets/panduan/travel/`
- `docs/assets/panduan/kabupaten/`

Lalu gambar itu sudah dirujuk di:

- `docs/PANDUAN_TRAVEL.md`
- `docs/PANDUAN_ADMIN_KABUPATEN.md`

## File Word (.docx)

Setelah screenshot siap, buat dokumen Word berisi teks + gambar:

```bash
npm run panduan:docx
```

Hasil:

- `docs/PANDUAN_TRAVEL.docx`
- `docs/PANDUAN_ADMIN_KABUPATEN.docx`

## File teknis

| File | Fungsi |
|------|--------|
| `scripts/panduan-shots-prepare.sh` | migrate:fresh + PanduanScreenshotSeeder |
| `database/seeders/PanduanScreenshotSeeder.php` | 1 data Mataram + cabang pending + 1 pengaduan |
| `e2e/panduan/panduan-screenshots.spec.ts` | Alur screenshot |
| `e2e/helpers/panduan-shots.ts` | Helper shot / sidebar |

## Catatan

- Butuh Chromium Playwright (`npm run e2e:install`).
- `panduan:prepare` menghapus ulang database lokal (`migrate:fresh`). Jangan dipakai di production.
- Kalau ingin melihat browser: `npm run panduan:shots:headed`.

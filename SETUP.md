# Setup & Deployment — PANTAU (termasuk Modul V2)

Panduan ini mencakup konfigurasi environment, perintah `artisan`, scheduler, cache, queue, role **Pengawas**, dan **antrian kerja pengawasan** untuk menjalankan PANTAU (V1 + modul pengawasan V2).

### Ringkasan proses latar belakang

| Proses | Wajib? | Kapan / cara menjalankan |
|--------|--------|--------------------------|
| **Cron scheduler** (`schedule:run`) | **Ya** di production | Crontab setiap menit — lihat [bagian 5](#5-scheduler-cron--wajib-di-production) |
| **Queue worker** (`queue:work`) | **Tidak** (default `sync`) | Hanya jika `QUEUE_CONNECTION` diubah ke `redis`/`database` — lihat [bagian 6](#6-queue-worker-opsional) |
| **Antrian kerja** (`/v2/antrian`) | **Tidak** (tanpa worker/cron) | Item masuk otomatis saat event bisnis; lihat [bagian 5.1](#51-antrian-kerja-pengawasan--tanpa-workercron) |

---

## Prasyarat

| Komponen | Versi minimum |
|----------|----------------|
| PHP | 8.1+ (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` atau `imagick`) |
| Composer | 2.x |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Node.js (opsional) | Untuk build asset frontend jika diperlukan |

**Opsional (production / performa):**

- **Redis** — cache dashboard & monitoring (disarankan)
- **Supervisor** — menjalankan queue worker (hanya jika `QUEUE_CONNECTION` ≠ `sync`)

---

## 1. Instalasi awal (development)

```bash
# Clone & masuk ke folder proyek
cd PHU

# Dependensi PHP
composer install

# Salin environment & generate key
cp .env.example .env
php artisan key:generate

# Buat database MySQL, lalu isi DB_* di .env (lihat bagian 2)

# Migrasi semua tabel (V1 + V2)
php artisan migrate

# Symlink storage (upload BAP, sertifikat, dll.)
php artisan storage:link

# (Opsional) Data contoh untuk development
php artisan db:seed

# Jalankan server lokal
php artisan serve
```

Akses aplikasi: `http://localhost:8000`

**Login default** (setelah `migrate` + `db:seed`):

| Role | Email | Password |
|------|-------|----------|
| Admin Kanwil | `admin@phu.com` | `admin123` |
| Pengawas (per kabupaten/kota) | `pengawas.lombokbarat@phu.local` (dan 8 akun lainnya) | `password123` |
| Admin kabupaten | `kabupaten.lombokbarat@phu.com` (dan 8 akun lainnya) | `password123` |
| User travel | (dari seeder) | `password123` |

> User travel/kabupaten **wajib ganti password** sebelum mengakses modul V2 (middleware `password.changed`).  
> Akun **Pengawas** dari `PengawasSeeder` sudah `is_password_changed=true` — langsung bisa akses modul V2 di wilayah kabupatennya.

**Seeder khusus Pengawas** (tanpa data contoh lain):

```bash
php artisan db:seed --class=PengawasSeeder
```

Akun pengawas juga bisa dibuat lewat menu **Manajemen User** (role *Pengawas*). Atur wilayah akses: satu kabupaten, beberapa kabupaten, atau seluruh NTB.

---

## 2. Konfigurasi `.env`

### Wajib

```dotenv
APP_NAME=PANTAU
APP_ENV=local          # production di server live
APP_DEBUG=true         # false di production
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phu
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=local  # upload followup & dokumen disimpan private (bukan public)
```

### Disarankan — performa modul V2

Dashboard V2 mem-cache overview/statistik selama **5 menit**. Untuk production, gunakan Redis:

```dotenv
CACHE_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Tanpa Redis, cache memakai **file** (`CACHE_DRIVER=file`) — tetap jalan, tetapi lebih lambat di traffic tinggi.

### Opsional — queue (background job)

Default: `QUEUE_CONNECTION=sync` — notifikasi database & perintah scheduler dijalankan **langsung**, tanpa worker.

Notifikasi modul V2 (pengaduan baru, reminder deadline, dll.) disimpan ke tabel `notifications` secara **sinkron**. Worker hanya diperlukan jika Anda mengubah driver queue:

```dotenv
QUEUE_CONNECTION=redis   # atau database
```

Lalu jalankan worker (lihat [bagian 6](#6-queue-worker-opsional)).

### Email (reminder deadline followup)

```dotenv
MAIL_MAILER=log        # development: tulis ke log
# MAIL_MAILER=smtp     # production: SMTP sesungguhnya
MAIL_FROM_ADDRESS=noreply@phu.local
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 3. Migrasi database V2

Modul V2 menambahkan tabel berikut:

| Tabel | Fungsi |
|-------|--------|
| `master_checklist_categories` | Kategori checklist pengawasan |
| `master_checklists` | Item checklist master |
| `master_checklist_options` | Opsi jawaban checklist |
| `pengawasan` | Inspeksi / pengawasan travel |
| `pengawasan_checklists` | Jawaban checklist per inspeksi |
| `pengawasan_temuan` | Temuan inspeksi |
| `pengawasan_photos` | Foto bukti pengawasan |
| `pengawasan_followups` | Tindak lanjut temuan |
| `pengawasan_followup_logs` | Timeline tindak lanjut |
| `risk_scores` | Skor risiko per travel |
| `audit_logs` | Jejak audit modul V2 |
| `notifications` | Notifikasi Laravel (in-app & reminder deadline) |
| `pengawasan_antrian` | Antrian kerja pengawasan (pengaduan, risiko, deadline, verifikasi TL) |

**Perubahan skema tambahan** (`2026_07_03_*`):

| Migrasi | Perubahan |
|---------|-----------|
| `add_pengawas_role_to_users_table` | Role `pengawas` di kolom `users.role` |
| `create_pengawasan_antrian_table` | Tabel antrian kerja |
| `add_public_token_to_pengaduan_table` | Token UUID publik untuk pelacakan pengaduan |

```bash
# Migrasi semua (termasuk V2)
php artisan migrate

# Cek status migrasi
php artisan migrate:status

# Rollback batch terakhir (hati-hati di production)
php artisan migrate:rollback
```

---

## 4. Perintah Artisan — referensi

### Setup & maintenance

| Perintah | Kapan dijalankan |
|----------|------------------|
| `php artisan key:generate` | Sekali saat setup awal |
| `php artisan migrate` | Setup awal & setiap deploy ada migrasi baru |
| `php artisan db:seed` | Development / staging (data contoh) |
| `php artisan storage:link` | Sekali — symlink `public/storage` |
| `php artisan config:clear` | Setelah ubah `.env` di development |
| `php artisan cache:clear` | Setelah ubah logic cache / troubleshooting |
| `php artisan route:clear` | Setelah ubah routes |
| `php artisan view:clear` | Setelah ubah Blade |

### Production (setelah deploy)

```bash
composer install --no-dev --optimize-autoloader

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
# php artisan event:cache   # jika memakai event discovery
```

### Modul V2 — risk, notifikasi & antrian

| Perintah | Deskripsi |
|----------|-----------|
| `php artisan risk:calculate` | Hitung ulang risk score **semua** travel; item **risiko tinggi/kritis** otomatis masuk antrian |
| `php artisan risk:calculate --travel=ID` | Hitung ulang risk untuk satu travel |
| `php artisan followup:send-deadline-reminders` | Kirim reminder deadline tindak lanjut (H-7, H-3, H, H+7, H+30, terlambat) |

> **Antrian kerja** tidak punya perintah artisan sendiri — diisi otomatis oleh event aplikasi (lihat bagian 5.1).

### Testing

```bash
# Semua test modul V2
php artisan test --filter=V2

# Matriks akses role V1 + V2 (PHPUnit)
php artisan test --filter=RoleAccessMatrixTest

# Test spesifik
php artisan test --filter=DashboardRepositoryTest
php artisan test --filter=CalculateRiskScoresCommandTest
```

### Uji coba otomatis per role (E2E)

Siapkan database + akun uji + fixture Playwright:

```bash
bash scripts/e2e-prepare.sh
# atau: npm run e2e:prepare
```

Akun contoh (password sudah di-set `is_password_changed = true`):

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `admin@phu.com` | `admin123` |
| Pimpinan | `pimpinan@phu.local` | `password123` |
| Pengawas | `pengawas.lombokbarat@phu.local` | `password123` |
| Kabupaten | `kabupaten.lombokbarat@phu.com` | `password123` |
| User Travel | `lombokbarat.travel@phu.com` | `password123` |

Browser (Playwright + Chrome):

```bash
npm install
npm run e2e:install          # sekali: unduh Chromium
php artisan serve            # terminal terpisah
npm run e2e                  # alur bisnis lengkap (7 langkah, browser tampil)
npm run e2e:matrix           # matriks akses route (opsional, banyak login)
HEADLESS=1 npm run e2e       # mode headless (tanpa jendela)
npm run e2e:ui               # mode interaktif
```

Fixture route diekspor ke `e2e/fixtures/accounts.json` setiap kali seeder dijalankan.

**Alur `e2e/journey/lombok-barat-flow.spec.ts` (serial):**

| # | Peran | Kegiatan |
|---|--------|----------|
| 1 | Travel | BA Pemberangkatan: buat, unggah PDF, ajukan |
| 2 | Kabupaten | Proses → setujui → cetak |
| 3 | Pengawas | BA Pemeriksaan: DRAFT → SCHEDULED → ON_PROGRESS → temuan → WAITING_FOLLOWUP |
| 4 | Travel | Unggah bukti tindak lanjut → FOLLOWUP_UPLOADED |
| 5 | Pengawas | Verifikasi TL → temuan VERIFIED |
| 6 | Pengawas | Tutup pengawasan: VERIFIED → CLOSED |
| 7 | Pengawas | Cek riwayat audit (`/v2/audit-log`) — create/update pengawasan, upload/approve TL |

Set `.env` → `E2E_TESTING=true` agar rate limit login tidak mengganggu pergantian peran.

### Debug & inspeksi

```bash
php artisan route:list --path=v2
php artisan schedule:list
php artisan tinker
```

---

## 5. Scheduler (cron) — wajib di production

Dua perintah V2 terdaftar di `app/Console/Kernel.php`:

| Jadwal | Perintah | Dampak ke antrian | Log |
|--------|----------|-------------------|-----|
| Setiap hari **00:30** | `risk:calculate` | Travel berisiko tinggi/kritis masuk antrian | `storage/logs/risk-calculate.log` |
| Setiap hari **08:00** | `followup:send-deadline-reminders` | Notifikasi email/in-app ke user travel | `storage/logs/deadline-reminders.log` |

### Cara menjalankan di production

Tambahkan **satu baris** crontab pada user web server (mis. `www-data`):

```cron
* * * * * cd /path/ke/PHU && php artisan schedule:run >> /dev/null 2>&1
```

Ganti `/path/ke/PHU` dengan path absolut instalasi Anda.

### Cara menjalankan di development

```bash
# Lihat jadwal terdaftar
php artisan schedule:list

# Jalankan semua task yang jatuh tempo sekarang (tes sekali)
php artisan schedule:run

# Jalankan perintah individual tanpa menunggu cron
php artisan risk:calculate
php artisan followup:send-deadline-reminders
```

### 5.1 Antrian kerja pengawasan — tanpa worker/cron

Modul **Antrian Kerja** (`/v2/antrian`) **tidak memerlukan** queue worker atau cron terpisah. Item antrian dibuat/diperbarui secara real-time:

| Jenis antrian | Pemicu | Kapan |
|---------------|--------|-------|
| Pengaduan baru | Submit pengaduan (internal / publik) | Langsung saat form disimpan |
| Skor risiko tinggi/kritis | `risk:calculate` atau recalculate manual | Setelah perhitungan risk score |
| Verifikasi tindak lanjut | Travel submit bukti followup | Langsung saat upload disimpan |
| Deadline temuan | Buka halaman `/v2/antrian` | Disinkronkan saat pengawas/admin membuka antrian |

**Siapa yang mengakses:** role `admin` (seluruh NTB) dan `pengawas` (scoped per kabupaten).

---

## 6. Queue worker (opsional)

Default: `QUEUE_CONNECTION=sync` — **tidak perlu menjalankan worker**.

Worker hanya diperlukan jika Anda mengubah `.env` ke `redis` atau `database`:

```bash
# Buat tabel jobs (jika pakai driver database)
php artisan queue:table
php artisan migrate

# Jalankan worker (development) — biarkan terminal terbuka
php artisan queue:work --tries=3

# Tes singkat: proses satu job lalu keluar
php artisan queue:work --once
```

**Production** — jalankan worker via Supervisor agar restart otomatis:

```ini
; /etc/supervisor/conf.d/phu-worker.conf
[program:phu-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/ke/PHU/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/ke/PHU/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start phu-worker:*
```

---

## 7. Permission folder (Linux server)

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

Pastikan `storage/app/followups/` dapat ditulis (upload lampiran tindak lanjut — disimpan **private**, diunduh via route terautentikasi).

---

## 8. Route modul V2

Semua route V2 memakai prefix `/v2`, middleware `auth` + `password.changed`:

| URL | Modul | Akses utama |
|-----|-------|-------------|
| `/v2/antrian` | Antrian kerja pengawasan | Admin, Pengawas |
| `/v2/dashboard` | Dashboard eksekutif | Admin |
| `/v2/monitoring` | Monitoring operasional | Admin |
| `/v2/pengawasan` | BA Pemeriksaan (inspeksi pengawasan PPIU) | Admin, Pengawas |
| `/v2/master/checklist` | Master checklist | Admin |
| `/v2/followup` | Tindak lanjut temuan | Admin, Pengawas, User travel |
| `/v2/risk` | Risk scoring | Admin, Pengawas (baca) |
| `/v2/compliance` | Profil kepatuhan travel | Admin, Pengawas |

---

## 9. Checklist deploy production

- [ ] `.env` — `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL` benar
- [ ] Database credentials & `php artisan migrate --force` (termasuk `pengawasan_antrian` & role `pengawas`)
- [ ] Akun **Pengawas** dibuat per kabupaten (seeder atau Manajemen User)
- [ ] `CACHE_DRIVER=redis` + Redis berjalan
- [ ] Cron `schedule:run` aktif (wajib untuk risk score & reminder deadline)
- [ ] Queue worker — **hanya** jika `QUEUE_CONNECTION` ≠ `sync`
- [ ] `php artisan config:cache` & `route:cache`
- [ ] `storage:link` & permission `storage/` OK
- [ ] Mail SMTP dikonfigurasi (untuk reminder deadline)
- [ ] HTTPS (`SESSION_SECURE_COOKIE=true` jika full HTTPS)
- [ ] Backup database terjadwal

---

## 10. Troubleshooting

| Gejala | Solusi |
|--------|--------|
| 403 di semua route `/v2` | Login dulu; ganti password default (`/change-password`) |
| 403 di `/v2/antrian` untuk Pengawas | Pastikan `users.kabupaten` terisi & role = `pengawas` |
| Antrian kosong padahal ada pengaduan | Pengaduan harus dibuat **setelah** migrasi `pengawasan_antrian`; item lama tidak di-backfill otomatis |
| Item risiko tidak muncul di antrian | Jalankan `php artisan risk:calculate`; pastikan cron 00:30 aktif |
| Deadline temuan tidak muncul | Buka `/v2/antrian` sekali (sync saat load halaman) atau pastikan temuan punya `deadline` & belum closed |
| Dashboard lambat | Set `CACHE_DRIVER=redis`; pastikan Redis jalan |
| Risk score tidak terupdate | Cek cron & log `storage/logs/risk-calculate.log`; jalankan manual `php artisan risk:calculate` |
| Upload followup gagal | Cek permission `storage/app/`; `FILESYSTEM_DISK=local` |
| Reminder tidak terkirim | Cek `MAIL_*`; jalankan `php artisan followup:send-deadline-reminders` |
| Notifikasi pengaduan tidak muncul | Notifikasi disimpan ke tabel `notifications` (sync); cek user `admin`/`pengawas` di kabupaten travel terkait |
| Class / config tidak berubah setelah deploy | `php artisan optimize:clear` lalu `config:cache` ulang |

---

## Dokumentasi terkait

- [FITUR_SISTEM.md](./FITUR_SISTEM.md) — fitur lengkap V1
- [guidev2/](./guidev2/) — spesifikasi modul V2 (dashboard, pengawasan, risk, dll.)
- [.env.example](./.env.example) — template variabel environment

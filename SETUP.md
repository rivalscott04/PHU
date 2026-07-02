# Setup & Deployment — PHU (termasuk Modul V2)

Panduan ini mencakup konfigurasi environment, perintah `artisan`, scheduler, cache, dan queue untuk menjalankan sistem PHU (V1 + modul pengawasan V2).

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
- **Supervisor** — menjalankan queue worker & scheduler

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

**Login default** (setelah `migrate` + `AdminSeeder`):

| Role | Email | Password |
|------|-------|----------|
| Admin Kanwil | `admin@phu.com` | `admin123` |
| User travel baru | (dari seeder) | `password123` |

> User travel/kabupaten **wajib ganti password** sebelum mengakses modul V2 (middleware `password.changed`).

---

## 2. Konfigurasi `.env`

### Wajib

```dotenv
APP_NAME=PHU
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

Saat ini perintah risk & notifikasi dijalankan via **scheduler** (sync). Jika nanti ada job async:

```dotenv
QUEUE_CONNECTION=redis   # atau database
```

Lalu jalankan worker (lihat bagian 5).

### Email (reminder deadline followup)

```dotenv
MAIL_MAILER=log        # development: tulis ke log
# MAIL_MAILER=smtp     # production: SMTP sesungguhnya
MAIL_FROM_ADDRESS=noreply@phu.local
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 3. Migrasi database V2

Modul V2 menambahkan tabel berikut (prefix migrasi `2026_07_02_*`):

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
| `notifications` | Notifikasi Laravel (reminder deadline) |

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

### Modul V2 — risk & notifikasi

| Perintah | Deskripsi |
|----------|-----------|
| `php artisan risk:calculate` | Hitung ulang risk score **semua** travel |
| `php artisan risk:calculate --travel=ID` | Hitung ulang risk untuk satu travel |
| `php artisan followup:send-deadline-reminders` | Kirim reminder deadline tindak lanjut (H-7, H-3, H, H+7, H+30, terlambat) |

### Testing

```bash
# Semua test modul V2
php artisan test --filter=V2

# Test spesifik
php artisan test --filter=DashboardRepositoryTest
php artisan test --filter=CalculateRiskScoresCommandTest
```

### Debug & inspeksi

```bash
php artisan route:list --path=v2
php artisan schedule:list
php artisan tinker
```

---

## 5. Scheduler (cron) — wajib di production

Dua perintah V2 terdaftar di `app/Console/Kernel.php`:

| Jadwal | Perintah | Log |
|--------|----------|-----|
| Setiap hari **00:30** | `risk:calculate` | `storage/logs/risk-calculate.log` |
| Setiap hari **08:00** | `followup:send-deadline-reminders` | `storage/logs/deadline-reminders.log` |

Tambahkan ke crontab user web server:

```cron
* * * * * cd /path/ke/PHU && php artisan schedule:run >> /dev/null 2>&1
```

Verifikasi:

```bash
php artisan schedule:list
php artisan schedule:run    # jalankan manual sekali untuk tes
```

---

## 6. Queue worker (opsional)

Default: `QUEUE_CONNECTION=sync` (perintah dijalankan langsung, tanpa worker).

Jika diubah ke `redis` atau `database`:

```bash
# Buat tabel jobs (jika pakai driver database)
php artisan queue:table
php artisan migrate

# Jalankan worker (development)
php artisan queue:work --tries=3

# Production — contoh Supervisor (/etc/supervisor/conf.d/phu-worker.conf)
# [program:phu-worker]
# command=php /path/ke/PHU/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
# autostart=true
# autorestart=true
# user=www-data
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

| URL | Modul |
|-----|-------|
| `/v2/dashboard` | Dashboard eksekutif |
| `/v2/monitoring` | Monitoring operasional |
| `/v2/pengawasan` | Inspeksi / pengawasan |
| `/v2/master/checklist` | Master checklist |
| `/v2/followup` | Tindak lanjut temuan |
| `/v2/risk` | Risk scoring |
| `/v2/compliance` | Profil kepatuhan travel |

---

## 9. Checklist deploy production

- [ ] `.env` — `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL` benar
- [ ] Database credentials & `php artisan migrate --force`
- [ ] `CACHE_DRIVER=redis` + Redis berjalan
- [ ] Cron `schedule:run` aktif
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
| Dashboard lambat | Set `CACHE_DRIVER=redis`; pastikan Redis jalan |
| Risk score tidak terupdate | Cek cron & log `storage/logs/risk-calculate.log`; jalankan manual `php artisan risk:calculate` |
| Upload followup gagal | Cek permission `storage/app/`; `FILESYSTEM_DISK=local` |
| Reminder tidak terkirim | Cek `MAIL_*`; jalankan `php artisan followup:send-deadline-reminders` |
| Class / config tidak berubah setelah deploy | `php artisan optimize:clear` lalu `config:cache` ulang |

---

## Dokumentasi terkait

- [FITUR_SISTEM.md](./FITUR_SISTEM.md) — fitur lengkap V1
- [guidev2/](./guidev2/) — spesifikasi modul V2 (dashboard, pengawasan, risk, dll.)
- [.env.example](./.env.example) — template variabel environment

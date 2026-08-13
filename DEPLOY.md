# Panduan Deploy Production: PANTAU

Dokumen ini menjelaskan tata cara deploy aplikasi PANTAU ke server production, termasuk setup **Redis**, **Laravel Reverb** (notifikasi real-time), **Nginx**, **Supervisor**, dan **cron scheduler**.

Untuk instalasi development lokal, lihat [SETUP.md](./SETUP.md).

---

## Ringkasan arsitektur production

```
                    ┌─────────────────────────────────────┐
                    │            Nginx (HTTPS)            │
                    │  :443  → PHP-FPM (Laravel app)      │
                    │  /app  → Reverb WebSocket (:8080)   │
                    └──────────┬──────────────┬───────────┘
                               │              │
                    ┌──────────▼──┐    ┌──────▼──────┐
                    │   MySQL     │    │   Reverb    │
                    │  (database) │    │  (WebSocket)│
                    └─────────────┘    └──────┬──────┘
                                              │
                    ┌──────────┐       ┌──────▼──────┐
                    │  Redis   │◄──────│  Laravel    │
                    │  (cache) │       │  (broadcast)│
                    └──────────┘       └─────────────┘

Proses latar belakang (Supervisor + cron):
  • php artisan reverb:start, WebSocket server (wajib jika notifikasi real-time)
  • php artisan queue:work, opsional (hanya jika QUEUE_CONNECTION ≠ sync)
  • php artisan schedule:run, cron setiap menit (wajib)
```

| Komponen | Wajib? | Fungsi |
|----------|--------|--------|
| Nginx + PHP-FPM | Ya | Melayani HTTP/HTTPS |
| MySQL | Ya | Database aplikasi |
| Redis | Sangat disarankan | Cache dashboard V2 (performa) |
| Reverb | Disarankan | Notifikasi real-time di bell icon |
| Cron scheduler | Ya | Risk score harian & reminder deadline |
| Queue worker | Tidak (default) | Hanya jika `QUEUE_CONNECTION=redis` |

---

## 1. Prasyarat server

### Spesifikasi minimum

| Komponen | Versi |
|----------|-------|
| OS | Ubuntu 22.04 / 24.04 LTS (atau Debian 12+) |
| PHP | 8.2+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` atau `imagick`, `bcmath`, `redis` (phpredis) |
| Composer | 2.x |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Redis | 6.x+ |
| Nginx | 1.18+ |
| Node.js | Opsional (tidak wajib, asset frontend sudah pre-built) |

### Instalasi paket (Ubuntu)

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.2 + ekstensi
sudo apt install -y nginx mysql-server redis-server \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-redis \
  git unzip supervisor certbot python3-certbot-nginx

# Composer (jika belum ada)
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verifikasi:

```bash
php -v          # >= 8.2
redis-cli ping  # PONG
mysql --version
nginx -v
```

---

## 2. Persiapan database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE phu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'phu_user'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON phu.* TO 'phu_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Deploy kode aplikasi

```bash
# Buat direktori deploy
sudo mkdir -p /var/www/phu
sudo chown -R $USER:www-data /var/www/phu

# Clone repository
git clone https://github.com/rivalscott04/PHU.git /var/www/phu
cd /var/www/phu

# Dependensi PHP (tanpa dev packages)
composer install --no-dev --optimize-autoloader

# Environment
cp .env.example .env
php artisan key:generate
```

Edit `/var/www/phu/.env`, lihat [bagian 4](#4-konfigurasi-env-production).

```bash
# Migrasi & symlink storage
php artisan migrate --force
php artisan storage:link

# Seed data awal (akun inti, master checklist, daftar maskapai)
# WAJIB dijalankan terpisah setelah migrasi, bukan di tengah migrasi.
php artisan db:seed --force

# Optimasi Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Instalasi baru:** `db:seed` harus dijalankan sebagai langkah tersendiri.
> Migrasi `2026_07_02_100018_run_all_seeders` dulu memanggil `DatabaseSeeder`
> dari dalam migrasi, dan itu membuat instalasi baru gagal begitu ada seeder
> yang menyentuh tabel dari migrasi yang lebih belakang. Migrasi itu sekarang
> kosong. Server lama tidak terpengaruh karena catatannya sudah tersimpan.
>
> Jangan pernah menjalankan `DevTravelSeeder` di production. Isinya travel
> contoh untuk development; data travel live datang dari registrasi mandiri.

### Permission folder

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

---

## 4. Konfigurasi `.env` production

Salin template berikut ke `.env` dan sesuaikan nilai yang ditandai `GANTI_*`.

```dotenv
# --- Aplikasi ---
APP_NAME=PANTAU
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pantau.example.com

# --- Database ---
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phu
DB_USERNAME=phu_user
DB_PASSWORD=GANTI_PASSWORD_DB

# --- Session (HTTPS wajib) ---
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true

# --- Cache (Redis disarankan) ---
CACHE_DRIVER=redis

# --- Queue (default sync, tidak perlu worker) ---
QUEUE_CONNECTION=sync

# --- Filesystem ---
FILESYSTEM_DISK=local

# --- Email ---
MAIL_MAILER=smtp
MAIL_HOST=GANTI_SMTP_HOST
MAIL_PORT=587
MAIL_USERNAME=GANTI_SMTP_USER
MAIL_PASSWORD=GANTI_SMTP_PASS
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pantau.example.com
MAIL_FROM_NAME="${APP_NAME}"

# --- Logging ---
LOG_CHANNEL=stack
LOG_LEVEL=warning

# --- Redis ---
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# --- Broadcasting (Reverb) ---
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=GANTI_APP_ID
REVERB_APP_KEY=GANTI_APP_KEY
REVERB_APP_SECRET=GANTI_APP_SECRET

# PHP → Reverb (server-side publish). WAJIB loopback agar submit form
# (pengaduan / BA pemeriksaan) tidak hang lewat HTTPS publik.
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Browser → Reverb (WebSocket lewat Nginx /app). Domain publik HTTPS.
REVERB_CLIENT_HOST=pantau.example.com
REVERB_CLIENT_PORT=443
REVERB_CLIENT_SCHEME=https

# Server internal Reverb (bind lokal)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

### Generate kredensial Reverb

Jalankan sekali saat setup awal:

```bash
cd /var/www/phu
php artisan reverb:install
```

Perintah di atas mengisi `REVERB_APP_ID`, `REVERB_APP_KEY`, dan `REVERB_APP_SECRET` di `.env`.

Atau generate manual:

```bash
# App ID (angka acak)
echo "REVERB_APP_ID=$(shuf -i 100000-999999 -n 1)"

# App Key & Secret (string acak)
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Setelah mengubah `.env`, selalu refresh cache:

```bash
php artisan config:clear
php artisan config:cache
```

---

## 5. Setup Redis

### Instalasi & autostart

```bash
sudo systemctl enable redis-server
sudo systemctl start redis-server
redis-cli ping   # harus: PONG
```

### Keamanan (production)

Edit `/etc/redis/redis.conf`:

```conf
bind 127.0.0.1
# requirepass GANTI_PASSWORD_REDIS   # aktifkan jika perlu
```

```bash
sudo systemctl restart redis-server
```

Jika pakai password, update `.env`:

```dotenv
REDIS_PASSWORD=GANTI_PASSWORD_REDIS
```

### Verifikasi dari Laravel

```bash
php artisan tinker
>>> Cache::store('redis')->put('test', 'ok', 60);
>>> Cache::store('redis')->get('test');
# harus return: "ok"
```

Redis dipakai untuk:

- **Cache dashboard V2** (`CACHE_DRIVER=redis`): wajib disarankan di production
- **Queue worker**, hanya jika `QUEUE_CONNECTION=redis`
- **Reverb scaling**, hanya jika menjalankan lebih dari 1 instance Reverb (`REVERB_SCALING_ENABLED=true`)

---

## 6. Setup Laravel Reverb

Reverb meneruskan notifikasi database ke browser secara real-time (bell icon di header). Tanpa Reverb, notifikasi tetap tersimpan di database tetapi **tidak muncul otomatis**, user harus refresh halaman.

Badge angka di sidebar ikut channel yang sama: setiap notifikasi masuk, browser memanggil `GET /v2/sidebar-badges` lalu mengecat ulang badge. Jika Reverb mati, sidebar otomatis turun ke polling 60 detik, jadi angkanya tetap benar, hanya lebih lambat.

### 6.1 Jalankan manual (tes awal)

```bash
cd /var/www/phu
php artisan reverb:start
```

Buka aplikasi, login, lalu trigger notifikasi (mis. submit pengaduan). Badge bell harus update tanpa refresh.

### 6.2 Production via Supervisor

Buat file `/etc/supervisor/conf.d/phu-reverb.conf`:

```ini
[program:phu-reverb]
process_name=%(program_name)s
command=php /var/www/phu/artisan reverb:start
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/phu/storage/logs/reverb.log
stopwaitsecs=3600
```

Aktifkan:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start phu-reverb
sudo supervisorctl status
```

### 6.3 Reverse proxy Nginx (WebSocket)

Reverb listen di port **8080** internal. Browser connect via HTTPS port **443**, Nginx meneruskan koneksi WebSocket.

Tambahkan di dalam block `server` Nginx (lihat juga [bagian 7](#7-konfigurasi-nginx)):

```nginx
# WebSocket Reverb
location /app {
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_read_timeout 86400;
    proxy_pass http://127.0.0.1:8080;
}
```

Pastikan `.env` memisahkan host publisher (PHP) dan host browser:

```dotenv
# PHP publish ke Reverb lokal
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Browser connect via HTTPS/Nginx
REVERB_CLIENT_HOST=pantau.example.com
REVERB_CLIENT_PORT=443
REVERB_CLIENT_SCHEME=https
```

Frontend membaca `REVERB_CLIENT_*` lewat `notification-bell.blade.php` → `public/js/notifications-realtime.js`.

> **Penting:** jangan set `REVERB_HOST` ke domain publik. PHP akan publish event lewat HTTPS publik; jika Nginx hanya mem-proxy `/app` (WebSocket) atau Reverb mati, request create pengaduan/pengawasan bisa hang tanpa respon.

### 6.4 Scaling (opsional, multi-server)

Hanya diperlukan jika menjalankan **lebih dari satu** proses Reverb (load balancing):

```dotenv
REVERB_SCALING_ENABLED=true
REVERB_SCALING_CHANNEL=reverb
```

Redis wajib aktif untuk scaling. Untuk single-server biasa, biarkan `REVERB_SCALING_ENABLED=false` (default).

---

## 7. Konfigurasi Nginx

Buat `/etc/nginx/sites-available/phu`:

```nginx
server {
    listen 80;
    server_name pantau.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name pantau.example.com;
    root /var/www/phu/public;

    index index.php;
    charset utf-8;

    # SSL (sesuaikan path setelah certbot)
    ssl_certificate     /etc/letsencrypt/live/pantau.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/pantau.example.com/privkey.pem;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # WebSocket Reverb
    location /app {
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400;
        proxy_pass http://127.0.0.1:8080;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/phu /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL dengan Certbot

```bash
sudo certbot --nginx -d pantau.example.com
```

---

## 8. Cron scheduler (wajib)

Scheduler menjalankan perhitungan risk score dan reminder deadline secara otomatis.

```bash
sudo crontab -u www-data -e
```

Tambahkan:

```cron
* * * * * cd /var/www/phu && php artisan schedule:run >> /dev/null 2>&1
```

| Jadwal | Perintah | Fungsi |
|--------|----------|--------|
| Setiap hari 00:30 | `risk:calculate` | Hitung ulang skor risiko travel |
| Setiap hari 08:00 | `followup:send-deadline-reminders` | Kirim reminder deadline tindak lanjut |

Log: `storage/logs/risk-calculate.log` dan `storage/logs/deadline-reminders.log`.

Verifikasi:

```bash
sudo -u www-data php /var/www/phu/artisan schedule:list
```

---

## 9. Queue worker (opsional)

Default `QUEUE_CONNECTION=sync`, **tidak perlu worker**.

Jika ingin notifikasi/broadcast di background (disarankan untuk traffic tinggi):

```dotenv
QUEUE_CONNECTION=redis
```

Buat `/etc/supervisor/conf.d/phu-worker.conf`:

```ini
[program:phu-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/phu/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/phu/storage/logs/worker.log
```

```bash
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start phu-worker:*
```

---

## 10. Prosedur update/deploy ulang

Setiap kali ada release baru:

```bash
cd /var/www/phu

# Maintenance mode
php artisan down

# Pull kode terbaru
git pull origin master

# Dependensi & migrasi
composer install --no-dev --optimize-autoloader
php artisan migrate --force

# WAJIB: bersihkan cache lama sebelum rebuild (UI/Blade/CSS link tidak akan update tanpa ini)
php artisan optimize:clear
php artisan view:clear

# Rebuild cache production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reload PHP-FPM agar OPcache tidak menyimpan file PHP lama
sudo systemctl reload php8.2-fpm

# Restart proses latar belakang
sudo supervisorctl restart phu-reverb
# sudo supervisorctl restart phu-worker:*   # jika pakai queue

php artisan up
```

> **Penting:** Perubahan tampilan dashboard (warna tenang, typography, partial Blade) **bukan** file CSS terpisah saja, sebagian besar ada di `resources/views/`. Jika hanya `git pull` tanpa `view:clear`, server bisa masih merender HTML/JS versi lama meskipun file di `public/css/` sudah baru.

> **Rilis alur registrasi cabang:** rilis ini menambah kolom pada tabel
> `travel_cabang`, jadi `php artisan migrate --force` wajib jalan sebelum kode
> baru dipakai. Layar PPIU Cabang membaca `registration_status` dan akan error
> tanpa migrasi tersebut. Perubahan JavaScript ada di `public/js/pdf-preview.js`
> dan `public/js/confirm-dialogs.js`, keduanya berkas statis, jadi minta petugas
> melakukan hard refresh sekali setelah deploy. Detail alurnya ada di
> [docs/ALUR_REGISTRASI.md](./docs/ALUR_REGISTRASI.md).

### Verifikasi cepat setelah deploy

```bash
# Commit terbaru harus sama dengan GitHub
git log -1 --oneline

# File typography harus ada
test -f public/css/app-typography.css && echo "OK: app-typography.css"

# Layout harus mereferensikan typography (cek sumber Blade, bukan cache browser)
grep -n app-typography resources/views/layouts/app.blade.php
```

Di browser (DevTools → Elements → `<head>`), pastikan ada:

```html
<link href="/css/app-typography.css?v=..." rel="stylesheet" />
```

Jika file CSS ada tapi tag `<link>` tidak muncul di HTML, jalankan ulang `php artisan view:clear && php artisan view:cache`.

Jika pakai **Cloudflare**, purge cache untuk URL HTML (`/` atau `/login`) setelah deploy, file CSS statis biasanya sudah miss, tapi halaman Blade bisa tertahan di edge cache.

---

## 11. Checklist deploy production

### Infrastruktur

- [ ] PHP 8.2+ dengan ekstensi lengkap (`phpredis` untuk Redis)
- [ ] MySQL database dibuat & kredensial aman
- [ ] Redis berjalan (`redis-cli ping` → PONG)
- [ ] Nginx + PHP-FPM dikonfigurasi
- [ ] SSL/HTTPS aktif (Certbot)
- [ ] Firewall: buka 80, 443; **tutup** 8080 dari publik (hanya localhost)

### Aplikasi

- [ ] `.env`, `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL` benar
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] Permission `storage/` & `bootstrap/cache/` OK
- [ ] `CACHE_DRIVER=redis`
- [ ] Mail SMTP dikonfigurasi

### Reverb (notifikasi real-time)

- [ ] `BROADCAST_CONNECTION=reverb`
- [ ] `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` terisi
- [ ] `REVERB_HOST=127.0.0.1`, `REVERB_PORT=8080`, `REVERB_SCHEME=http` (publisher PHP)
- [ ] `REVERB_CLIENT_HOST` = domain HTTPS, `REVERB_CLIENT_SCHEME=https`, `REVERB_CLIENT_PORT=443` (browser)
- [ ] Supervisor `phu-reverb` running
- [ ] Nginx proxy `/app` ke `127.0.0.1:8080`
- [ ] Tes: notifikasi muncul tanpa refresh halaman
- [ ] Tes: badge sidebar bertambah tanpa refresh saat travel mengajukan BA / mendaftar

### Scheduler & opsional

- [ ] Cron `schedule:run` aktif (user `www-data`)
- [ ] Queue worker, hanya jika `QUEUE_CONNECTION=redis`
- [ ] Backup database terjadwal
- [ ] Akun Pengawas dibuat per kabupaten

---

## 12. Verifikasi pasca-deploy

```bash
# Health check Laravel
curl -s https://pantau.example.com/up

# Redis cache
php artisan tinker --execute="Cache::store('redis')->put('deploy_test','ok',60); echo Cache::store('redis')->get('deploy_test');"

# Reverb process
sudo supervisorctl status phu-reverb

# Scheduler
php artisan schedule:list

# Log error
tail -f storage/logs/laravel.log
tail -f storage/logs/reverb.log
```

### Tes notifikasi real-time

1. Login sebagai admin/pengawas di browser A
2. Di browser B (atau tab incognito), submit pengaduan publik
3. Bell icon di browser A harus update badge **tanpa refresh**

Jika tidak jalan, buka DevTools → Network → filter `WS`, harus ada koneksi WebSocket ke `wss://pantau.example.com/app/...`.

---

## 13. Troubleshooting

| Gejala | Penyebab umum | Solusi |
|--------|---------------|--------|
| Submit pengaduan/pengawasan hang, tidak ada respon | `REVERB_HOST` mengarah ke domain publik / Reverb mati | Set `REVERB_HOST=127.0.0.1`, `REVERB_PORT=8080`, `REVERB_SCHEME=http`; isi `REVERB_CLIENT_*` untuk browser; restart `php-fpm` + `config:cache` |
| Notifikasi tidak real-time | Reverb tidak jalan | `sudo supervisorctl status phu-reverb`; cek `storage/logs/reverb.log` |
| WebSocket gagal connect (403/502) | Nginx proxy belum benar | Pastikan block `location /app` ada; Reverb listen di 8080 |
| WebSocket connect tapi tidak dapat event | Kredensial Reverb salah | Regenerate dengan `php artisan reverb:install`; `config:cache` ulang |
| Bell icon tidak load Echo | `BROADCAST_CONNECTION` bukan `reverb` | Set `BROADCAST_CONNECTION=reverb` + isi `REVERB_APP_KEY` |
| Dashboard lambat | Cache masih file | Set `CACHE_DRIVER=redis`; pastikan Redis jalan |
| `Connection refused` Redis | Redis mati / bind salah | `sudo systemctl status redis-server`; cek `bind 127.0.0.1` |
| 500 setelah ubah `.env` | Config cache stale | `php artisan config:clear && php artisan config:cache` |
| UI/CSS beda dengan lokal setelah deploy | View cache / OPcache / Cloudflare HTML cache | `php artisan optimize:clear && php artisan view:clear && php artisan view:cache && sudo systemctl reload php8.2-fpm`; purge Cloudflare; cek `<head>` punya `app-typography.css` |
| Risk score tidak update | Cron tidak aktif | Cek crontab `www-data`; jalankan manual `php artisan risk:calculate` |
| Upload gagal | Permission storage | `chown -R www-data:www-data storage`; `chmod -R ug+rwx storage` |
| Mixed content error (WS) | `REVERB_CLIENT_SCHEME=http` di HTTPS | Set `REVERB_CLIENT_SCHEME=https`, `REVERB_CLIENT_PORT=443` |
| `Table 'bap_airlines' doesn't exist` saat instalasi baru | Seeder dipanggil dari dalam migrasi (sudah diperbaiki) | Pastikan kode terbaru; jalankan `php artisan migrate --force` lalu `php artisan db:seed --force` terpisah |
| `Unknown column 'registration_status'` di layar cabang | Migrasi alur registrasi cabang belum jalan | `php artisan migrate --force`, lalu `php artisan optimize:clear` |
| Travel di Lombok Utara tidak bisa daftar | Versi lama `NtbKabupatenMap` hanya punya 9 wilayah | Pastikan kode terbaru; `php artisan optimize:clear` |
| Tombol verifikasi cabang tidak muncul | Cabang berstatus `approved` (data lama dianggap sah) | Normal. Tombol hanya untuk status `pending` dan `menunggu_kanwil` |
| Cabang tidak muncul di daftar petugas kabupaten | Kolom `kabupaten` tidak cocok daftar kanonik NTB | Perbaiki lewat form edit cabang; nilai bebas hasil impor lama tidak akan cocok |
| Tombol pratinjau dokumen tidak membuka apa apa | `storage:link` belum dibuat atau berkas hilang | `php artisan storage:link`; cek berkas ada di `storage/app/public` |
| Halaman scroll aneh setelah menutup pratinjau dokumen | `public/js/pdf-preview.js` versi lama | Pastikan kode terbaru; `php artisan view:clear` dan hard refresh browser |
| PIC diminta ganti password padahal buat sendiri | Versi lama menandai akun sebagai password default | Pastikan kode terbaru; akun lama bisa diperbaiki dengan set `is_password_changed = 1` |

### Perintah diagnostik

```bash
# Cek konfigurasi broadcasting
php artisan tinker --execute="echo config('broadcasting.default');"

# Restart semua proses
sudo supervisorctl restart phu-reverb
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm

# Clear semua cache
php artisan optimize:clear
php artisan config:cache
```

---

## 14. Development lokal (Reverb + Redis)

Untuk tes Reverb di mesin lokal:

```bash
# Terminal 1: aplikasi
php artisan serve

# Terminal 2: Reverb
php artisan reverb:start

# Terminal 3: Redis (jika belum jalan)
redis-server
```

`.env` lokal:

```dotenv
BROADCAST_CONNECTION=reverb
CACHE_DRIVER=redis
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_APP_ID=local
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
```

Generate kredensial: `php artisan reverb:install`

---

## Dokumentasi terkait

- [SETUP.md](./SETUP.md): instalasi development, perintah artisan, modul V2
- [.env.example](./.env.example): template variabel environment
- [README.md](./README.md): ringkasan fitur aplikasi
- [docs/ALUR_REGISTRASI.md](./docs/ALUR_REGISTRASI.md) : alur registrasi pusat dan cabang, verifikasi berjenjang, dan troubleshooting-nya

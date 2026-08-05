# Panduan Deploy Production — PANTAU

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
  • php artisan reverb:start     — WebSocket server (wajib jika notifikasi real-time)
  • php artisan queue:work       — opsional (hanya jika QUEUE_CONNECTION ≠ sync)
  • php artisan schedule:run     — cron setiap menit (wajib)
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
| Node.js | Opsional (tidak wajib — asset frontend sudah pre-built) |

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

Edit `/var/www/phu/.env` — lihat [bagian 4](#4-konfigurasi-env-production).

```bash
# Migrasi & symlink storage
php artisan migrate --force
php artisan storage:link

# Optimasi Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

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

# Host yang dilihat browser (domain publik)
REVERB_HOST=pantau.example.com
REVERB_PORT=443
REVERB_SCHEME=https

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

- **Cache dashboard V2** (`CACHE_DRIVER=redis`) — wajib disarankan di production
- **Queue worker** — hanya jika `QUEUE_CONNECTION=redis`
- **Reverb scaling** — hanya jika menjalankan lebih dari 1 instance Reverb (`REVERB_SCALING_ENABLED=true`)

---

## 6. Setup Laravel Reverb

Reverb meneruskan notifikasi database ke browser secara real-time (bell icon di header). Tanpa Reverb, notifikasi tetap tersimpan di database tetapi **tidak muncul otomatis** — user harus refresh halaman.

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

Reverb listen di port **8080** internal. Browser connect via HTTPS port **443** — Nginx meneruskan koneksi WebSocket.

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

Pastikan `.env` client-side match dengan domain publik:

```dotenv
REVERB_HOST=pantau.example.com   # sama dengan domain HTTPS
REVERB_PORT=443
REVERB_SCHEME=https
```

Frontend membaca konfigurasi ini lewat `notification-bell.blade.php` → `public/js/notifications-realtime.js`.

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

Default `QUEUE_CONNECTION=sync` — **tidak perlu worker**.

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

# Refresh cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart proses latar belakang
sudo supervisorctl restart phu-reverb
# sudo supervisorctl restart phu-worker:*   # jika pakai queue

php artisan up
```

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

- [ ] `.env` — `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL` benar
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] Permission `storage/` & `bootstrap/cache/` OK
- [ ] `CACHE_DRIVER=redis`
- [ ] Mail SMTP dikonfigurasi

### Reverb (notifikasi real-time)

- [ ] `BROADCAST_CONNECTION=reverb`
- [ ] `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` terisi
- [ ] `REVERB_HOST` = domain HTTPS, `REVERB_SCHEME=https`, `REVERB_PORT=443`
- [ ] Supervisor `phu-reverb` running
- [ ] Nginx proxy `/app` ke `127.0.0.1:8080`
- [ ] Tes: notifikasi muncul tanpa refresh halaman

### Scheduler & opsional

- [ ] Cron `schedule:run` aktif (user `www-data`)
- [ ] Queue worker — hanya jika `QUEUE_CONNECTION=redis`
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

Jika tidak jalan, buka DevTools → Network → filter `WS` — harus ada koneksi WebSocket ke `wss://pantau.example.com/app/...`.

---

## 13. Troubleshooting

| Gejala | Penyebab umum | Solusi |
|--------|---------------|--------|
| Notifikasi tidak real-time | Reverb tidak jalan | `sudo supervisorctl status phu-reverb`; cek `storage/logs/reverb.log` |
| WebSocket gagal connect (403/502) | Nginx proxy belum benar | Pastikan block `location /app` ada; Reverb listen di 8080 |
| WebSocket connect tapi tidak dapat event | Kredensial Reverb salah | Regenerate dengan `php artisan reverb:install`; `config:cache` ulang |
| Bell icon tidak load Echo | `BROADCAST_CONNECTION` bukan `reverb` | Set `BROADCAST_CONNECTION=reverb` + isi `REVERB_APP_KEY` |
| Dashboard lambat | Cache masih file | Set `CACHE_DRIVER=redis`; pastikan Redis jalan |
| `Connection refused` Redis | Redis mati / bind salah | `sudo systemctl status redis-server`; cek `bind 127.0.0.1` |
| 500 setelah ubah `.env` | Config cache stale | `php artisan config:clear && php artisan config:cache` |
| Risk score tidak update | Cron tidak aktif | Cek crontab `www-data`; jalankan manual `php artisan risk:calculate` |
| Upload gagal | Permission storage | `chown -R www-data:www-data storage`; `chmod -R ug+rwx storage` |
| Mixed content error (WS) | `REVERB_SCHEME=http` di HTTPS | Set `REVERB_SCHEME=https`, `REVERB_PORT=443` |

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
# Terminal 1 — aplikasi
php artisan serve

# Terminal 2 — Reverb
php artisan reverb:start

# Terminal 3 — Redis (jika belum jalan)
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

- [SETUP.md](./SETUP.md) — instalasi development, perintah artisan, modul V2
- [.env.example](./.env.example) — template variabel environment
- [README.md](./README.md) — ringkasan fitur aplikasi

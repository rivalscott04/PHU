# Akun kantor dan reset password

Seeder `UserSeeder` mencakup 10 kabupaten/kota NTB. Jalankan `php artisan db:seed --class=UserSeeder` bila perlu mengisi akun kantor. Seeder mempertahankan password dan status ganti password akun yang sudah ada. Production memerlukan `PHU_SEED_PASSWORD` untuk akun baru. Akun awal wajib mengganti password saat masuk.

## Pembuatan akun oleh admin

Admin mengisi identitas tanpa password. Setelah disimpan, salin tautan set password atau gunakan tombol WhatsApp ke nomor terdaftar. Pemilik akun membuat password minimal 8 karakter. Tautan berlaku 60 menit dan hanya dapat dipakai sekali. Setelah selesai, pengguna masuk lewat halaman login.

Impor pusat dan cabang menggunakan kolom `nama`, `email`, `nomor_hp`, dan `travel_company`. Kolom password pada berkas lama diabaikan. Setelah impor, buka daftar pengguna dan terbitkan tautan melalui tombol kunci untuk masing-masing akun.

Jika tautan kedaluwarsa atau pengguna lupa password, admin menerbitkan ulang dari daftar pengguna. Tautan lama menjadi tidak berlaku. Password yang sedang dipakai tetap berlaku sampai pemilik akun menyelesaikan reset. Reset juga mengganti remember token; sesi yang sudah aktif tidak dicabut oleh alur ini.

## Pengaktifan

Jalankan migrasi `php artisan migrate` untuk tabel `password_reset_tokens` dan perubahan skema yang masih tertunda. Gunakan `APP_URL` sesuai alamat HTTPS aplikasi.

Secara bawaan `PHU_PASSWORD_RESET_EMAIL_ENABLED=false`: halaman Lupa Password mengarahkan pengguna ke petugas Kanwil. Pengiriman WhatsApp dilakukan petugas, bukan otomatis.

Untuk email mandiri, konfigurasi SMTP dan alamat pengirim yang benar, verifikasi pengiriman, lalu set `PHU_PASSWORD_RESET_EMAIL_ENABLED=true` dan bangun ulang cache konfigurasi jika digunakan. Jangan aktifkan dengan mailer `log` atau `array`. Respons publik tidak membedakan email terdaftar dan tidak terdaftar. Bila email gagal diterima, pengguna dapat meminta bantuan petugas.

## Pengujian lokal

`php vendor/bin/phpunit --filter 'AccountPasswordResetTest|UserSeederSecurityTest' --no-coverage`

Pengujian memakai SQLite in-memory. Direktori `tests` diabaikan Git pada proyek ini, sehingga pengujian lokal tersebut tidak otomatis ikut commit.

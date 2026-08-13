# PANTAU
### Sistem Pengawasan Haji dan Umrah Kanwil Kementerian Haji dan Umroh NTB

PANTAU membantu Kanwil Kementerian Haji dan Umroh Provinsi Nusa Tenggara Barat mengelola penyelenggara perjalanan ibadah, data jamaah, persetujuan keberangkatan, sertifikat resmi, pengaduan masyarakat, dan pengawasan digital travel haji dan umrah, termasuk transparansi publik lewat indeks kepercayaan travel.

Semua proses penting bisa dilacak dari satu tempat: mulai dari pendaftaran travel dan jamaah, pengajuan keberangkatan, hingga pemeriksaan dan tindak lanjut di lapangan.

---

## Untuk siapa aplikasi ini?

| Pengguna | Peran dalam sistem |
|----------|-------------------|
| Kanwil NTB | Mengawasi seluruh wilayah provinsi |
| Kabupaten dan kota | Memproses keberangkatan jamaah di wilayah masing masing |
| Pengawas lapangan | Melakukan pemeriksaan dan menindaklanjuti temuan |
| Pimpinan Kanwil | Memantau gambaran besar tanpa turun ke detail operasional harian |
| Travel PPIU dan PIHK | Mengelola jamaah, mengajukan keberangkatan, dan menanggapi hasil pengawasan |
| Masyarakat | Melihat jadwal keberangkatan, indeks kepercayaan travel, mengirim pengaduan, dan memverifikasi dokumen resmi |

---

## Fitur utama

### Operasional harian

**Registrasi travel mandiri**
Travel PPIU atau PIHK bisa mendaftar akun sendiri lewat halaman publik. Form wizard memandu pengisian data perusahaan, PIC, dan unggah dokumen SK serta akreditasi. Setelah submit, status menunggu verifikasi Kanwil. Admin Kanwil meninjau, menyetujui, atau menolak registrasi beserta catatannya. Travel yang belum disetujui tidak bisa mengakses fitur operasional; yang ditolak bisa mendaftar ulang.

**Data travel**
Mencatat perusahaan travel pusat dan cabang di seluruh NTB, lengkap dengan lisensi, akreditasi, status layanan (umrah, haji, atau haji khusus), dan status registrasi.

**Data jamaah**
Mengelola jamaah umrah, haji reguler, dan pendaftaran haji khusus beserta dokumen pendukungnya. Pendaftaran haji khusus memakai form wizard multi langkah dengan unggah dokumen per tahap. Data bisa diinput manual atau diimpor dari Excel.

**BA Pemberangkatan**
Travel mengajukan Berita Acara Pelaporan Keberangkatan sebagai deklarasi rencana keberangkatan jamaah. Admin atau admin kabupaten meninjau, memproses, lalu menyetujui. Setelah disetujui, dokumen mendapat nomor surat resmi dan tanda tangan elektronik yang bisa diverifikasi publik.

**Jadwal keberangkatan**
Kalender keberangkatan menampilkan rencana berangkat yang sudah disetujui, sehingga jadwal travel terbuka dan mudah dipantau.

**Sertifikat PPIU**
Penerbitan sertifikat resmi untuk travel, dilengkapi kode QR yang bisa dipindai siapa saja untuk memastikan keasliannya.

**Pengaduan masyarakat**
Warga bisa mengirim pengaduan dari halaman depan tanpa harus login, lengkap dengan lampiran bukti (PDF, JPG, atau PNG). Form memiliki validasi langsung dan pemeriksaan keamanan file. Setiap pengaduan mendapat token unik untuk mengecek status dan mengunduh PDF tanggapan resmi setelah selesai. Pengaduan baru otomatis masuk antrian kerja pengawasan. Admin Kanwil dan admin kabupaten (sesuai wilayah) menindaklanjuti hingga status selesai atau ditolak. Riwayat pengaduan yang sudah selesai ditampilkan di halaman depan.

**Pengunduran jamaah**
Travel dapat mengajukan pengunduran jamaah beserta dokumen pendukung untuk diproses admin.

**Beranda per peran**
Setelah login, setiap peran melihat beranda yang relevan dengan tugasnya:
* **Admin Kanwil:** command center dengan kartu antrian (registrasi travel, BA Pemberangkatan, pengaduan, risiko tinggi) dan indikator kondisi (normal, ada antrian, perlu perhatian segera)
* **Admin Kabupaten:** antrian terfilter wilayah kabupaten/kota, plus daftar BA terbaru yang menunggu proses
* **User Travel:** checklist langkah demi langkah (registrasi disetujui → tambah jamaah → ajukan BA → pantau jadwal keberangkatan)

### Pengawasan Digital

Modul ini khusus untuk memastikan travel berjalan sesuai aturan dan standar yang berlaku.

**Dashboard pengawasan**
Ringkasan kondisi wilayah: jumlah pemeriksaan, temuan, risiko, dan kepatuhan travel dalam satu tampilan. Untuk Pimpinan Kanwil tersedia tampilan eksekutif dengan ringkasan narasi otomatis, heatmap per kabupaten, ranking travel, timeline aktivitas, dan peringatan dini. Data dashboard di-cache agar tetap responsif.

**Antrian kerja**
Daftar tugas yang perlu segera ditangani: pengaduan baru, skor risiko tinggi, deadline tindak lanjut temuan, atau verifikasi bukti perbaikan dari travel. Pengawas masuk langsung ke antrian ini setelah login.

**Monitoring PPIU**
Memantau aktivitas tiap travel: keberangkatan, riwayat pengawasan, pengaduan, dan skor risiko. Detail pengaduan per travel dapat dilihat langsung dari halaman monitoring.

**Export laporan**
Unduh data monitoring, pengawasan, dan dashboard ke Excel untuk keperluan arsip atau presentasi.

**BA Pemeriksaan**
Pengawas menjadwalkan inspeksi ke travel, mengisi checklist, mencatat temuan, lalu menunggu travel mengunggah tindak lanjut. Ini berbeda dari BA Pemberangkatan yang mengatur persetujuan keberangkatan jamaah.

**Tindak lanjut temuan**
Travel mengunggah bukti perbaikan. Pengawas memverifikasi: disetujui atau diminta revisi sampai sesuai.

**Skor risiko**
Sistem menghitung prioritas travel yang perlu perhatian lebih, agar pengawasan bisa difokuskan ke yang paling penting.

**Profil kepatuhan**
Gambaran lengkap satu travel: sertifikat, temuan inspeksi, pengaduan, dan riwayat keberangkatan.

**Log aktivitas**
Catatan jejak siapa melakukan apa dan kapan di modul pengawasan, untuk keperluan audit dan akuntabilitas.

**Notifikasi**
Pemberitahuan di dalam aplikasi untuk pengaduan baru, pengingat deadline, dan pembaruan tindak lanjut. Notifikasi real-time muncul langsung di bell icon tanpa perlu refresh halaman (memerlukan Laravel Reverb di production).

### Halaman untuk publik

Tanpa perlu masuk ke sistem, masyarakat bisa:

1. Melihat jadwal keberangkatan di halaman depan
2. Mengirim pengaduan, melihat riwayat pengaduan selesai, dan mengecek status lewat token unik
3. Melihat direktori travel berizin beserta **indeks kepercayaan** (skor kepatuhan berdasarkan data pengawasan Kanwil). Hanya travel dengan registrasi disetujui yang tampil
4. Membuka profil travel publik: izin operasional, akreditasi, riwayat pengawasan, jumlah pengaduan, dan jamaah terlayani
5. Memverifikasi sertifikat PPIU lewat pemindaian QR
6. Memverifikasi tanda tangan elektronik pada BA Pemberangkatan
7. Mendaftarkan travel PPIU atau PIHK lewat form registrasi mandiri

Indeks kepercayaan bukan sertifikat resmi, melainkan ringkasan transparan dari data pengawasan untuk membantu jamaah memilih travel.

---

## Peran pengguna dan tugasnya

Setiap akun masuk ke menu yang sesuai tugasnya. Berikut penjelasan singkat tiap peran.

### Super Admin

Wilayah kerja: seluruh NTB.

Tugas utama:
* Mengelola data travel pusat dan akun pengguna (termasuk peran pengawas dan pimpinan)
* Memverifikasi registrasi travel mandiri (setujui atau tolak beserta catatan)
* Memproses pengaduan masyarakat
* Menyetujui BA Pemberangkatan
* Mengatur master checklist pemeriksaan
* Mengawasi dan menindaklanjuti lewat modul Pengawasan Digital
* Bisa masuk sementara sebagai akun travel atau kabupaten untuk membantu troubleshooting

### Pimpinan Kanwil

Wilayah kerja: seluruh NTB (tampilan eksekutif).

Tugas utama:
* Membaca dashboard pengawasan eksekutif (ringkasan narasi, heatmap, ranking, peringatan dini)
* Memantau monitoring PPIU dan statistik keberangkatan
* Melihat data travel dan jamaah untuk keperluan laporan
* Mengunduh export laporan monitoring dan dashboard
* Tidak memproses BA Pemberangkatan, pengaduan, atau tugas operasional harian

### Pengawas

Wilayah kerja: bisa diatur per kabupaten, beberapa kabupaten, atau seluruh NTB.

Tugas utama:
* Menangani antrian kerja pengawasan
* Menjadwalkan dan melaksanakan BA Pemeriksaan
* Mencatat temuan dan memverifikasi tindak lanjut dari travel
* Memantau skor risiko dan profil kepatuhan travel di wilayahnya
* Melihat log aktivitas pengawasan

### Admin Kabupaten

Wilayah kerja: satu kabupaten atau kota. Akses data dan antrian otomatis difilter sesuai wilayah kerja.

Tugas utama:
* Memproses pengajuan BA Pemberangkatan di wilayahnya
* Mengelola data cabang travel
* Menerbitkan sertifikat PPIU untuk travel di wilayahnya
* Melihat dan memproses pengunduran jamaah
* Memproses pengaduan masyarakat terkait travel di wilayahnya

### User Travel

Wilayah kerja: satu perusahaan travel (pusat atau cabang).

Tugas utama:
* Menunggu verifikasi registrasi (jika mendaftar mandiri) sebelum fitur operasional aktif
* Menginput dan mengelola data jamaah
* Membuat dan mengajukan BA Pemberangkatan
* Melihat jadwal keberangkatan yang sudah disetujui
* Melihat sertifikat milik travel sendiri
* Mengunggah tindak lanjut atas temuan pemeriksaan
* Mengajukan pengunduran jamaah

**Catatan:** PPIU melayani umrah. PIHK melayani haji, umrah, dan haji khusus. Menu yang tampil menyesuaikan jenis layanan travel. Setiap halaman menampilkan panduan alur kerja sesuai peran pengguna.

---

## Alur kerja singkat

**Registrasi travel pusat**
Travel pilih "Kantor Pusat" di form registrasi, isi data izin dan akreditasi, unggah SK izin → status menunggu verifikasi → Kanwil tinjau dan setujui atau tolak → jika disetujui, akun aktif dan fitur operasional terbuka.

**Registrasi travel cabang**
Cabang pilih "Kantor Cabang" dan menunjuk travel pusat yang izinnya sudah disetujui, lalu unggah OSS cabang, akta notaris, KTP kepala cabang, dan SK domisili usaha kelurahan (SK pusat terbaca otomatis, tidak perlu diunggah ulang) → Kantor Kemenag kabupaten/kota di wilayah cabang meninjau dan mengunggah rekomendasi atau berita acara peninjauan → status berpindah ke menunggu Kanwil → Kanwil beri keputusan akhir. Detailnya di [docs/ALUR_REGISTRASI.md](./docs/ALUR_REGISTRASI.md).

**Keberangkatan jamaah**
Travel input jamaah → buat BA Pemberangkatan → ajukan ke Kanwil → admin atau kabupaten tinjau dan setujui → jadwal muncul di kalender → dokumen bisa dicetak dengan tanda tangan elektronik.

**Pengawasan travel**
Pengawas jadwalkan pemeriksaan → isi checklist dan catat temuan → travel unggah bukti perbaikan → pengawas verifikasi → tutup pemeriksaan setelah semua temuan selesai.

**Pengaduan warga**
Warga kirim pengaduan dari halaman depan (dengan lampiran opsional) → masuk antrian kerja pengawasan → admin Kanwil atau kabupaten tanggapi → setelah selesai, pelapor bisa cek status dan unduh PDF lewat token unik.

---

## Mulai menggunakan

Untuk instalasi development, perintah artisan, dan cron, lihat **[SETUP.md](./SETUP.md)**.  
Untuk deploy production (Nginx, Redis, Reverb, Supervisor, SSL), lihat **[DEPLOY.md](./DEPLOY.md)**.  
Untuk persiapan data testing live per peran, lihat **[docs/persiapan/README.md](./docs/persiapan/README.md)**.  
Untuk alur registrasi pusat dan cabang beserta troubleshooting-nya, lihat **[docs/ALUR_REGISTRASI.md](./docs/ALUR_REGISTRASI.md)**.

**Login awal** (setelah migrasi database):

| Peran | Email contoh | Password |
|-------|--------------|----------|
| Super Admin | `admin@phu.com` | `admin123` |
| Pimpinan Kanwil | `pimpinan@phu.local` | `password123` |
| Pengawas | `pengawas.mataram@phu.local` | `password123` |
| Admin Kabupaten | `kota.mataram@phu.com` | `password123` |

Akun **travel tidak di-seed**, data travel live dari registrasi mandiri (lihat [PERSIAPAN_TRAVEL.md](./docs/persiapan/PERSIAPAN_TRAVEL.md)). Untuk development lokal saja: `php artisan db:seed --class=DevTravelSeeder` (PT. Mataram Travel + `mataram.travel@phu.com`), lengkap dengan satu cabang berstatus disetujui di Kota Mataram dan satu cabang berstatus menunggu verifikasi di Lombok Utara, supaya alur peninjauan bisa langsung dicoba.

Seeding dijalankan sebagai langkah tersendiri setelah migrasi (`php artisan db:seed`), bukan dari dalam migrasi.

Akun kabupaten baru wajib mengganti password saat pertama kali masuk.

---

## Teknologi

Dibangun dengan Laravel 12, PHP 8.2+, dan MySQL. Antarmuka memakai template admin Skote (Bootstrap 5). Modul pengawasan (V2) memanfaatkan Redis untuk cache dashboard, Laravel Reverb untuk notifikasi real-time, dan DomPDF untuk dokumen pengaduan.

---

*PANTAU · Kanwil Kementerian Haji dan Umroh NTB*

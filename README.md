# PANTAU
### Sistem Pengawasan Haji dan Umrah Kanwil Kementerian Haji dan Umroh NTB

PANTAU membantu Kanwil Kementerian Haji dan Umroh Provinsi Nusa Tenggara Barat mengelola penyelenggara perjalanan ibadah, data jamaah, persetujuan keberangkatan, sertifikat resmi, pengaduan masyarakat, dan pengawasan digital travel haji dan umrah — termasuk transparansi publik lewat indeks kepercayaan travel.

Semua proses penting bisa dilacak dari satu tempat: mulai dari pendaftaran jamaah, pengajuan keberangkatan, hingga pemeriksaan dan tindak lanjut di lapangan.

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

**Data travel**
Mencatat perusahaan travel pusat dan cabang di seluruh NTB, lengkap dengan lisensi, akreditasi, dan status layanan (umrah, haji, atau haji khusus).

**Data jamaah**
Mengelola jamaah umrah, haji reguler, dan pendaftaran haji khusus beserta dokumen pendukungnya. Data bisa diinput manual atau diimpor dari Excel.

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
Pemberitahuan di dalam aplikasi untuk pengaduan baru, pengingat deadline, dan pembaruan tindak lanjut.

### Halaman untuk publik

Tanpa perlu masuk ke sistem, masyarakat bisa:

1. Melihat jadwal keberangkatan di halaman depan
2. Mengirim pengaduan, melihat riwayat pengaduan selesai, dan mengecek status lewat token unik
3. Melihat direktori travel berizin beserta **indeks kepercayaan** (skor kepatuhan berdasarkan data pengawasan Kanwil)
4. Membuka profil travel publik: izin operasional, akreditasi, riwayat pengawasan, jumlah pengaduan, dan jamaah terlayani
5. Memverifikasi sertifikat PPIU lewat pemindaian QR
6. Memverifikasi tanda tangan elektronik pada BA Pemberangkatan

Indeks kepercayaan bukan sertifikat resmi, melainkan ringkasan transparan dari data pengawasan untuk membantu jamaah memilih travel.

---

## Peran pengguna dan tugasnya

Setiap akun masuk ke menu yang sesuai tugasnya. Berikut penjelasan singkat tiap peran.

### Super Admin

Wilayah kerja: seluruh NTB.

Tugas utama:
* Mengelola data travel pusat dan akun pengguna (termasuk peran pengawas dan pimpinan)
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

Wilayah kerja: satu kabupaten atau kota.

Tugas utama:
* Memproses pengajuan BA Pemberangkatan di wilayahnya
* Mengelola data cabang travel
* Menerbitkan sertifikat PPIU untuk travel di wilayahnya
* Melihat dan memproses pengunduran jamaah
* Memproses pengaduan masyarakat terkait travel di wilayahnya

### User Travel

Wilayah kerja: satu perusahaan travel (pusat atau cabang).

Tugas utama:
* Menginput dan mengelola data jamaah
* Membuat dan mengajukan BA Pemberangkatan
* Melihat jadwal keberangkatan yang sudah disetujui
* Melihat sertifikat milik travel sendiri
* Mengunggah tindak lanjut atas temuan pemeriksaan
* Mengajukan pengunduran jamaah

**Catatan:** PPIU melayani umrah. PIHK melayani haji, umrah, dan haji khusus. Menu yang tampil menyesuaikan jenis layanan travel. Setiap halaman menampilkan panduan alur kerja sesuai peran pengguna.

---

## Alur kerja singkat

**Keberangkatan jamaah**
Travel input jamaah → buat BA Pemberangkatan → ajukan ke Kanwil → admin atau kabupaten tinjau dan setujui → jadwal muncul di kalender → dokumen bisa dicetak dengan tanda tangan elektronik.

**Pengawasan travel**
Pengawas jadwalkan pemeriksaan → isi checklist dan catat temuan → travel unggah bukti perbaikan → pengawas verifikasi → tutup pemeriksaan setelah semua temuan selesai.

**Pengaduan warga**
Warga kirim pengaduan dari halaman depan (dengan lampiran opsional) → masuk antrian kerja pengawasan → admin Kanwil atau kabupaten tanggapi → setelah selesai, pelapor bisa cek status dan unduh PDF lewat token unik.

---

## Mulai menggunakan

Untuk instalasi, konfigurasi server, perintah artisan, cron, dan Redis, lihat **[SETUP.md](./SETUP.md)**.

Untuk dokumentasi fitur lengkap per modul, lihat **[FITUR_SISTEM.md](./FITUR_SISTEM.md)**.

**Login awal** (setelah migrasi database):

| Peran | Email contoh | Password |
|-------|--------------|----------|
| Super Admin | `admin@phu.com` | `admin123` |
| Pimpinan Kanwil | `pimpinan@phu.local` | `password123` |
| Pengawas | `pengawas.lombokbarat@phu.local` | `password123` |
| Admin Kabupaten | `kabupaten.lombokbarat@phu.com` | `password123` |
| User Travel | (dari data seeder) | `password123` |

Akun travel dan kabupaten baru wajib mengganti password saat pertama kali masuk.

---

## Teknologi

Dibangun dengan Laravel 10, PHP 8.1+, dan MySQL. Antarmuka memakai Argon Dashboard. Modul pengawasan (V2) memanfaatkan Redis untuk cache dashboard dan DomPDF untuk dokumen pengaduan.

---

*PANTAU · Kanwil Kementerian Haji dan Umroh NTB*

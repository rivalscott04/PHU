# PANTAU
### Sistem Pengawasan Haji dan Umrah Kanwil Kementerian Haji dan Umroh NTB

PANTAU membantu Kanwil Kementerian Haji dan Umroh Provinsi Nusa Tenggara Barat mengelola penyelenggara perjalanan ibadah, data jamaah, persetujuan keberangkatan, sertifikat resmi, pengaduan masyarakat, dan pengawasan digital travel haji dan umrah.

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
| Masyarakat | Melihat jadwal keberangkatan, mengirim pengaduan, dan memverifikasi dokumen resmi |

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
Warga bisa mengirim pengaduan dari halaman depan tanpa harus login. Admin Kanwil menindaklanjuti dan memberi tanggapan resmi.

**Pengunduran jamaah**
Travel dapat mengajukan pengunduran jamaah beserta dokumen pendukung untuk diproses admin.

### Pengawasan Digital

Modul ini khusus untuk memastikan travel berjalan sesuai aturan dan standar yang berlaku.

**Dashboard pengawasan**
Ringkasan kondisi wilayah: jumlah pemeriksaan, temuan, risiko, dan kepatuhan travel dalam satu tampilan.

**Antrian kerja**
Daftar tugas yang perlu segera ditangani, seperti pengaduan baru, risiko tinggi, deadline tindak lanjut, atau verifikasi bukti perbaikan dari travel.

**Monitoring PPIU**
Memantau aktivitas tiap travel: keberangkatan, riwayat pengawasan, pengaduan, dan skor risiko.

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
2. Mengirim dan mengecek status pengaduan
3. Melihat direktori travel terdaftar
4. Memverifikasi sertifikat PPIU lewat pemindaian QR
5. Memverifikasi tanda tangan elektronik pada BA Pemberangkatan

---

## Peran pengguna dan tugasnya

Setiap akun masuk ke menu yang sesuai tugasnya. Berikut penjelasan singkat tiap peran.

### Super Admin

Wilayah kerja: seluruh NTB.

Tugas utama:
* Mengelola data travel pusat dan akun pengguna
* Memproses pengaduan masyarakat
* Menyetujui BA Pemberangkatan
* Mengatur master checklist pemeriksaan
* Mengawasi dan menindaklanjuti lewat modul Pengawasan Digital
* Bisa masuk sementara sebagai akun travel atau kabupaten untuk membantu troubleshooting

### Pimpinan Kanwil

Wilayah kerja: seluruh NTB (tampilan eksekutif).

Tugas utama:
* Membaca dashboard pengawasan untuk gambaran besar kondisi wilayah
* Memantau monitoring PPIU dan statistik keberangkatan
* Melihat data travel dan jamaah untuk keperluan laporan
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

### User Travel

Wilayah kerja: satu perusahaan travel (pusat atau cabang).

Tugas utama:
* Menginput dan mengelola data jamaah
* Membuat dan mengajukan BA Pemberangkatan
* Melihat jadwal keberangkatan yang sudah disetujui
* Melihat sertifikat milik travel sendiri
* Mengunggah tindak lanjut atas temuan pemeriksaan
* Mengajukan pengunduran jamaah

**Catatan:** PPIU melayani umrah. PIHK melayani haji, umrah, dan haji khusus. Menu yang tampil menyesuaikan jenis layanan travel.

---

## Alur kerja singkat

**Keberangkatan jamaah**
Travel input jamaah → buat BA Pemberangkatan → ajukan ke Kanwil → admin atau kabupaten tinjau dan setujui → jadwal muncul di kalender → dokumen bisa dicetak dengan tanda tangan elektronik.

**Pengawasan travel**
Pengawas jadwalkan pemeriksaan → isi checklist dan catat temuan → travel unggah bukti perbaikan → pengawas verifikasi → tutup pemeriksaan setelah semua temuan selesai.

**Pengaduan warga**
Warga kirim pengaduan dari halaman depan → admin Kanwil tanggapi → status bisa dicek oleh pelapor.

---

## Mulai menggunakan

Untuk instalasi, konfigurasi server, perintah artisan, cron, dan Redis, lihat **[SETUP.md](./SETUP.md)**.

Untuk dokumentasi fitur lengkap per modul, lihat **[FITUR_SISTEM.md](./FITUR_SISTEM.md)**.

**Login awal** (setelah migrasi database):

| Peran | Email contoh | Password |
|-------|--------------|----------|
| Super Admin | `admin@phu.com` | `admin123` |
| Pengawas | `pengawas.lombokbarat@phu.local` | `password123` |
| Admin Kabupaten | `kabupaten.lombokbarat@phu.com` | `password123` |
| User Travel | (dari data seeder) | `password123` |

Akun travel dan kabupaten baru wajib mengganti password saat pertama kali masuk.

---

## Teknologi

Dibangun dengan Laravel 10, PHP 8.1+, dan MySQL. Antarmuka memakai Argon Dashboard.

---

*PANTAU · Kanwil Kementerian Haji dan Umroh NTB*

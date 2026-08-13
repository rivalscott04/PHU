# Alur Registrasi Travel Pusat dan Cabang

Dokumen ini menjelaskan apa yang sudah berjalan di alur registrasi mandiri, siapa
mengerjakan apa, dokumen apa saja yang diminta, dan apa yang harus dilakukan
kalau ada yang error.

---

## 1. Ringkasan alur

Pendaftar memilih jenis pendaftaran lebih dulu di halaman `/registrasi-travel`.
Pilihan itu menentukan formulir dan jalur verifikasinya.

### Pusat

```
Travel mendaftar  ->  Menunggu Verifikasi  ->  Kanwil setujui  ->  Disetujui
                                            \-> Kanwil tolak   ->  Ditolak
```

Kabupaten/kota tidak terlibat. Keputusan sepenuhnya di Kanwil.

### Cabang

```
Cabang mendaftar  ->  Menunggu Verifikasi  ->  Kabko unggah rekomendasi
                                                        |
                                                        v
                                              Menunggu Kanwil  ->  Kanwil setujui  ->  Disetujui
                                                                \-> Kanwil tolak   ->  Ditolak
```

Yang berhak meninjau adalah kantor Kemenag di kabupaten/kota yang dipilih cabang,
bukan kabupaten pusatnya. Kanwil tetap bisa menyetujui langsung tanpa menunggu
rekomendasi, misalnya untuk kasus mendesak, dan dialog konfirmasinya menyebut
eksplisit bahwa tahap peninjauan sedang dilewati.

Penolakan bisa dilakukan Kabko maupun Kanwil, dan wajib disertai alasan. Menolak
akan menghapus akun PIC, jadi pendaftar harus mendaftar ulang.

---

## 2. Data dan dokumen yang diminta

### Sama untuk pusat dan cabang

Nama travel, tanggal izin, nomor izin, nama pimpinan, alamat, dan telepon.

### Khusus pusat

| Isian | Kolom database | Catatan |
|-------|----------------|---------|
| Nama penyelenggara | `travels.Penyelenggara` | Sesuai izin resmi |
| Jenis izin | `travels.Status` | PPIU atau PIHK |
| Nomor SK / NIB | `travels.Pusat` | Nomor izin, bukan nama kota |
| Tanggal SK | `travels.Tanggal` | |
| Masa berlaku izin | `travels.license_expiry` | Wajib, ditampilkan ke publik |
| Akreditasi | `nilai_akreditasi`, `tanggal_akreditasi`, `lembaga_akreditasi` | |
| Scan SK izin | `travels.dokumen_sk` | **Wajib** |
| Scan sertifikat akreditasi | `travels.dokumen_akreditasi` | Opsional, boleh menyusul |

### Khusus cabang

| Isian | Kolom database | Catatan |
|-------|----------------|---------|
| Travel pusat | `travel_cabang.travel_id` | Dipilih dari daftar pusat yang sudah disetujui |
| Kabupaten/kota | `travel_cabang.kabupaten` | Menentukan Kabko mana yang meninjau |
| Nama kepala cabang | `travel_cabang.pimpinan_cabang` | |
| Nomor SK / BA pembukaan cabang | `travel_cabang.SK_BA` | |
| Tanggal SK / BA | `travel_cabang.tanggal` | |
| Alamat dan telepon cabang | `alamat_cabang`, `telepon` | |
| OSS cabang | `travel_cabang.dokumen_oss` | **Wajib** |
| Akta notaris / pembukaan cabang | `travel_cabang.dokumen_akta` | **Wajib** |
| KTP kepala cabang | `travel_cabang.dokumen_ktp_kepala` | **Wajib** |
| SK domisili usaha kelurahan | `travel_cabang.dokumen_sk_du` | **Wajib** |

Nama penyelenggara, nomor SK pusat, pimpinan pusat, dan alamat pusat **tidak
diketik ulang**. Semuanya diambil dari travel pusat yang dipilih. SK izin pusat
juga tidak diunggah ulang, sistem membacanya dari data pusat.

Semua berkas menerima PDF, JPG, atau PNG, maksimal 1,5 MB per berkas.

### Diisi petugas Kabupaten/Kota

| Isian | Kolom database |
|-------|----------------|
| Rekomendasi / BA laporan peninjauan | `travel_cabang.dokumen_rekomendasi` |
| Catatan peninjauan | `travel_cabang.catatan_rekomendasi` |
| Waktu dan pelaku rekomendasi | `recommended_at`, `recommended_by` |

Formatnya berupa dokumen yang ditandatangani manual lalu dipindai dan diunggah.
Sistem tidak membuatkan templatenya.

Catatan peninjauan disimpan permanen dan dibaca Kanwil sebagai bahan keputusan.
Ia tidak ikut terhapus saat pendaftaran disetujui, karena merupakan jejak audit.

---

## 3. Status pendaftaran

Empat status dipakai bersama oleh pusat dan cabang, tersimpan di kolom
`registration_status`.

| Nilai | Label di layar | Arti |
|-------|----------------|------|
| `pending` | Menunggu Verifikasi | Baru masuk. Pusat menunggu Kanwil, cabang menunggu Kabko |
| `menunggu_kanwil` | Menunggu Kanwil | Khusus cabang. Rekomendasi sudah diunggah Kabko |
| `approved` | Disetujui | Selesai. PIC bisa login |
| `rejected` | Ditolak | Akun PIC dihapus, harus daftar ulang |

Data lama otomatis berstatus `approved`, jadi cabang yang sudah tercatat sebelum
fitur ini tidak ikut masuk antrean verifikasi.

Selama status belum `approved`, PIC tidak bisa login dan pesan errornya menyebut
status terkini. Cabang yang belum disetujui juga tidak muncul di direktori
publik, halaman depan, dropdown sertifikat, maupun hitungan dashboard.

---

## 4. Siapa boleh apa

| Aksi | Super Admin (Kanwil) | Admin Kabupaten | Pendaftar |
|------|----------------------|-----------------|-----------|
| Mendaftar pusat atau cabang | ya | ya | ya |
| Melihat cabang di wilayahnya | seluruh NTB | wilayahnya saja | tidak |
| Unggah rekomendasi peninjauan | ya | wilayahnya saja | tidak |
| Setujui pendaftaran cabang | ya | tidak | tidak |
| Tolak pendaftaran cabang | ya | wilayahnya saja | tidak |
| Setujui atau tolak pendaftaran pusat | ya | tidak | tidak |

Membuka data cabang di luar wilayah dibalas 404, bukan 403, supaya keberadaan
datanya tidak bocor.

### Mengubah data izin travel

Nomor izin, tanggal izin, masa berlaku, nilai akreditasi, jenis izin PPIU atau
PIHK, dan kabupaten pengawas hanya boleh diubah **Admin Kanwil** dan
**Kabupaten/Kota wilayah travel tersebut**. Pengawas tidak termasuk, tugasnya
memeriksa kepatuhan bukan menetapkan izin. Travel sendiri sama sekali tidak
boleh, termasuk membuka formulir editnya.

Penjaganya `KabupatenResourceGuard::authorizeTravelAsStaff()`, dengan pasangan
`authorizeCabangAsStaff()` untuk cabang. Jangan memakai `authorizeTravel()` atau
`authorizeCabang()` untuk jalur yang mengubah data, karena keduanya sengaja
longgar agar pemiliknya bisa membaca datanya sendiri.

Aturan yang sama berlaku untuk **penerbitan sertifikat PPIU**: dokumen resmi,
jadi hanya Admin Kanwil dan Kabupaten/Kota wilayahnya, dan travel tidak bisa
membuka formulirnya sekalipun.

### Sertifikat PPIU

Nomor surat dan nomor dokumen **diterbitkan sistem** saat sertifikat disimpan,
di dalam transaksi dengan penguncian baris. Nomor yang dikirim dari luar
formulir diabaikan. Urutannya berjalan sepanjang tahun; bulan tetap dicantumkan
pada nomornya sebagai keterangan bulan terbit, tetapi tidak mengulang hitungan.

Sertifikat yang dihapus hanya ditandai batal, tidak dihapus permanen. Nomornya
sudah beredar, jadi tidak boleh dipakai ulang dokumen lain, dan registri dokumen
resmi perlu jejak apa yang pernah diterbitkan.

**Masa berlaku berakhir setiap 1 Januari**, berapa pun bulan terbitnya. Terbit
Juni 2026 maupun Desember 2026 sama sama berakhir 1 Januari 2027. Tanggalnya
dihitung otomatis di model, jadi tidak bisa terlupa dari jalur manapun termasuk
seeder dan impor.

Pengingat dikirim ke akun pemilik sertifikat lewat perintah terjadwal
`sertifikat:reminder-kadaluarsa`, jalan harian pukul 07:00. Ia hanya bekerja
pada sertifikat yang sudah lewat tanggal berakhirnya dan belum pernah
diingatkan, sehingga hari yang terlewat karena server mati tetap tersusul dan
tidak ada kiriman ganda. Sisa masa berlaku juga tampil di halaman Sertifikat
PPIU milik travel, dengan penanda kuning saat tersisa 30 hari atau kurang.

> **Satu aksi bisa punya lebih dari satu rute.** `KanwilController@updateStatus`
> dipanggil dari `travel.update-status` dan `update.status`. Menutup salah satu
> saja tidak menutup apa apa. Pasang penjaganya di controller, bukan hanya di
> middleware rute.

Form edit cabang hanya menyimpan field yang tervalidasi. Ia tidak bisa dipakai
sebagai jalan pintas mengubah status pendaftaran atau kolom verifikasi.

---

## 5. Isolasi data pusat dan cabang

Setelah disetujui, cabang beroperasi penuh seperti pusat: mengelola jamaah,
mengatur paket, dan mengajukan BA Pemberangkatan sendiri.

Datanya **terisolasi penuh**. Pusat tidak bisa melihat data cabangnya, cabang
tidak bisa melihat data pusat, dan antar cabang tidak bisa saling melihat.
Koordinasi antar mereka urusan di luar sistem, misalnya saling mengirim hasil
ekspor Excel.

Kepemilikan ditandai dua kolom pada `jamaah`, `jamaah_haji_khusus`, dan
`travel_packages`:

| Kolom | Arti |
|-------|------|
| `travel_id` | Travel pemegang izin PPIU. Menentukan jenis jamaah yang boleh dikelola |
| `cabang_id` | Pemilik barisnya. NULL berarti milik kantor pusat |

Satu tempat yang mendefinisikan aturan ini: `App\Support\OperatorScope`. Setiap
pembacaan dan penulisan data operasional milik akun travel harus lewat sana,
jangan menyaring `travel_id` secara langsung.

BA juga membawa `cabang_id`, karena BA cabang memakai nama PPIU pusat sehingga
`ppiuname` tidak bisa dipakai membedakan pemiliknya.

Pengawasan berbeda: sasarannya pemegang izin, jadi datanya milik pusat dan PIC
cabang tidak diberi akses ke sana.

> **Hati hati dengan filter opsional.** Penyaring `when($travelId, ...)` akan
> lenyap begitu nilainya `null`, dan yang tersisa adalah query tanpa batas sama
> sekali. Dua kebocoran terparah di modul ini persis berasal dari pola itu:
> akun tanpa `travel_id` bisa membaca data seluruh travel di NTB. Kalau kuncinya
> mungkin kosong, tolak permintaannya, jangan biarkan penyaringnya hilang.

BA Pemberangkatan dari cabang memakai nama PPIU pusat, karena izinnya memang
milik pusat, tetapi tercatat di **kabupaten cabang**. Itulah yang menentukan
Kabko mana yang meninjau pengajuannya.

---

## 6. Wilayah NTB

Daftar kabupaten/kota berasal dari satu sumber, `App\Support\NtbKabupatenMap`.
Isinya sepuluh wilayah: Lombok Barat, Lombok Tengah, Lombok Timur, Lombok Utara,
Sumbawa, Sumbawa Barat, Dompu, Bima, Kota Mataram, dan Kota Bima.

Menambah atau mengubah wilayah cukup di `centroids()`. Semua yang lain ikut
menyesuaikan: dropdown pendaftaran, aturan validasi, penyaringan wilayah petugas,
dan titik peta.

---

## 7. Menyiapkan data uji di lokal

```bash
php artisan migrate:fresh --seed --force
php artisan db:seed --class=DevTravelSeeder --force
php artisan storage:link
```

Hasilnya:

- PT. Mataram Travel, status disetujui, punya berkas SK sehingga tombol
  pratinjau "SK Pusat" ada isinya
- satu cabang di Kota Mataram berstatus disetujui, mewakili data lama
- satu cabang di Lombok Utara berstatus menunggu verifikasi, lengkap dengan
  empat berkas contoh, siap dipakai mencoba alur peninjauan

Akun untuk mencoba:

| Peran | Email | Password |
|-------|-------|----------|
| Kanwil | `admin@phu.com` | `admin123` |
| Kabupaten Kota Mataram | `kota.mataram@phu.com` | `password123` |

Cabang uji ada di Lombok Utara, jadi untuk mencobanya sebagai Kabko perlu akun
kabupaten Lombok Utara. Buat lewat menu Kelola Pengguna, atau ubah wilayah
cabang uji lewat form edit.

`DevTravelSeeder` khusus development. Jangan dijalankan di production.

---

## 8. Troubleshooting

### Saat instalasi atau deploy

**`Table 'phu.bap_airlines' doesn't exist` waktu `migrate:fresh --seed`**
Migrasi `2026_07_02_100018_run_all_seeders` dulu memanggil `DatabaseSeeder` dari
dalam migrasi, sementara seeder itu terus bertambah isinya dan menyentuh tabel
yang baru dibuat migrasi jauh setelahnya. Migrasi tersebut sekarang dikosongkan.
Pastikan memakai kode terbaru, lalu jalankan seeding sebagai langkah terpisah:
`php artisan migrate --force` diikuti `php artisan db:seed --force`.

**`Unknown column 'registration_status'` saat membuka PPIU Cabang**
Migrasi alur cabang belum jalan. Jalankan `php artisan migrate --force` lalu
`php artisan optimize:clear`.

**`Unknown column 'catatan_rekomendasi'`**
Sama, migrasi `2026_08_13_120000` belum jalan.

**`Unknown column 'cabang_id'` di layar jamaah atau paket**
Migrasi isolasi `2026_08_13_160000` belum jalan.

**Migrasi indeks unik nomor surat BA berhenti dengan pesan nomor kembar**
Migrasi `2026_08_13_140000` sengaja berhenti dan mencetak daftar nomor yang
kembar. Itu data lama yang terlanjur rusak oleh bug penerbitan nomor. Betulkan
nomornya di database dulu, baru jalankan migrasi lagi.

### Saat mendaftar

**Travel di Lombok Utara ditolak validasi kabupaten**
Versi lama `NtbKabupatenMap` hanya memuat sembilan wilayah. Pastikan kode
terbaru, lalu `php artisan optimize:clear`.

**"Travel pusat tidak ditemukan atau izinnya belum disetujui"**
Dropdown pusat hanya memuat travel berstatus `approved`. Kalau pusatnya masih
menunggu verifikasi, selesaikan dulu pendaftaran pusatnya.

**Formulir cabang tidak muncul**
Cek dropdown "Daftar sebagai" di bagian paling atas halaman. Formulir menyesuaikan
pilihan itu, dan berpindah pilihan akan pindah halaman. Kalau sudah ada isian
yang diketik, akan muncul konfirmasi lebih dulu.

**Upload ditolak walau berkasnya kecil**
Batasnya 1,5 MB per berkas, dan hanya PDF, JPG, atau PNG. Cek juga
`upload_max_filesize` serta `post_max_size` di PHP server.

### Saat verifikasi

**Cabang tidak muncul di daftar petugas kabupaten**
Penyaringannya mencocokkan kolom `kabupaten` dengan daftar kanonik NTB. Data
hasil impor lama bisa berisi teks bebas yang tidak cocok. Buka form edit cabang
dan pilih ulang kabupatennya dari dropdown.

**Tombol verifikasi tidak ada di baris cabang**
Tombol itu hanya tampil untuk status `pending` dan `menunggu_kanwil`. Cabang yang
sudah disetujui atau ditolak tidak menampilkannya.

**Tombol "SK Pusat" tidak muncul di modal verifikasi**
Travel pusatnya tidak punya berkas SK. Ini normal untuk travel lama yang datanya
diinput petugas, karena hanya pendaftar mandiri yang mengunggah SK. Lengkapi
lewat form edit travel pusat kalau diperlukan.

**Tombol pratinjau tidak menampilkan apa apa**
Jalankan `php artisan storage:link`, lalu pastikan berkasnya benar benar ada di
`storage/app/public`.

**Halaman di belakang ikut bergeser setelah pratinjau ditutup**
Versi lama `public/js/pdf-preview.js` tidak mengembalikan kunci scroll saat
pratinjau ditutup di atas modal lain. Pastikan kode terbaru, jalankan
`php artisan view:clear`, lalu hard refresh browser.

**Tombol Tolak tidak berfungsi atau formnya kosong**
Gejala versi lama, ketika modal ditaruh langsung di dalam `<tbody>` sehingga
markupnya tidak sah dan parser browser membongkar isinya. Sudah diperbaiki
dengan menempatkan modal di dalam `<td>`. Pastikan kode terbaru.

### Setelah disetujui

**PIC diminta "ganti password default" padahal passwordnya buat sendiri**
Perilaku versi lama yang menandai akun pendaftar mandiri sebagai memakai password
default. Sudah diperbaiki. Akun lama bisa dibereskan dengan
`UPDATE users SET is_password_changed = 1 WHERE id = ...`.

**PIC tidak bisa login walau statusnya disetujui**
Cek status pada entitas yang benar. PIC cabang terikat lewat `users.cabang_id`,
PIC pusat lewat `users.travel_id`. Pesan error di halaman login menyebut status
terkini.

**Cabang yang belum disetujui muncul di halaman publik**
Seharusnya tidak. Kalau terjadi, kode yang jalan masih versi lama, karena
penyaring `approved()` belum terpasang di direktori publik, halaman depan,
dropdown sertifikat, dan hitungan dashboard.

**Pusat melihat data jamaah cabangnya, atau antar cabang saling melihat**
Kode versi lama menandai pemilik hanya dengan `travel_id`, sehingga satu
`travel_id` dipakai bersama pusat dan seluruh cabangnya. Pastikan kode terbaru
dan migrasi `2026_08_13_160000` sudah jalan. Data lama otomatis bernilai
`cabang_id` NULL, artinya milik pusat.

**Pengingat kedaluwarsa sertifikat tidak pernah sampai**
Perintahnya bergantung pada cron scheduler. Pastikan `php artisan schedule:run`
terpasang di crontab, lihat bagian 8 DEPLOY.md. Untuk menguji tanpa menunggu
1 Januari: `php artisan sertifikat:reminder-kadaluarsa --pada=2027-01-01`.

**Ingin mengirim ulang pengingat yang sudah terkirim**
Kosongkan `reminder_kadaluarsa_at` pada baris sertifikat tersebut, lalu jalankan
perintahnya lagi.

**Migrasi indeks unik nomor sertifikat berhenti dengan pesan nomor kembar**
Sama seperti nomor BA. Nomor surat sertifikat dulu dikirim dari formulir tanpa
pemeriksaan keunikan, dan nomor saran dihitung dari jumlah baris sehingga
terpakai ulang setiap kali ada sertifikat dihapus. Betulkan nomor kembarnya di
database dulu, baru jalankan migrasi lagi.

**Akun travel dapat 404 saat membuka Terbitkan Sertifikat**
Memang begitu. Penerbitan sertifikat pekerjaan petugas. Sebelum ini akun travel
bisa menerbitkan sertifikat resmi untuk dirinya sendiri berikut nomor suratnya.

**Akun travel dapat 404 saat membuka Edit Travel atau mengubah jenis izin**
Memang begitu. Sebelum ini akun travel bisa menaikkan izinnya sendiri dari PPIU
ke PIHK, memperpanjang masa berlaku izinnya, mengganti nomor izin dan nilai
akreditasi, bahkan memindahkan wilayahnya sehingga lepas dari Kabko pengawas.

**PIC cabang dapat 403 di menu Pengawasan**
Memang begitu. Pengawasan menyasar pemegang izin dan datanya milik pusat.
Sebelum ini akun cabang justru bisa membaca data pengawasan seluruh travel di
NTB, karena penyaringnya lenyap saat `travel_id` kosong.

**PIC cabang tidak bisa mengedit jamaahnya sendiri**
Gejala versi lama. Penjaga aksesnya membandingkan `travel_id` langsung, yang
selalu gagal untuk PIC cabang. Pastikan kode terbaru.

**Jamaah lama milik cabang ikut terbaca sebagai milik pusat**
Konsekuensi dari poin sebelumnya: sebelum ada `cabang_id`, tidak ada informasi
yang membedakannya. Perlu pemetaan manual kalau di produksi sudah ada akun
cabang hasil impor Excel yang sempat menginput jamaah.

---

## 9. Berkas terkait

| Bagian | Lokasi |
|--------|--------|
| Formulir registrasi | `resources/views/travel-registration/` |
| Wizard bersama | `resources/views/travel-registration/partials/wizard-*.blade.php` |
| Layar verifikasi cabang | `resources/views/kanwil/partials/cabang-travel-table-body.blade.php` |
| Controller pendaftaran | `app/Http/Controllers/TravelRegistrationController.php` |
| Controller verifikasi | `app/Http/Controllers/KanwilController.php` |
| Model cabang | `app/Models/CabangTravel.php` |
| Status pendaftaran | `app/Enums/TravelRegistrationStatus.php` |
| Daftar wilayah NTB | `app/Support/NtbKabupatenMap.php` |
| Pratinjau dokumen | `public/js/pdf-preview.js` |
| Dialog konfirmasi | `public/js/confirm-dialogs.js` |

# Dokumentasi Fitur Sistem PANTAU

**PANTAU** adalah sistem manajemen berbasis web untuk Kanwil Kementerian Haji dan Umroh Provinsi Nusa Tenggara Barat (NTB). Sistem ini mengelola data travel haji/umrah, jamaah, **BA Pemberangkatan** (persetujuan keberangkatan), **BA Pemeriksaan** (pengawasan PPIU), sertifikat PPIU, pengaduan, dan pengunduran.

**Stack teknologi:** Laravel 10, PHP 8.1+, MySQL, Argon Dashboard UI

---

## Daftar Isi

1. [Peran Pengguna](#1-peran-pengguna)
2. [Autentikasi & Akun](#2-autentikasi--akun)
3. [Dashboard](#3-dashboard)
4. [Manajemen Travel (PPIU/PIHK)](#4-manajemen-travel-ppiupihk)
5. [Manajemen Jamaah](#5-manajemen-jamaah)
6. [BA Pemberangkatan](#6-ba-pemberangkatan)
7. [Sertifikat PPIU](#7-sertifikat-ppiu)
8. [Pengaduan](#8-pengaduan)
9. [Pengunduran](#9-pengunduran)
10. [Manajemen Pengguna](#10-manajemen-pengguna)
11. [Impersonasi Admin](#11-impersonasi-admin)
12. [Halaman Publik](#12-halaman-publik)
13. [API & Utilitas](#13-api--utilitas)
14. [Import/Export Excel](#14-importexport-excel)
15. [Struktur Database](#15-struktur-database)
16. [Alur Kerja Utama](#16-alur-kerja-utama)

---

## 1. Peran Pengguna

Sistem memiliki **3 peran** dengan hak akses berbeda:

| Peran | Kode | Deskripsi |
|-------|------|-----------|
| **Admin Kanwil** | `admin` | Akses penuh ke seluruh sistem |
| **Admin Kabupaten** | `kabupaten` | Terbatas pada kabupaten/kota yang ditugaskan |
| **User Travel** | `user` | Akun perusahaan travel (PPIU pusat atau cabang) |

### Jenis Perusahaan Travel

| Tipe | Kode | Layanan |
|------|------|---------|
| **PPIU** | Penyelenggara Perjalanan Ibadah Umrah | Umrah saja |
| **PIHK** | Penyelenggara Ibadah Haji Khusus | Haji, Umrah, dan Haji Khusus |

Menu sidebar dan fitur yang tampil disesuaikan otomatis berdasarkan peran dan tipe travel (`TravelCapabilityService`).

---

## 2. Autentikasi & Akun

### Fitur Login
- Login menggunakan **email** atau **nomor HP**
- Halaman landing publik (`/`) menampilkan jadwal keberangkatan dan form pengaduan
- Middleware `password.changed` memaksa user travel/kabupaten baru mengganti password default

### Fitur Profil
- Lihat dan ubah profil pengguna (`/profile`)
- Ubah password (`/change-password`) — wajib untuk akun baru

### Password Default
- Akun travel baru: `password123`
- Admin default (setelah migrate): `admin@phu.com` / `admin123`

### Route Utama
| Fitur | Route |
|-------|-------|
| Landing page | `GET /` |
| Login | `GET/POST /login` |
| Logout | `POST /logout` |
| Ganti password | `GET/POST /change-password` |
| Profil | `GET/POST /profile` |

---

## 3. Dashboard

Dashboard menampilkan statistik berbeda per peran:

### Admin Kanwil
- Total jamaah (haji & umrah)
- Total BAP per status
- Jumlah travel pusat & cabang
- Jumlah pengguna
- Grafik pendapatan bulanan
- Statistik keseluruhan wilayah NTB

### Admin Kabupaten
- Jamaah haji/umrah di kabupaten terkait
- BAP per status (diajukan, diproses, diterima)
- Data terbatas pada kabupaten yang ditugaskan

### User Travel
- Jumlah BAP milik travel sendiri per status
- Pendapatan dari BAP yang diterima
- Ringkasan jamaah yang terdaftar

**Route:** `GET /dashboard`

---

## 4. Manajemen Travel (PPIU/PIHK)

### 4.1 Data PPIU Pusat (Admin)

Mengelola perusahaan travel pusat di seluruh NTB.

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar travel pusat | `GET /travel` | Semua PPIU/PIHK pusat |
| Tambah travel | `GET/POST /travel/form` | Form pendaftaran baru |
| Edit travel | `GET /travel/{id}/edit` | Ubah data perusahaan |
| Update status | `POST /travel/{id}/status` | Ubah status PPIU/PIHK |
| Export Excel | `GET /travel/export` | Unduh data travel pusat |

**Data yang dicatat:**
- Nama penyelenggara, pusat, pimpinan
- Alamat kantor (lama & baru)
- Telepon, kabupaten/kota
- Status (PPIU/PIHK), kemampuan layanan
- Data akreditasi (nilai, tanggal, lembaga)
- Nomor & masa berlaku lisensi

### 4.2 Data PPIU Cabang (Admin & Kabupaten)

Mengelola kantor cabang travel per kabupaten.

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar cabang | `GET /cabang-travel` | Cabang di wilayah terkait |
| Tambah cabang | `GET/POST /cabang-travel/form` | Form cabang baru |
| Edit cabang | `GET /cabang-travel/{id}/edit` | Ubah data cabang |
| Hapus cabang | `DELETE /cabang-travel/{id}` | Hapus cabang |
| Import Excel | `POST /import-cabang-travel` | Import massal |
| Template import | `GET /download-template-cabang-travel` | Unduh template |
| Export Excel | `GET /cabang-travel/export` | Unduh data cabang |

**Akses:** Admin (semua kabupaten) dan Kabupaten (hanya kabupaten sendiri)

### 4.3 Direktori Travel (Publik)

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar publik | `GET /travel-public` | Tanpa login, travel pusat + cabang |
| Daftar terautentikasi | `GET /list-travel` | Perlu login |

---

## 5. Manajemen Jamaah

### 5.1 Jamaah Umrah & Haji Reguler

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar jamaah haji | `GET /jamaah/haji` | Admin: dikelompokkan per travel |
| Tambah jamaah haji | `GET/POST /jamaah/haji/create` | Hanya PIHK |
| Daftar jamaah umrah | `GET /jamaah/umrah` | PPIU & PIHK |
| Tambah jamaah umrah | `GET/POST /jamaah/umrah/create` | Semua travel umrah |
| Detail jamaah | `GET /jamaah/{id}` | Lihat data lengkap |
| Edit jamaah | `GET/PUT /jamaah/edit/{id}` | Ubah data |
| Hapus jamaah | `DELETE /jamaah/{id}` | Hapus data |
| Import Excel | `POST /jamaah/import` | Import massal |
| Export semua | `GET /jamaah/export` | Export Excel |
| Export umrah | `GET /jamaah/umrah/export` | Filter umrah |
| Export haji | `GET /jamaah/haji/export` | Filter haji |
| Template import | `GET /jamaah/template` | Unduh template |

**Data jamaah:** NIK, nama, alamat, nomor HP, jenis (haji/umrah), terkait travel & user

### 5.2 Jamaah Haji Khusus (PIHK)

Modul lengkap untuk pendaftaran haji khusus dengan dokumen pendukung.

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar | `GET /jamaah/haji-khusus` | Semua pendaftar haji khusus |
| Tambah | `GET/POST /jamaah/haji-khusus/create` | Form pendaftaran lengkap |
| Detail | `GET /jamaah/haji-khusus/{id}` | Lihat data & dokumen |
| Edit | `GET/PUT /jamaah/haji-khusus/{id}/edit` | Ubah data |
| Hapus | `DELETE /jamaah/haji-khusus/{id}` | Hapus pendaftar |
| Update status | `PUT /jamaah/haji-khusus/{id}/status` | pending → approved/rejected/completed |
| Verifikasi bukti setor | `POST /jamaah/haji-khusus/{id}/verify-bukti-setor` | Verifikasi bukti bank |
| Assign nomor porsi | `POST /jamaah/haji-khusus/{id}/assign-porsi` | Tetapkan nomor porsi haji |
| Export Excel | `GET /jamaah/haji-khusus/export` | Unduh data |
| Export PDF | `GET /jamaah/haji-khusus/export-pdf` | Laporan PDF |

**Data lengkap:** identitas, alamat, pekerjaan, pendidikan, status pernikahan, riwayat haji, golongan darah, alergi, paspor, dokumen (KTP, KK, paspor, foto, surat keterangan, bukti setor bank)

**Status pendaftaran:** `pending` → `approved` / `rejected` → `completed`

**Akses:** Hanya travel PIHK (`canHandleHajiKhusus()`)

---

## 6. BA Pemberangkatan

**BA Pemberangkatan** (Berita Acara Pelaporan Keberangkatan) adalah dokumen deklarasi keberangkatan jamaah yang diajukan travel ke Kanwil. Modul ini **berbeda** dari **BA Pemeriksaan** di modul Pengawasan Digital (hasil inspeksi PPIU).

> **Siapa yang memproses?** Persetujuan BA Pemberangkatan dilakukan oleh **Admin** atau **Kabupaten** — bukan Pimpinan.

### Alur Status BAP

```
pending → diajukan → diproses → diterima
```

| Status | Keterangan |
|--------|------------|
| `pending` | Draft, belum diajukan |
| `diajukan` | Travel sudah mengajukan ke Kanwil |
| `diproses` | Sedang ditinjau admin/kabupaten |
| `diterima` | Disetujui, nomor surat otomatis digenerate |

### Fitur BAP

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar BAP | `GET /bap` | Terfilter per peran |
| Form BAP baru | `GET /form-bap` | Butuh jamaah terdaftar |
| Simpan BAP | `POST /bap` | PPIU: hitung umrah, PIHK: hitung haji |
| Detail BAP | `GET /bap/detail/{id}` | Lihat data lengkap |
| Cetak BAP | `GET /cetak-bap/{id}` | Cetak dengan QR e-sign (jika diterima) |
| Upload PDF | `POST /bap/upload/{id}` | Lampirkan PDF bertanda tangan |
| Ajukan BAP | `POST /bap/ajukan/{id}` | pending → diajukan |
| Update status | `POST /bap/update-status/{id}` | Admin/kabupaten ubah status |
| Kalender keberangkatan | `GET /keberangkatan` | Jadwal keberangkatan BAP diterima |
| Event kalender (API) | `GET /keberangkatan/events` | JSON untuk kalender |
| Verifikasi QR | `POST /bap/verify-qr` | Verifikasi e-signature |
| Verifikasi e-sign | `GET /verify-e-sign` | Halaman verifikasi (login) |
| Verifikasi publik | `GET /public/verify-e-sign` | Verifikasi tanpa login |

**Data BA Pemberangkatan:** nama penanggung jawab, jabatan, nama PPIU, alamat/telepon, kab/kota, jumlah jamaah, hari, harga, tanggal keberangkatan, maskapai, tanggal kembali, maskapai pulang

**Fitur khusus:**
- Auto-generate `nomor_surat` saat status `diterima`
- QR code e-signature pada BAP yang diterima
- Command artisan: `php artisan bap:update-days` — hitung ulang field `days`

### BA Pemeriksaan (modul terpisah)

**BA Pemeriksaan** dicatat di menu **Pengawasan Digital → BA Pemeriksaan** (`/v2/pengawasan`). Alurnya: Pengawas/Admin menjadwalkan inspeksi → mencatat temuan → travel mengunggah tindak lanjut → Pengawas memverifikasi. Modul ini **tidak** menggantikan atau memproses BA Pemberangkatan.

---

## 7. Sertifikat PPIU

Modul penerbitan sertifikat resmi untuk travel PPIU.

### Fitur Sertifikat

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar sertifikat | `GET /sertifikat` | Admin: semua, Kabupaten: terbatas |
| Buat sertifikat | `GET/POST /sertifikat` | Terbitkan untuk pusat/cabang |
| Generate PDF | `GET /sertifikat/{id}/generate` | Buat PDF dengan QR code |
| Preview | `GET /sertifikat/{id}/view` | Lihat sertifikat |
| Download | `GET /sertifikat/{id}/download` | Unduh PDF |
| Hapus | `DELETE /sertifikat/{id}` | Hapus sertifikat |
| Data travel (AJAX) | `GET /sertifikat/travel-data/{id}` | Auto-fill dari travel |
| Data cabang (AJAX) | `GET /sertifikat/cabang-data/{id}` | Auto-fill dari cabang |
| Nomor berikutnya | `GET /sertifikat/get-next-nomor` | Auto-increment nomor surat |
| Pengaturan | `GET/POST /sertifikat/settings` | Nama & NIP penandatangan |
| Sertifikat saya | `GET /travel/certificates` | Travel lihat sertifikat sendiri |
| Verifikasi publik | `GET /verifikasi-sertifikat/{uuid}` | Scan QR tanpa login |

**Format nomor surat:** `B-{n}/Kw.18.01/HJ.00/2/{bulan}/{tahun}`

**Data sertifikat:** UUID unik, nama PPIU, nama kepala, alamat, tanggal terbit & tanda tangan, nomor surat & dokumen, jenis lokasi (pusat/cabang), status (active/revoked)

---

## 8. Pengaduan

Sistem pengaduan masyarakat terhadap travel haji/umrah.

### Fitur Admin

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar pengaduan | `GET /pengaduan` | Semua pengaduan |
| Buat pengaduan | `GET/POST /pengaduan` | Admin input manual |
| Detail | `GET /pengaduan/{id}` | Lihat & tanggapi |
| Update status | `POST /pengaduan/{id}/status` | Ubah status |
| Download PDF | `GET /pengaduan/{id}/download-pdf` | Generate PDF tanggapan |

### Fitur Publik

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Kirim pengaduan | `POST /pengaduan-public` | Dari landing page |
| Lihat status | `GET /pengaduan-public/{id}` | Cek status pengaduan |
| Download PDF | `GET /public/pengaduan/{id}/download-pdf` | Unduh PDF |
| API selesai | `GET /api/pengaduan-completed` | JSON pengaduan selesai |

**Status:** `pending` → `in_progress` → `completed` / `rejected`

**Data:** nama pelapor, email, telepon, kabupaten, judul, deskripsi, kategori, nama travel, file lampiran, tanggapan admin

---

## 9. Pengunduran

Pengajuan pengunduran diri jamaah dari keberangkatan.

| Fitur | Route | Keterangan |
|-------|-------|------------|
| Daftar pengunduran | `GET /pengunduran` | Admin: semua, Kabupaten: terbatas |
| Buat pengunduran | `GET /pengunduran/create` | Hanya user travel |
| Simpan | `POST /pengunduran` | Upload berkas pengunduran |

**Data:** nama jamaah, no KTP, no paspor, jenis pengunduran, alasan, dokumen pendukung

**Status:** `pending` → `approved` / `rejected`

---

## 10. Manajemen Pengguna

### User Kabupaten (Admin)

| Fitur | Route |
|-------|-------|
| Daftar | `GET /kabupaten` |
| Tambah | `GET/POST /kabupaten/create` |

### User Travel PPIU (Admin)

| Fitur | Route |
|-------|-------|
| Daftar | `GET /travel-users` |
| Tambah | `GET/POST /travel-users/create` |
| Import Excel (pusat) | `GET/POST /travel-users/import` |
| Template import | `GET /travel-users/template` |
| Import Excel (cabang) | `GET/POST /cabang-users/import` |
| Template cabang | `GET /cabang-users/template` |

### Manajemen Umum (Admin)

| Fitur | Route |
|-------|-------|
| Edit user | `GET/PUT /users/{id}/edit` |
| Hapus user | `DELETE /users/{id}` |

### Legacy Kanwil Routes

| Fitur | Route |
|-------|-------|
| Daftar akun travel | `GET /kanwil/travels` |
| Tambah akun travel | `GET/POST /kanwil/tambah-akun-travel` |
| Reset password | `PUT /kanwil/reset-password/{id}` |

---

## 11. Impersonasi Admin

Admin dapat masuk sebagai user travel atau kabupaten untuk troubleshooting.

| Fitur | Route |
|-------|-------|
| Pilih user | `GET /impersonate` |
| Mulai impersonasi | `GET /impersonate/{id}` |
| Keluar impersonasi | `GET /impersonate-leave` |

**Aturan:** Hanya admin yang bisa impersonate; target hanya `user` dan `kabupaten`.

---

## 12. Halaman Publik

Tanpa perlu login:

| Halaman | Route | Fungsi |
|---------|-------|--------|
| Landing page | `GET /` | Beranda, jadwal keberangkatan, form pengaduan |
| Direktori travel | `GET /travel-public` | Daftar travel terdaftar |
| Kirim pengaduan | `POST /pengaduan-public` | Form pengaduan |
| Status pengaduan | `GET /pengaduan-public/{id}` | Cek status |
| Verifikasi sertifikat | `GET /verifikasi-sertifikat/{uuid}` | Scan QR sertifikat |
| Verifikasi e-sign BAP | `GET /public/verify-e-sign` | Verifikasi tanda tangan elektronik |

---

## 13. API & Utilitas

### API Wilayah Indonesia

Proxy ke API emsifa (tanpa API key):

| Endpoint | Fungsi |
|----------|--------|
| `GET /api/provinces` | Daftar provinsi |
| `GET /api/cities?province_id=` | Kota/kabupaten per provinsi |
| `GET /api/districts?regency_id=` | Kecamatan per kota |

### Utilitas

| Fitur | Route | Fungsi |
|-------|-------|--------|
| Storage link | `GET /storage-link` | Buat symlink `public/storage` (untuk cPanel) |

---

## 14. Import/Export Excel

Sistem menggunakan **Maatwebsite Excel** untuk operasi bulk:

| Modul | Import | Export | Template |
|-------|--------|--------|----------|
| Jamaah | ✅ | ✅ (umrah, haji, semua) | ✅ |
| Travel pusat | — | ✅ | — |
| Travel cabang | ✅ | ✅ | ✅ |
| User travel (pusat) | ✅ | — | ✅ |
| User travel (cabang) | ✅ | — | ✅ |
| Jamaah haji khusus | — | ✅ (Excel & PDF) | — |
| Import generik | ✅ | — | — |

---

## 15. Struktur Database

| Tabel | Fungsi |
|-------|--------|
| `users` | Semua akun (admin, kabupaten, travel) |
| `travels` | Perusahaan travel pusat (PPIU/PIHK) |
| `travel_cabang` | Kantor cabang travel |
| `jamaah` | Data jamaah haji & umrah |
| `jamaah_haji_khusus` | Pendaftaran haji khusus |
| `bap` | BA Pemberangkatan (pelaporan keberangkatan jamaah) |
| `pengaduan` | Pengaduan masyarakat |
| `pengunduran` | Pengunduran jamaah |
| `sertifikat` | Sertifikat PPIU |
| `sertifikat_settings` | Konfigurasi penandatangan sertifikat |

### Relasi Utama

```
users ──→ travels (travel_id)
users ──→ travel_cabang (cabang_id)
jamaah ──→ travels, users
jamaah_haji_khusus ──→ travels
bap ──→ users
pengaduan ──→ users (processed_by)
pengunduran ──→ travels
sertifikat ──→ travels, travel_cabang
```

---

## 16. Alur Kerja Utama

### Alur Pendaftaran Travel Baru

```
Admin buat data PPIU pusat
  → Admin buat akun user travel
  → User login (password default)
  → User wajib ganti password
  → User mulai input jamaah & BAP
```

### Alur BA Pemberangkatan

```
Travel input jamaah
  → Travel buat BAP (draft/pending)
  → Travel ajukan BAP (diajukan)
  → Admin/Kabupaten proses (diproses)
  → Admin/Kabupaten setujui (diterima + nomor surat)
  → BAP bisa dicetak dengan QR e-sign
  → Muncul di kalender keberangkatan
```

### Alur Sertifikat PPIU

```
Admin/Kabupaten buat sertifikat
  → Auto-fill data dari travel/cabang
  → Generate PDF + QR code
  → Travel bisa lihat sertifikat sendiri
  → Publik verifikasi via scan QR
```

### Alur Haji Khusus

```
PIHK input pendaftar haji khusus + dokumen
  → Admin verifikasi bukti setor bank
  → Admin assign nomor porsi
  → Update status: approved → completed
```

---

## Hak Akses per Peran (Ringkasan)

| Fitur | Admin | Kabupaten | User Travel |
|-------|:-----:|:---------:|:-----------:|
| Dashboard | ✅ | ✅ | ✅ |
| Travel pusat | ✅ CRUD | ❌ | ❌ |
| Travel cabang | ✅ CRUD | ✅ CRUD | ❌ |
| Jamaah umrah | ✅ | ❌ | ✅ (PPIU/PIHK) |
| Jamaah haji khusus | ✅ | ❌ | ✅ (PIHK) |
| BA Pemberangkatan | ✅ | ✅ | ✅ |
| Keberangkatan | ✅ | ✅ | ✅ |
| Pengaduan | ✅ | ❌ | ❌ |
| Pengunduran | ✅ | ✅ lihat | ✅ ajukan |
| Sertifikat | ✅ buat | ✅ buat | ✅ lihat |
| User management | ✅ | ❌ | ❌ |
| Impersonasi | ✅ | ❌ | ❌ |

---

## Setup & Deployment

```bash
# 1. Salin environment
cp .env.example .env

# 2. Generate key
php artisan key:generate

# 3. Migrasi database (termasuk seeder data awal NTB)
php artisan migrate

# 4. Symlink storage untuk upload file
php artisan storage:link

# 5. Jalankan server
php artisan serve
```

**Login default:** `admin@phu.com` / `admin123`

**Catatan production:** Set `APP_ENV=production` dan `APP_DEBUG=false`. Sistem otomatis memaksa HTTPS saat production.

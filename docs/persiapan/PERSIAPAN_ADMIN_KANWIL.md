# Data yang Harus Disiapkan: Admin Kanwil

**Untuk:** Tim Admin/Super Admin Kanwil Kementerian Haji dan Umroh NTB  
**Tujuan:** Agar master data dan akun siap sebelum travel, admin kabupaten, dan pengawas mulai testing live.

> Dokumen ini hanya berisi **daftar data yang perlu kalian siapkan**. Cara input ke sistem akan dijelaskan saat presentasi.

---

## 1. Data Akun Admin Kanwil

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama lengkap admin | ✅ |
| 2 | Email aktif | ✅ |
| 3 | Nomor HP | ✅ |

---

## 2. Master Data Travel (Seluruh NTB)

Kumpulkan daftar **semua travel PPIU dan PIHK** di NTB. Per travel:

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama penyelenggara (sesuai izin) | ✅ |
| 2 | Status (PPIU / PIHK) | ✅ |
| 3 | Kabupaten/Kota | ✅ |
| 4 | Kota pusat | ✅ |
| 5 | Nama pimpinan | ✅ |
| 6 | Alamat kantor lengkap | ✅ |
| 7 | Nomor telepon | ✅ |
| 8 | Nilai akreditasi | ✅ |
| 9 | Tanggal akreditasi | ✅ |
| 10 | Lembaga akreditasi | ✅ |

**Target minimal untuk testing:** 1 travel per kabupaten/kota (9 kab/kota NTB).

---

## 3. Daftar Akun yang Perlu Dibuat

Siapkan spreadsheet berisi semua akun yang akan diinput ke sistem:

### 3.1 Akun Travel (1 per travel)

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama PIC travel | ✅ |
| 2 | Email login | ✅ |
| 3 | Nomor HP | ✅ |
| 4 | Travel terkait | ✅ |

### 3.2 Akun Admin Kabupaten (1 per kab/kota)

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama admin | ✅ |
| 2 | Email login | ✅ |
| 3 | Nomor HP | ✅ |
| 4 | Kabupaten/Kota | ✅ |

**9 kabupaten/kota NTB:**
1. Lombok Barat
2. Lombok Tengah
3. Lombok Timur
4. Sumbawa
5. Sumbawa Barat
6. Dompu
7. Bima
8. Kota Mataram
9. Kota Bima

### 3.3 Akun Pengawas

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama pengawas | ✅ |
| 2 | Email login | ✅ |
| 3 | Nomor HP | ✅ |
| 4 | Wilayah tugas (1 kab / beberapa kab / seluruh NTB) | ✅ |
| 5 | Daftar kabupaten/kota (jika wilayah custom) | Jika custom |

### 3.4 Akun Pimpinan Kanwil (opsional, untuk demo dashboard)

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama pimpinan | ✅ |
| 2 | Email login | ✅ |
| 3 | Nomor HP | ✅ |

---

## 4. Master Checklist Pengawasan

Siapkan daftar item pemeriksaan yang akan dipakai pengawas saat inspeksi travel. Default sistem sudah punya 3 kategori:

| Kategori | Contoh Item |
|----------|-------------|
| **Legalitas** | Izin operasional aktif, akreditasi masih berlaku |
| **Operasional** | Kantor aktif beroperasi, jumlah jamaah aktif |
| **Keuangan** | Laporan keuangan tersedia, rekening escrow sesuai |

> Review dan sesuaikan checklist dengan ketentuan Kanwil sebelum go-live.

---

## 5. Setting Sertifikat PPIU

Data pejabat penandatangan (diinput **sekali**):

| No | Data | Wajib |
|----|------|-------|
| 1 | Nama lengkap pejabat penandatangan | ✅ |
| 2 | NIP pejabat penandatangan | ✅ |

---

## 6. Koordinasi dengan Pihak Lain

Sebelum testing live, pastikan sudah koordinasi:

| Pihak | Yang perlu dipastikan |
|-------|----------------------|
| **Travel** | Data perusahaan, jamaah, & BA Pemberangkatan sudah siap |
| **Admin Kabupaten** | Daftar travel di wilayah & data sertifikat sudah siap |
| **Pengawas** | Wilayah tugas sudah jelas & travel di wilayah sudah terdaftar |

---

## Checklist Ringkas: Admin Kanwil

Centang sebelum mulai testing live:

- [ ] Daftar semua travel PPIU/PIHK NTB sudah lengkap
- [ ] Spreadsheet akun (travel, kabupaten, pengawas) sudah siap
- [ ] Master checklist pengawasan sudah direview
- [ ] Data pejabat penandatangan sertifikat sudah ditentukan
- [ ] Server/database sudah siap (migrate & seed jika perlu)
- [ ] Sudah koordinasi jadwal testing dengan travel, kabupaten, & pengawas

---

*PANTAU · Kanwil Kementerian Haji dan Umroh NTB*

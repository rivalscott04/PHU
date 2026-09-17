# Panduan Mengisi Data: Travel (PPIU / PIHK)

Panduan ini untuk **user travel** yang sudah punya akun di sistem PANTAU.  
Bahasanya sengaja dibuat sederhana. Nama menu mengikuti yang tampil di sidebar kiri setelah Anda login.

> Screenshot diambil otomatis dengan Playwright (**fullscreen 1920×1080**) dari satu data contoh **PT. Mataram Travel**.  
> Cara menghasilkan ulang gambar: lihat [PANDUAN_SCREENSHOTS.md](./PANDUAN_SCREENSHOTS.md).

---

## 1. Apa tugas travel di sistem ini?

Secara singkat, travel mengerjakan empat hal utama:

1. Mendaftar (jika belum punya akun resmi)
2. Mengisi data jamaah
3. Mengajukan **BA Pemberangkatan** (surat pernyataan rencana keberangkatan)
4. Menanggapi tugas dari Kanwil (misalnya tindak lanjut temuan pemeriksaan)

Setelah BA disetujui Kabupaten/Kota atau Kanwil, jadwal keberangkatan muncul di kalender.

---

## 2. Menu yang Anda lihat setelah login

![Menu sidebar travel](assets/panduan/travel/02-sidebar-menu.png)

| Menu di sidebar | Fungsi singkat |
|-----------------|----------------|
| **Beranda** | Checklist langkah Anda, status BA, dan ringkasan |
| **Data Jamaah → Jamaah Umrah** | Isi / impor data jamaah umrah (PPIU dan PIHK) |
| **Data Jamaah → Jamaah Haji Khusus** | Hanya muncul jika travel berstatus **PIHK** |
| **Keberangkatan → BA Pemberangkatan** | Buat dan ajukan BA |
| **Keberangkatan → Paket Umrah Saya** | Simpan harga & durasi paket agar form BA terisi otomatis |
| **Keberangkatan → Jadwal Keberangkatan** | Lihat jadwal yang sudah disetujui |
| **Tugas dari Kanwil → Tindak Lanjut Temuan** | Unggah bukti perbaikan jika ada temuan pengawasan |
| **Tugas dari Kanwil → Profil Kepatuhan Saya** | Lihat ringkasan kepatuhan travel Anda |
| **Sertifikat → Sertifikat Saya** | Unduh sertifikat PPIU milik travel Anda |

**Catatan:**
- Menu **Jamaah Haji Khusus** hanya untuk **PIHK**.
- Menu **Jamaah Umrah** muncul jika travel melayani umrah.
- Jika registrasi belum disetujui, fitur operasional belum bisa dipakai.

---

## 3. Sebelum mulai: siapkan data ini

### A. Data untuk login

- Email atau nomor HP akun PIC
- Password (ganti password jika sistem meminta saat pertama masuk)

### B. Data jamaah umrah (minimal 1 orang sebelum mengajukan BA)

| Field di form | Contoh | Aturan |
|---------------|--------|--------|
| NIK | 5201010101010001 | Tepat 16 digit angka |
| Nama | Siti Aminah | Sesuai KTP |
| Alamat | Jl. Pejanggik No. 10, Mataram | Sesuai KTP |
| Nomor HP | 081234567890 | Diawali 08, 8 s.d. 16 digit |

### C. Untuk pengajuan BA Pemberangkatan

- Daftar jamaah yang akan berangkat (sudah masuk sistem)
- Jumlah hari perjalanan
- Harga paket per orang (bukan total rombongan)
- Tanggal berangkat dan pulang
- Maskapai berangkat dan pulang
- File PDF **surat pernyataan** yang sudah ditandatangani

### D. Opsional tapi sangat membantu

- Katalog paket di menu **Paket Umrah Saya** (agar harga, hari, dan maskapai terisi otomatis di form BA)

---

## 4. Langkah 0: Registrasi mandiri (jika belum punya akun)

Buka halaman publik **Registrasi Travel**, lalu pilih:

- **Kantor Pusat**, atau
- **Kantor Cabang**

### 4.1 Registrasi kantor pusat

1. Isi nama penyelenggara sesuai izin, jenis izin (PPIU / PIHK), nomor SK / NIB, tanggal SK, masa berlaku izin.
2. Isi data pimpinan, alamat, telepon, dan akreditasi.
3. Unggah **scan SK izin** (wajib). Sertifikat akreditasi boleh menyusul.
4. Isi data PIC (nama, email, nomor HP) untuk akun login.
5. Kirim pendaftaran.

**Status setelah kirim:**
- **Menunggu Verifikasi** → Kanwil sedang memeriksa
- **Disetujui** → Anda bisa login dan mulai isi jamaah
- **Ditolak** → baca catatan penolakan, lalu daftar ulang

### 4.2 Registrasi kantor cabang

1. Pilih travel pusat yang izinnya sudah **Disetujui**.
2. Pilih kabupaten/kota cabang (ini menentukan petugas Kabko yang meninjau).
3. Isi data kepala cabang, alamat, telepon, nomor/tanggal SK BA cabang.
4. Unggah dokumen wajib:
   - OSS cabang
   - Akta notaris / pembukaan cabang
   - KTP kepala cabang
   - SK domisili usaha kelurahan
5. Kirim pendaftaran.

**Alur status cabang:**
1. **Menunggu Verifikasi** → petugas Kabupaten/Kota meninjau
2. **Menunggu Kanwil** → rekomendasi Kabko sudah diunggah, menunggu keputusan Kanwil
3. **Disetujui** → cabang bisa login dan beroperasi
4. **Ditolak** → akun PIC dihapus, harus daftar ulang

**Format unggahan:** PDF / JPG / PNG, maksimal 1,5 MB per berkas.

---

## 5. Login dan baca Beranda

1. Buka halaman login.
2. Masuk dengan email atau nomor HP + password.
3. Di **Beranda**, ikuti checklist:

   - Akun / cabang sudah aktif
   - Tambah data jamaah
   - Atur paket umrah
   - Ajukan BA Pemberangkatan
   - Pantau jadwal keberangkatan

![Halaman login](assets/panduan/travel/01-login.png)

![Beranda travel](assets/panduan/travel/03-beranda.png)

Angka di kartu **BA Diajukan**, **BA Diproses**, dan **BA Diterima** membantu Anda tahu di tahap mana pengajuan Anda berada.

---

## 6. Mengisi data jamaah umrah

Menu: **Data Jamaah → Jamaah Umrah**

![Daftar jamaah umrah](assets/panduan/travel/04-jamaah-umrah-daftar.png)

### Cara 1: isi satu per satu

1. Klik **Tambah Jamaah Umrah** (atau tombol tambah di halaman daftar).
2. Isi NIK, Nama, Alamat, Nomor HP.
3. Klik **Simpan**.

![Form tambah jamaah umrah](assets/panduan/travel/05-jamaah-umrah-tambah.png)

Setelah disimpan, jamaah muncul di daftar:

![Jamaah setelah disimpan](assets/panduan/travel/06-jamaah-umrah-setelah-simpan.png)

### Cara 2: unggah banyak sekaligus (Excel)

1. Di halaman tambah jamaah, klik **Upload XLSX**.
2. Unduh template jika tersedia, atau siapkan file Excel dengan kolom yang sama.
3. Pastikan NIK tidak dobel dalam travel Anda.
4. Unggah file, lalu cek hasilnya di daftar jamaah.

### Tips agar tidak gagal

- NIK harus 16 digit, tanpa spasi.
- Nomor HP harus diawali `08`.
- Nama dan alamat diisi lengkap sesuai dokumen.
- Jamaah harus sudah tersimpan **sebelum** dipilih di form BA.

### Edit / hapus

- Dari daftar jamaah, buka detail atau edit jika ada kesalahan ketik.
- NIK biasanya tidak bisa diubah setelah tersimpan; jika salah besar, hubungi admin Kanwil.

---

## 7. Mengisi jamaah haji khusus (hanya PIHK)

Menu: **Data Jamaah → Jamaah Haji Khusus**

Formnya lebih lengkap (wizard multi langkah). Siapkan:

- Biodata (KTP, tempat/tanggal lahir, jenis kelamin, alamat, HP, nama ayah, pekerjaan, pendidikan, status pernikahan, golongan darah)
- Data paspor dan nomor porsi
- Scan dokumen: KTP, KK, paspor, foto, surat keterangan, bukti setor bank

Isi bertahap sesuai langkah di layar, unggah dokumen tiap tahap, lalu simpan sampai status lengkap.

---

## 8. Atur paket umrah (disarankan)

Menu: **Keberangkatan → Paket Umrah Saya**

![Paket umrah saya](assets/panduan/travel/07-paket-umrah.png)

1. Buat paket baru (nama paket, harga, jumlah hari, maskapai default jika ada).
2. Aktifkan paket yang masih dipakai.
3. Saat membuat BA, pilih paket tersebut agar field harga/hari/maskapai terisi otomatis.

Ini tidak wajib, tetapi sangat mengurangi salah ketik di form BA.

---

## 9. Mengajukan BA Pemberangkatan

Menu: **Keberangkatan → BA Pemberangkatan**

![Daftar BA Pemberangkatan](assets/panduan/travel/08-bap-daftar.png)

Wizard ada **3 langkah**. Ikuti urutannya.

### Langkah 1: isi data

1. Klik **Tambah** (atau **Ajukan BA Baru** dari Beranda).
2. Pastikan data penandatangan, PPIU, nomor HP, dan Kab/Kota sudah benar (biasanya terisi otomatis).
3. Pilih jamaah yang akan berangkat.
4. (Opsional) pilih **Paket** agar harga dan hari terisi otomatis.
5. Isi **Jumlah Hari** dan **Harga per Orang**.
6. Pilih maskapai keberangkatan dan kepulangan (bisa pilih “lainnya” lalu ketik nama maskapai).
7. Klik **Simpan & Lanjutkan**.

![Langkah 1 isi data BA](assets/panduan/travel/09-bap-langkah1-isi-data.png)

Jika belum ada jamaah, sistem akan mengingatkan Anda untuk mengisi jamaah dulu.

### Langkah 2: unggah PDF

1. Unggah **surat pernyataan** dalam format PDF.
2. Pastikan file sudah ditandatangani dan isinya sesuai data di Langkah 1.
3. Klik **Unggah & Lanjutkan**.

![Langkah 2 unggah PDF](assets/panduan/travel/10-bap-langkah2-unggah-pdf.png)

### Langkah 3: review dan ajukan

1. Periksa ringkasan data dan PDF.
2. Jika ada yang salah, kembali ke langkah sebelumnya untuk memperbaiki.
3. Klik **Ajukan ke Kabupaten/Kanwil**.
4. Status berubah menjadi **Diajukan**.

![Langkah 3 review](assets/panduan/travel/11-bap-langkah3-review.png)

![Konfirmasi ajukan](assets/panduan/travel/12-bap-konfirmasi-ajukan.png)

![BA sudah diajukan](assets/panduan/travel/13-bap-setelah-diajukan.png)

### Arti status BA

| Status di layar | Artinya untuk travel |
|-----------------|----------------------|
| Draf / pending | Belum diajukan; lanjutkan wizard |
| **Diajukan** | Sudah terkirim, menunggu petugas |
| **Diproses** | Sedang ditinjau Kabupaten/Kota atau Kanwil |
| **Diterima** | Disetujui; jadwal muncul di kalender; BA bisa dicetak |

Setelah **Diterima**, buka **Keberangkatan → Jadwal Keberangkatan** untuk melihat tanggalnya.

![Jadwal setelah BA diterima](assets/panduan/travel/15-jadwal-setelah-disetujui.png)

![Status BA Diterima](assets/panduan/travel/16-bap-status-diterima.png)

---

## 10. Melihat sertifikat

Menu: **Sertifikat → Sertifikat Saya**

![Sertifikat saya](assets/panduan/travel/14-sertifikat-saya.png)

- Sertifikat diterbitkan oleh admin Kabupaten/Kota atau Kanwil.
- Unduh dari halaman ini jika sudah tersedia.
- Perhatikan sisa masa berlaku (sistem menandai jika mendekati habis).

Travel **tidak** membuat sertifikat sendiri dari menu ini.

---

## 11. Menanggapi temuan pengawasan

Menu: **Tugas dari Kanwil → Tindak Lanjut Temuan**

Jika travel Anda pernah diperiksa dan ada temuan:

1. Buka daftar tindak lanjut yang menunggu tindakan.
2. Baca keterangan temuan.
3. Unggah bukti perbaikan (foto / PDF) beserta keterangan.
4. Kirim, lalu tunggu verifikasi pengawas.
5. Jika diminta revisi, perbaiki dan unggah ulang sampai disetujui.

---

## 12. Checklist kerja harian travel

Centang sebelum mengajukan keberangkatan:

- [ ] Login berhasil dan status registrasi **Disetujui**
- [ ] Jamaah yang berangkat sudah masuk di **Jamaah Umrah** (atau Haji Khusus)
- [ ] Paket umrah sudah diatur (opsional tapi disarankan)
- [ ] BA diisi lengkap (jamaah, hari, harga, maskapai, tanggal)
- [ ] PDF surat pernyataan sudah diunggah
- [ ] Tombol **Ajukan ke Kabupaten/Kanwil** sudah diklik
- [ ] Status BA sudah **Diajukan** (bukan masih draf)
- [ ] Setelah disetujui, jadwal dicek di **Jadwal Keberangkatan**

---

## 13. Masalah yang sering terjadi

| Gejala | Penyebab umum | Yang perlu dilakukan |
|--------|---------------|----------------------|
| Tidak bisa login | Registrasi belum disetujui / ditolak | Cek status; jika ditolak, daftar ulang |
| Tombol ajukan BA tidak aktif | Belum ada jamaah | Isi jamaah dulu |
| Error NIK | Bukan 16 digit atau dobel | Perbaiki NIK |
| Error nomor HP | Tidak diawali 08 | Perbaiki format HP |
| PDF gagal diunggah | File terlalu besar / bukan PDF | Perkecil file, pastikan PDF |
| Jadwal kosong | BA belum **Diterima** | Tunggu proses Kabko/Kanwil |
| Tidak melihat menu Haji Khusus | Travel berstatus PPIU | Normal; PPIU hanya umrah |

---

## 14. Urutan kerja yang benar (ringkas)

```
Registrasi disetujui
        ↓
Isi Jamaah Umrah (dan Haji Khusus jika PIHK)
        ↓
Atur Paket Umrah Saya (opsional)
        ↓
Buat BA Pemberangkatan (isi data → unggah PDF → ajukan)
        ↓
Tunggu status Diproses → Diterima
        ↓
Cek Jadwal Keberangkatan + cetak BA jika perlu
```

---

*PANTAU · Kanwil Kementerian Haji dan Umroh NTB*

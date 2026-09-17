# Panduan Mengisi Data & Verifikasi: Admin Kabupaten/Kota

Panduan ini untuk **Admin Kabupaten/Kota** di sistem PANTAU.  
Wilayah kerja Anda otomatis terfilter ke kabupaten/kota yang terhubung ke akun. Anda hanya memproses data di wilayah sendiri.

Nama menu di bawah ini mengikuti yang tampil di sidebar kiri setelah login.

> Screenshot diambil otomatis dengan Playwright (**fullscreen 1920×1080**) dari satu data contoh **Kota Mataram** + **PT. Mataram Travel**.  
> Cara menghasilkan ulang gambar: lihat [PANDUAN_SCREENSHOTS.md](./PANDUAN_SCREENSHOTS.md).

---

## 1. Apa tugas Anda di sistem?

Fokus utama Anda:

1. Meninjau registrasi **cabang travel** di wilayah Anda (unggah rekomendasi)
2. Memproses **BA Pemberangkatan** hingga disetujui
3. Menangani **pengaduan** masyarakat terkait travel di wilayah Anda
4. Menerbitkan **sertifikat PPIU** untuk travel yang berhak
5. Memantau data jamaah dan profil kepatuhan wilayah

Keputusan akhir untuk **registrasi travel pusat** tetap di Kanwil. Untuk cabang, Anda meninjau dulu, lalu Kanwil memberi keputusan akhir.

---

## 2. Menu yang Anda lihat setelah login

![Menu sidebar admin kabupaten](assets/panduan/kabupaten/02-sidebar-menu.png)

| Menu di sidebar | Fungsi singkat |
|-----------------|----------------|
| **Beranda** | Antrian wilayah: BA yang menunggu, pengaduan, kondisi kerja |
| **Tugas Wilayah → BA Pemberangkatan** | Tinjau dan setujui pengajuan keberangkatan |
| **Tugas Wilayah → Pengaduan** | Proses pengaduan masyarakat di wilayah Anda |
| **Tugas Wilayah → Jadwal Keberangkatan** | Kalender BA yang sudah **Diterima** |
| **Data Travel → PPIU Pusat** | Lihat travel pusat terkait wilayah |
| **Data Travel → PPIU Cabang** | Data cabang + verifikasi registrasi cabang |
| **Data Travel → Sertifikat** | Terbitkan / kelola sertifikat PPIU |
| **Data Jamaah → Jamaah Umrah** | Pantau jamaah umrah di wilayah |
| **Data Jamaah → Jamaah Haji Khusus** | Pantau jamaah haji khusus di wilayah |
| **Profil Kepatuhan Wilayah** | Ringkasan kepatuhan travel di kabupaten/kota Anda |

Angka merah (badge) di menu menandakan ada antrean yang perlu segera dibuka.

---

## 3. Login pertama kali

1. Masuk dengan email / nomor HP dan password yang diberikan Kanwil.
2. Jika diminta **ganti password**, segera ganti dan simpan password baru.
3. Pastikan di sidebar tertulis wilayah yang benar (misalnya Kota Mataram).
4. Mulai dari **Beranda**: lihat kartu antrian BA dan pengaduan.

![Login admin kabupaten](assets/panduan/kabupaten/01-login.png)

![Beranda admin kabupaten](assets/panduan/kabupaten/03-beranda.png)

---

## 4. Alur kerja harian yang disarankan

```
Buka Beranda
   ↓
Cek badge / antrian BA Pemberangkatan
   ↓
Cek PPIU Cabang berstatus Menunggu Verifikasi
   ↓
Proses Pengaduan yang belum selesai
   ↓
Terbitkan Sertifikat jika ada permintaan resmi
   ↓
Cek Jadwal Keberangkatan & Profil Kepatuhan bila perlu
```

---

## 5. Verifikasi registrasi cabang travel

Menu: **Data Travel → PPIU Cabang**

![Daftar PPIU Cabang](assets/panduan/kabupaten/09-ppiu-cabang.png)

Cabang yang baru mendaftar mandiri masuk dengan status **Menunggu Verifikasi**.

### 5.1 Data yang sudah diisi travel (Anda periksa, bukan diketik ulang)

- Travel pusat yang ditunjuk (izinnya harus sudah disetujui)
- Kabupaten/kota cabang
- Nama kepala cabang, alamat, telepon
- Nomor dan tanggal SK / BA pembukaan cabang
- Dokumen:
  - OSS cabang
  - Akta notaris
  - KTP kepala cabang
  - SK domisili usaha kelurahan

Buka / pratinjau tiap berkas. Pastikan readable, nama cocok, dan lokasi sesuai wilayah Anda.

### 5.2 Langkah verifikasi Anda

1. Buka **PPIU Cabang**.
2. Cari baris berstatus **Menunggu Verifikasi**.
3. Klik tombol **Verifikasi**.
4. Periksa data dan dokumen di modal.
5. Jika layak, unggah **Rekomendasi / BA Laporan Peninjauan** (scan PDF/JPG/PNG, maks. 1,5 MB).
6. Isi **catatan peninjauan** (wajib untuk jejak audit Kanwil).
7. Simpan unggahan.

![Modal unggah rekomendasi cabang](assets/panduan/kabupaten/10-cabang-form-rekomendasi.png)

Setelah rekomendasi terunggah, status cabang menjadi **Menunggu Kanwil**.  
Kanwil yang memberi keputusan akhir: **Disetujui** atau **Ditolak**.

### 5.3 Jika cabang tidak layak

- Gunakan opsi **Tolak** (jika tersedia di halaman detail/tabel).
- Wajib isi alasan penolakan.
- Pendaftar harus mendaftar ulang (akun PIC lama dihapus).

### 5.4 Yang tidak Anda kerjakan

- Menyetujui akhir registrasi cabang (itu Kanwil)
- Memverifikasi registrasi **kantor pusat** (langsung ke Kanwil)

---

## 6. Memproses BA Pemberangkatan

Menu: **Tugas Wilayah → BA Pemberangkatan**

Ini tugas inti Anda. Travel mengajukan BA; Anda yang meninjau dan mengubah status.

### 6.1 Arti warna / status

| Status | Arti | Tindakan Anda |
|--------|------|---------------|
| **Diajukan** (biasanya biru) | Baru masuk | Buka Detail, mulai tinjau |
| **Diproses** (kuning) | Sedang ditinjau | Lanjutkan pemeriksaan, lalu putuskan |
| **Diterima** | Disetujui | Jadwal otomatis masuk kalender; BA bisa dicetak |

### 6.2 Langkah verifikasi BA

1. Di daftar BA, cari status **Diajukan**.

![Daftar BA Pemberangkatan](assets/panduan/kabupaten/04-bap-daftar.png)

2. Klik **Detail**.
3. Periksa:
   - Nama travel / PPIU
   - Kab/Kota (harus wilayah Anda)
   - Daftar jamaah
   - Jumlah hari dan harga per orang
   - Maskapai berangkat & pulang
   - Preview **PDF surat pernyataan**

![Detail BA Pemberangkatan](assets/panduan/kabupaten/05-bap-detail.png)

4. Ubah status: **Diajukan → Diproses**, lalu klik **Simpan status**.

![Status Diproses](assets/panduan/kabupaten/06-bap-status-diproses.png)

5. Jika data lengkap dan sesuai, ubah lagi: **Diproses → Diterima**, lalu **Simpan status**.

![Status Diterima](assets/panduan/kabupaten/07-bap-setelah-diterima.png)

6. Setelah **Diterima**, Anda dapat **Cetak BAP** bila diperlukan.

### 6.3 Apa yang dicek di PDF surat pernyataan

- Nama travel dan penandatangan cocok
- Tanggal / rencana keberangkatan masuk akal
- Daftar jamaah selaras dengan yang dipilih di sistem
- File terbaca jelas (bukan scan buram)

### 6.4 Setelah disetujui

Buka **Tugas Wilayah → Jadwal Keberangkatan**.  
Keberangkatan yang **Diterima** akan muncul di kalender. Jika kosong, pastikan status BA benar-benar **Diterima**.

![Jadwal keberangkatan](assets/panduan/kabupaten/08-jadwal-keberangkatan.png)

---

## 7. Memproses pengaduan

Menu: **Tugas Wilayah → Pengaduan**

![Daftar pengaduan](assets/panduan/kabupaten/11-pengaduan.png)

Pengaduan bisa masuk dari halaman publik atau dicatat petugas.

### 7.1 Status pengaduan

| Label di layar | Arti |
|----------------|------|
| **Menunggu** / Belum Diproses | Baru, belum ditindaklanjuti |
| **Sedang Diproses** | Sudah mulai ditangani |
| **Selesai** | Sudah selesai ditindaklanjuti |
| **Ditolak** | Tidak diteruskan / tidak memenuhi syarat |

### 7.2 Langkah kerja

1. Filter status **Menunggu** atau lihat kartu **Belum Diproses**.
2. Klik Detail / ikon mata.
3. Baca isi aduan, travel terkait, dan lampiran bukti (jika ada).
4. Koordinasikan dengan travel bila perlu klarifikasi.
5. Ubah status ke **Sedang Diproses** saat mulai dikerjakan.
6. Setelah selesai, ubah ke **Selesai** (isi tanggapan sesuai form yang tersedia).
7. Jika tidak valid, ubah ke **Ditolak** dengan alasan yang jelas.

Pelapor publik bisa mengecek status lewat token unik setelah pengaduan selesai.

---

## 8. Menerbitkan sertifikat PPIU

Menu: **Data Travel → Sertifikat**

Hanya Admin Kanwil dan Admin Kabupaten/Kota wilayah terkait yang boleh menerbitkan. Travel tidak bisa membuat sertifikat sendiri.

### 8.1 Persiapan

- Pastikan data travel pusat atau cabang sudah benar di sistem.
- Pastikan **Pengaturan Penandatangan** sudah diisi (nama pejabat, NIP, jabatan). Biasanya dikoordinasikan dengan Kanwil.
- Siapkan keputusan internal: travel mana yang akan diterbitkan sertifikatnya.

### 8.2 Langkah buat sertifikat

1. Buka **Sertifikat**.

![Daftar sertifikat](assets/panduan/kabupaten/12-sertifikat-daftar.png)

2. Klik **Buat Sertifikat** (atau tombol setara di halaman).
3. Pilih tab:
   - **Sertifikat Pusat**, atau
   - **Sertifikat Cabang**
4. Pilih travel / cabang dari dropdown.
5. Nama PPIU, kepala, dan alamat biasanya **terisi otomatis**. Cek lagi.
6. Isi tanggal diterbitkan / tanggal tanda tangan sesuai form.
7. Simpan / generate.

![Form buat sertifikat](assets/panduan/kabupaten/13-sertifikat-buat.png)

**Catatan penting:**
- Nomor surat dan nomor dokumen **diterbitkan sistem** otomatis. Anda tidak perlu (dan tidak bisa) mengarang nomor sendiri.
- Masa berlaku sertifikat berakhir setiap **1 Januari** tahun berikutnya, berapa pun bulan terbitnya.
- Sertifikat yang dibatalkan tidak memakai ulang nomor yang sama.

### 8.3 Setelah terbit

Travel mengunduh dari menu mereka: **Sertifikat → Sertifikat Saya**.  
Masyarakat bisa memverifikasi keaslian lewat QR di sertifikat.

---

## 9. Memantau data travel dan jamaah

### PPIU Pusat

Menu: **Data Travel → PPIU Pusat**  
Gunakan untuk melihat data perusahaan yang terkait wilayah Anda. Perubahan data izin (nomor SK, masa berlaku, akreditasi, jenis PPIU/PIHK) hanya boleh dilakukan petugas yang berwenang (Kanwil atau Kabko wilayah terkait).

### PPIU Cabang

Selain verifikasi registrasi, pastikan kontak dan kabupaten cabang sudah benar.

### Data Jamaah

Menu: **Data Jamaah → Jamaah Umrah** / **Jamaah Haji Khusus**

- Anda memantau, bukan menjadi pengisi utama (pengisi utama adalah travel).
- Pastikan jamaah sudah ada sebelum Anda menyetujui BA.
- Gunakan pencarian / filter jika perlu mengecek NIK atau nama tertentu.

---

## 10. Profil kepatuhan wilayah

Menu: **Profil Kepatuhan Wilayah**

Gunakan sebelum memproses BA atau pengaduan yang sensitif:

1. Cari travel di tabel.
2. Perhatikan level risiko dan temuan aktif.
3. Buka Detail untuk riwayat pengawasan / pengaduan.
4. Jika risiko tinggi, koordinasikan dengan Kanwil atau pengawas.

---

## 11. Data yang perlu Anda siapkan di luar sistem

Agar kerja di sistem lancar, siapkan dulu:

| Kebutuhan | Keterangan |
|-----------|------------|
| Akun login Kabko | Nama, email, HP, kabupaten/kota |
| Daftar travel aktif di wilayah | PPIU dan PIHK |
| Format rekomendasi cabang | Template BA / surat rekomendasi yang ditandatangani manual lalu di-scan |
| Data pejabat penandatangan sertifikat | Nama, NIP, jabatan (koordinasi Kanwil) |
| Kontak travel wilayah | Untuk klarifikasi BA / pengaduan |

Daftar checklist data lebih rinci ada di `docs/persiapan/PERSIAPAN_ADMIN_KABUPATEN.md`.

---

## 12. Checklist kerja Admin Kabupaten/Kota

### Verifikasi cabang

- [ ] Dokumen OSS, akta, KTP kepala cabang, SK domisili dicek
- [ ] Lokasi cabang memang di wilayah Anda
- [ ] Rekomendasi / BA peninjauan sudah diunggah
- [ ] Catatan peninjauan sudah diisi
- [ ] Status berubah menjadi **Menunggu Kanwil**

### Verifikasi BA Pemberangkatan

- [ ] Status awal **Diajukan** dibuka Detail
- [ ] PDF surat pernyataan dibaca
- [ ] Jamaah, harga, hari, maskapai masuk akal
- [ ] Status diubah **Diproses** lalu **Diterima**
- [ ] Jadwal muncul di **Jadwal Keberangkatan**

### Pengaduan

- [ ] Aduan baru dibuka dan dibaca
- [ ] Status diubah ke **Sedang Diproses** saat dikerjakan
- [ ] Ditutup dengan **Selesai** atau **Ditolak** + alasan

### Sertifikat

- [ ] Penandatangan sudah diatur
- [ ] Travel / cabang dipilih dengan benar
- [ ] Data otomatis dicek ulang
- [ ] Sertifikat berhasil digenerate

---

## 13. Masalah yang sering terjadi

| Gejala | Penyebab umum | Yang perlu dilakukan |
|--------|---------------|----------------------|
| Tidak melihat BA travel tertentu | Travel di luar wilayah akun | Normal; hanya wilayah Anda yang tampil |
| Cabang tidak muncul di antrean | Sudah **Menunggu Kanwil** / beda kabupaten | Cek filter status & wilayah |
| Tidak bisa setujui final cabang | Keputusan akhir di Kanwil | Unggah rekomendasi saja |
| Jadwal kosong setelah setujui | Status belum **Diterima** | Pastikan status akhir benar |
| Field sertifikat kosong | Travel belum lengkap datanya | Lengkapi data di PPIU Pusat/Cabang dulu |
| Password lama tidak bisa dipakai | Wajib ganti di login pertama | Ganti password, lalu masuk lagi |

---

## 14. Batas wewenang (supaya tidak salah langkah)

| Kegiatan | Admin Kabupaten/Kota | Kanwil |
|----------|----------------------|--------|
| Tinjau & rekomendasi cabang | Ya (wilayah sendiri) | Ya |
| Setujui akhir registrasi cabang | Tidak | Ya |
| Setujui registrasi pusat | Tidak | Ya |
| Proses BA Pemberangkatan wilayah | Ya | Ya (seluruh NTB) |
| Proses pengaduan wilayah | Ya | Ya |
| Terbitkan sertifikat wilayah | Ya | Ya |
| Mengubah data izin travel wilayah | Ya | Ya |

---

## 15. Ringkasan satu halaman

1. Login → buka **Beranda**.
2. **PPIU Cabang**: tinjau dokumen → unggah rekomendasi → tunggu Kanwil.
3. **BA Pemberangkatan**: Detail → cek PDF → **Diproses** → **Diterima**.
4. **Pengaduan**: baca → proses → **Selesai** / **Ditolak**.
5. **Sertifikat**: pilih travel → generate → travel unduh sendiri.
6. Pantau **Jadwal Keberangkatan** dan **Profil Kepatuhan Wilayah** bila perlu.

---

*PANTAU · Kanwil Kementerian Haji dan Umroh NTB*

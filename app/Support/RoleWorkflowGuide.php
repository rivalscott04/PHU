<?php

namespace App\Support;

use App\Enums\UserRole;

class RoleWorkflowGuide
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{
     *     title: string,
     *     hint: string,
     *     steps: list<string>,
     *     actions: list<array{label: string, url: string, style: string, icon: string}>
     * }|null
     */
    public static function for(string $page, array $context = []): ?array
    {
        $role = auth()->user()?->role;

        return match ($page) {
            'home' => match ($role) {
                UserRole::Kabupaten->value => self::homeKabupaten(),
                UserRole::Admin->value => self::homeAdmin(),
                default => null,
            },
            'bap_list' => in_array($role, [UserRole::Kabupaten->value, UserRole::Admin->value], true)
                ? self::bapListApprover()
                : ($role === UserRole::User->value ? self::bapListTravel() : null),
            'bap_detail' => in_array($role, [UserRole::Kabupaten->value, UserRole::Admin->value], true)
                ? self::bapDetailApprover((string) ($context['status'] ?? ''))
                : ($role === UserRole::User->value ? self::bapDetailTravel((string) ($context['status'] ?? '')) : null),
            'pengaduan' => $role === UserRole::Admin->value ? self::pengaduanAdmin() : null,
            'sertifikat' => in_array($role, [UserRole::Kabupaten->value, UserRole::Admin->value], true)
                ? self::sertifikat()
                : null,
            'keberangkatan' => self::keberangkatan(),
            'pengunduran' => in_array($role, [UserRole::Kabupaten->value, UserRole::Admin->value], true)
                ? self::pengunduran()
                : null,
            'cabang_travel' => in_array($role, [UserRole::Kabupaten->value, UserRole::Admin->value], true)
                ? self::cabangTravel($role)
                : null,
            'travel_master' => $role === UserRole::Admin->value ? self::travelMaster() : null,
            'users' => $role === UserRole::Admin->value ? self::usersAdmin() : null,
            'jamaah_umrah' => $role === UserRole::Admin->value ? self::jamaahUmrah() : null,
            'v2_dashboard' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2Dashboard($role)
                : null,
            'v2_monitoring' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2Monitoring($role)
                : null,
            'v2_pengawasan' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2Pengawasan($role)
                : null,
            'v2_followup' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2Followup($role)
                : null,
            'v2_risk' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2Risk($role)
                : null,
            'v2_compliance' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2Compliance($role)
                : null,
            'v2_checklist' => $role === UserRole::Admin->value ? self::v2Checklist() : null,
            'v2_audit_log' => in_array($role, [UserRole::Admin->value, UserRole::Pengawas->value], true)
                ? self::v2AuditLog()
                : null,
            default => null,
        };
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function homeKabupaten(): array
    {
        return [
            'title' => 'Tugas Anda di Kabupaten/Kota',
            'hint' => 'Fokus utama: memproses pengajuan keberangkatan jamaah (BA Pemberangkatan) di wilayah Anda.',
            'steps' => [
                'Lihat jumlah BA Diajukan / Diproses di kartu kiri atas',
                'Buka Keberangkatan → BA Pemberangkatan → klik Detail pada pengajuan',
                'Periksa PDF surat pernyataan, ubah status Diproses lalu Diterima',
                'Setelah disetujui, jadwal muncul di Jadwal Keberangkatan',
            ],
            'actions' => [
                ['label' => 'Proses BA Pemberangkatan', 'url' => route('bap'), 'style' => 'primary', 'icon' => 'bx-file'],
                ['label' => 'Jadwal Keberangkatan', 'url' => route('keberangkatan'), 'style' => 'outline-primary', 'icon' => 'bx-calendar'],
                ['label' => 'Data PPIU Cabang', 'url' => route('cabang.travel'), 'style' => 'outline-secondary', 'icon' => 'bx-buildings'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function homeAdmin(): array
    {
        return [
            'title' => 'Pusat Kendali Super Admin',
            'hint' => 'Anda mengelola seluruh NTB: keberangkatan jamaah, pengaduan publik, dan modul pengawasan digital.',
            'steps' => [
                'Cek Antrian Kerja untuk tugas pengawasan yang perlu ditindaklanjuti',
                'Proses pengaduan baru di menu Pengaduan',
                'Pantau risiko & kepatuhan travel lewat modul Pengawasan Digital',
                'Kelola master data travel dan pengguna bila diperlukan',
            ],
            'actions' => [
                ['label' => 'Antrian Kerja', 'url' => route('v2.antrian.index'), 'style' => 'primary', 'icon' => 'bx-list-check'],
                ['label' => 'Pengaduan', 'url' => route('pengaduan'), 'style' => 'outline-danger', 'icon' => 'bx-message-square-dots'],
                ['label' => 'Dashboard Pengawasan', 'url' => route('v2.dashboard'), 'style' => 'outline-primary', 'icon' => 'bx-bar-chart-alt-2'],
                ['label' => 'BA Pemberangkatan', 'url' => route('bap'), 'style' => 'outline-secondary', 'icon' => 'bx-file'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function bapListApprover(): array
    {
        return [
            'title' => 'Cara Memproses BA Pemberangkatan',
            'hint' => 'Travel mengajukan BA; Anda yang menyetujui atau menolak melalui perubahan status.',
            'steps' => [
                'Cari baris berstatus Diajukan (biru) atau Diproses (kuning)',
                'Klik Detail untuk melihat data lengkap dan PDF surat pernyataan',
                'Ubah status: Diajukan → Diproses → Diterima',
                'Setelah Diterima, cetak BA dan jadwal tampil di Jadwal Keberangkatan',
            ],
            'actions' => [
                ['label' => 'Jadwal Keberangkatan', 'url' => route('keberangkatan'), 'style' => 'outline-primary', 'icon' => 'bx-calendar'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function bapListTravel(): array
    {
        return [
            'title' => 'Cara Mengajukan BA Pemberangkatan',
            'hint' => 'Buat pengajuan keberangkatan, unggah surat pernyataan, lalu ajukan ke Kanwil/Kabupaten.',
            'steps' => [
                'Klik Tambah untuk membuat BA baru',
                'Isi data keberangkatan dan unggah PDF surat pernyataan',
                'Klik Ajukan di halaman Detail',
                'Tunggu persetujuan Kabupaten/Kanwil; pantau status di tabel ini',
            ],
            'actions' => [
                ['label' => 'Buat BA Baru', 'url' => route('form.bap'), 'style' => 'primary', 'icon' => 'bx-plus'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function bapDetailApprover(string $status): array
    {
        $hint = match ($status) {
            'diajukan' => 'Pengajuan baru masuk. Buka PDF, periksa kelengkapan, lalu ubah status ke Diproses.',
            'diproses' => 'Sedang ditinjau. Jika lengkap, ubah status ke Diterima untuk menyetujui keberangkatan.',
            'diterima' => 'Sudah disetujui. Anda dapat mencetak BA; jadwal otomatis masuk ke Jadwal Keberangkatan.',
            default => 'Periksa data dan PDF, lalu kelola status persetujuan di dropdown bawah.',
        };

        return [
            'title' => 'Langkah di Halaman Ini',
            'hint' => $hint,
            'steps' => [
                'Baca data keberangkatan dan preview PDF surat pernyataan',
                'Gunakan dropdown Status di bawah: Diproses → Diterima',
                'Setelah Diterima, klik Cetak BAP',
                'Travel dapat melihat jadwal di menu Jadwal Keberangkatan',
            ],
            'actions' => [
                ['label' => 'Kembali ke Daftar BA', 'url' => route('bap'), 'style' => 'outline-secondary', 'icon' => 'bx-arrow-back'],
                ['label' => 'Jadwal Keberangkatan', 'url' => route('keberangkatan'), 'style' => 'outline-primary', 'icon' => 'bx-calendar'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function bapDetailTravel(string $status): array
    {
        $hint = match ($status) {
            'pending', '' => 'Lengkapi data dan unggah PDF surat pernyataan terlebih dahulu.',
            'diajukan' => 'Pengajuan sudah dikirim. Tunggu Kabupaten/Kanwil memproses.',
            'diproses' => 'Sedang ditinjau oleh Kabupaten/Kanwil.',
            'diterima' => 'Disetujui. Anda dapat mencetak BA dan melihat jadwal keberangkatan.',
            default => 'Ikuti langkah di bawah untuk menyelesaikan pengajuan.',
        };

        return [
            'title' => 'Langkah di Halaman Ini',
            'hint' => $hint,
            'steps' => [
                'Pastikan PDF surat pernyataan sudah diunggah',
                'Klik Ajukan jika status masih menunggu pengajuan',
                'Pantau perubahan status dari Kabupaten/Kanwil',
                'Setelah Diterima, cetak BA dari tombol Cetak BAP',
            ],
            'actions' => [
                ['label' => 'Kembali ke Daftar BA', 'url' => route('bap'), 'style' => 'outline-secondary', 'icon' => 'bx-arrow-back'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function pengaduanAdmin(): array
    {
        return [
            'title' => 'Cara Menangani Pengaduan',
            'hint' => 'Pengaduan dari masyarakat masuk otomatis ke Antrian Kerja pengawasan saat disimpan.',
            'steps' => [
                'Filter status Belum Diproses untuk melihat pengaduan baru',
                'Klik Detail / ikon mata untuk membaca keluhan lengkap',
                'Koordinasikan penyelesaian dengan travel terkait',
                'Ubah status menjadi Selesai; antrian pengawasan ikut tertutup',
            ],
            'actions' => [
                ['label' => 'Antrian Pengaduan', 'url' => route('v2.antrian.index', ['type' => 'pengaduan']), 'style' => 'primary', 'icon' => 'bx-list-check'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function sertifikat(): array
    {
        return [
            'title' => 'Cara Mengelola Sertifikat PPIU',
            'hint' => 'Terbitkan dan kelola sertifikat izin penyelenggara ibadah umrah.',
            'steps' => [
                'Pastikan pengaturan penandatangan sudah diisi (tombol Pengaturan Penandatangan)',
                'Klik Buat Sertifikat untuk travel yang memerlukan',
                'Isi data dan generate dokumen sertifikat',
                'Travel dapat mengunduh sertifikat dari akun mereka',
            ],
            'actions' => [],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function keberangkatan(): array
    {
        $role = auth()->user()?->role;

        return [
            'title' => 'Jadwal Keberangkatan',
            'hint' => $role === UserRole::Kabupaten->value
                ? 'Kalender ini menampilkan keberangkatan yang BAnya sudah Anda setujui (status Diterima).'
                : 'Kalender keberangkatan jamaah dari BA Pemberangkatan yang telah disetujui.',
            'steps' => [
                'Klik tanggal di kalender untuk melihat detail keberangkatan',
                'Data berasal dari BA Pemberangkatan berstatus Diterima',
                'Jika jadwal kosong, pastikan BA sudah disetujui di menu BA Pemberangkatan',
            ],
            'actions' => [
                ['label' => 'BA Pemberangkatan', 'url' => route('bap'), 'style' => 'outline-primary', 'icon' => 'bx-file'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function pengunduran(): array
    {
        return [
            'title' => 'Cara Memproses Pengunduran',
            'hint' => 'Kelola permohonan pengunduran keberangkatan dari travel di wilayah Anda.',
            'steps' => [
                'Cari permohonan pengunduran yang masuk',
                'Klik Detail untuk melihat alasan dan data jamaah',
                'Proses persetujuan sesuai kebijakan',
            ],
            'actions' => [
                ['label' => 'BA Pemberangkatan', 'url' => route('bap'), 'style' => 'outline-secondary', 'icon' => 'bx-file'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function cabangTravel(?string $role = null): array
    {
        $isKabupaten = ($role ?? auth()->user()?->role) === UserRole::Kabupaten->value;

        return [
            'title' => 'Data PPIU Cabang',
            'hint' => $isKabupaten
                ? 'Daftar travel cabang yang beroperasi di wilayah kabupaten/kota Anda.'
                : 'Kelola data travel cabang seluruh NTB, impor/ekspor Excel bila perlu.',
            'steps' => $isKabupaten
                ? [
                    'Gunakan tabel untuk melihat travel cabang terdaftar',
                    'Pastikan data kontak dan kabupaten sudah benar',
                    'Travel cabang mengajukan BA melalui akun user travel masing-masing',
                ]
                : [
                    'Gunakan Import Excel untuk memuat data cabang massal',
                    'Klik Tambah untuk menambah cabang satu per satu',
                    'Pastikan setiap cabang terhubung ke akun user travel',
                ],
            'actions' => [
                ['label' => 'BA Pemberangkatan', 'url' => route('bap'), 'style' => 'primary', 'icon' => 'bx-file'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function travelMaster(): array
    {
        return [
            'title' => 'Data PPIU Pusat',
            'hint' => 'Kelola penyelenggara perjalanan ibadah umrah/haji tingkat pusat dan akun user travelnya.',
            'steps' => [
                'Gunakan Import Excel untuk memuat data travel massal',
                'Klik Tambah untuk mendaftarkan PPIU/PIHK baru',
                'Pastikan setiap travel punya akun user agar bisa mengajukan BA',
                'Cek kapabilitas layanan (Umrah/Haji) sesuai jenis izin',
            ],
            'actions' => [
                ['label' => 'Kelola Pengguna', 'url' => route('users.index'), 'style' => 'outline-primary', 'icon' => 'bx-user'],
                ['label' => 'Data Cabang', 'url' => route('cabang.travel'), 'style' => 'outline-secondary', 'icon' => 'bx-buildings'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function usersAdmin(): array
    {
        return [
            'title' => 'Kelola Pengguna',
            'hint' => 'Buat dan atur akun untuk setiap peran: Pimpinan, Kabupaten, Pengawas, dan User Travel.',
            'steps' => [
                'Pilih tab peran di atas (Pengawas, Kabupaten, User Travel, dll.)',
                'Klik Tambah Pengguna untuk membuat akun baru',
                'Untuk Pengawas: isi kabupaten/wilayah kerja agar data ter scope benar',
                'Untuk User Travel: hubungkan ke travel company yang sudah terdaftar',
            ],
            'actions' => [
                ['label' => 'Data PPIU Pusat', 'url' => route('travel'), 'style' => 'outline-secondary', 'icon' => 'bx-building'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function jamaahUmrah(): array
    {
        return [
            'title' => 'Data Jamaah Umrah',
            'hint' => 'Pantau data jamaah umrah yang diinput travel, dipakai sebagai dasar pengajuan BA Pemberangkatan.',
            'steps' => [
                'Travel menginput jamaah dari akun mereka',
                'Gunakan filter dan pencarian untuk menemukan jamaah tertentu',
                'Pastikan jamaah terdaftar sebelum travel membuat BA Pemberangkatan',
            ],
            'actions' => [
                ['label' => 'BA Pemberangkatan', 'url' => route('bap'), 'style' => 'outline-primary', 'icon' => 'bx-file'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Dashboard(string $role): array
    {
        $isAdmin = $role === UserRole::Admin->value;

        return [
            'title' => 'Cara Membaca Dashboard Pengawasan',
            'hint' => 'Ringkasan eksekutif, gunakan untuk melihat gambaran besar, bukan untuk menyelesaikan tugas harian.',
            'steps' => [
                'Terapkan filter kabupaten / periode sesuai kebutuhan',
                'Identifikasi wilayah atau travel yang perlu perhatian (peringatan, risiko tinggi)',
                $isAdmin
                    ? 'Turun ke Antrian Kerja untuk menindaklanjuti tugas spesifik'
                    : 'Turun ke Antrian Kerja Anda untuk menindaklanjuti tugas spesifik',
                'Buka Monitoring untuk detail per travel',
            ],
            'actions' => [
                ['label' => 'Antrian Kerja', 'url' => route('v2.antrian.index'), 'style' => 'primary', 'icon' => 'bx-list-check'],
                ['label' => 'Monitoring PPIU', 'url' => route('v2.monitoring.index'), 'style' => 'outline-primary', 'icon' => 'bx-radar'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Monitoring(string $role): array
    {
        return [
            'title' => 'Cara Menggunakan Monitoring',
            'hint' => 'Pantau aktivitas operasional travel: keberangkatan, pengawasan, pengaduan, dan risiko.',
            'steps' => [
                'Baca KPI di atas untuk gambaran cepat wilayah Anda',
                'Klik Data Travel untuk daftar lengkap per penyelenggara',
                'Klik baris travel untuk melihat Profil Kepatuhan',
                'Jika ada risiko tinggi, buka Antrian Kerja atau jadwalkan pemeriksaan',
            ],
            'actions' => [
                ['label' => 'Data Travel', 'url' => route('v2.monitoring.travel'), 'style' => 'primary', 'icon' => 'bx-list-ul'],
                ['label' => 'Antrian Kerja', 'url' => route('v2.antrian.index'), 'style' => 'outline-primary', 'icon' => 'bx-list-check'],
                ['label' => 'Profil Kepatuhan', 'url' => route('v2.compliance.index'), 'style' => 'outline-secondary', 'icon' => 'bx-shield-quarter'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Pengawasan(string $role): array
    {
        return [
            'title' => 'Cara Mengelola BA Pemeriksaan',
            'hint' => 'Modul inspeksi pengawasan PPIU, terpisah dari BA Pemberangkatan (persetujuan keberangkatan jamaah).',
            'steps' => [
                'Buat Pemeriksaan untuk menjadwalkan inspeksi ke travel',
                'Ubah status: Dijadwalkan → Berlangsung → catat temuan',
                'Travel mengunggah tindak lanjut; verifikasi di menu Tindak Lanjut Temuan',
                'Tutup pengawasan setelah semua temuan selesai',
            ],
            'actions' => [
                ['label' => 'Buat Pemeriksaan', 'url' => route('v2.pengawasan.create'), 'style' => 'primary', 'icon' => 'bx-calendar-plus'],
                ['label' => 'Antrian Kerja', 'url' => route('v2.antrian.index'), 'style' => 'outline-primary', 'icon' => 'bx-list-check'],
                ['label' => 'Verifikasi Tindak Lanjut', 'url' => route('v2.followup.index'), 'style' => 'outline-secondary', 'icon' => 'bx-task'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Followup(string $role): array
    {
        return [
            'title' => 'Cara Verifikasi Tindak Lanjut',
            'hint' => 'Travel mengunggah bukti perbaikan; Anda memverifikasi di sini atau lewat Antrian Kerja.',
            'steps' => [
                'Cari baris berstatus Diajukan / Menunggu',
                'Klik Detail untuk melihat lampiran dan keterangan travel',
                'Klik Setujui jika bukti sesuai, atau Minta Revisi jika perlu perbaikan',
                'Setelah disetujui, tutup BA Pemeriksaan terkait jika semua temuan selesai',
            ],
            'actions' => [
                ['label' => 'Antrian Verifikasi', 'url' => route('v2.antrian.index', ['type' => 'verifikasi_followup']), 'style' => 'primary', 'icon' => 'bx-list-check'],
                ['label' => 'BA Pemeriksaan', 'url' => route('v2.pengawasan.index'), 'style' => 'outline-secondary', 'icon' => 'bx-search-alt'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Risk(string $role): array
    {
        return [
            'title' => 'Cara Membaca Skor Risiko',
            'hint' => 'Skor dihitung otomatis, gunakan untuk memprioritaskan travel yang perlu pengawasan.',
            'steps' => [
                'Lihat ranking travel berdasarkan skor dan level risiko',
                'Klik Detail pada travel prioritas untuk breakdown indikator',
                'Ikuti rekomendasi: monitoring intensif atau jadwalkan pemeriksaan',
                'Tugas risiko tinggi juga muncul di Antrian Kerja',
            ],
            'actions' => [
                ['label' => 'Antrian Kerja', 'url' => route('v2.antrian.index', ['type' => 'risiko_tinggi']), 'style' => 'primary', 'icon' => 'bx-list-check'],
                ['label' => 'Buat Pemeriksaan', 'url' => route('v2.pengawasan.create'), 'style' => 'outline-primary', 'icon' => 'bx-calendar-plus'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Compliance(string $role): array
    {
        return [
            'title' => 'Cara Membaca Profil Kepatuhan',
            'hint' => 'Gambaran menyeluruh kepatuhan satu travel: sertifikat, temuan, pengaduan, dan BAP.',
            'steps' => [
                'Cari travel di tabel atau filter wilayah',
                'Klik Detail untuk melihat skor kepatuhan dan riwayat lengkap',
                'Gunakan rekomendasi di halaman detail untuk menentukan tindakan',
                'Hubungkan dengan Monitoring atau Antrian Kerja bila perlu tindak lanjut',
            ],
            'actions' => [
                ['label' => 'Monitoring PPIU', 'url' => route('v2.monitoring.index'), 'style' => 'outline-primary', 'icon' => 'bx-radar'],
                ['label' => 'Skor Risiko', 'url' => route('v2.risk.index'), 'style' => 'outline-secondary', 'icon' => 'bx-error-circle'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2Checklist(): array
    {
        return [
            'title' => 'Cara Mengatur Master Checklist',
            'hint' => 'Checklist dipakai saat BA Pemeriksaan, atur item pemeriksaan di sini sebelum inspeksi lapangan.',
            'steps' => [
                'Buat atau edit kategori dan item checklist',
                'Pastikan bobot dan jenis pemeriksaan sudah sesuai',
                'Pengawas akan mengisi checklist saat inspeksi berlangsung',
            ],
            'actions' => [
                ['label' => 'BA Pemeriksaan', 'url' => route('v2.pengawasan.index'), 'style' => 'outline-primary', 'icon' => 'bx-search-alt'],
            ],
        ];
    }

    /** @return array{title: string, hint: string, steps: list<string>, actions: list<array{label: string, url: string, style: string, icon: string}>} */
    private static function v2AuditLog(): array
    {
        return [
            'title' => 'Log Aktivitas',
            'hint' => 'Jejak audit semua aksi penting, gunakan untuk melacak siapa melakukan apa dan kapan.',
            'steps' => [
                'Filter modul (pengawasan, followup, risk, dll.) untuk mempersempit pencarian',
                'Gunakan kotak pencarian untuk nama travel atau nomor pengawasan',
                'Klik baris untuk detail lengkap aktivitas',
            ],
            'actions' => [],
        ];
    }
}

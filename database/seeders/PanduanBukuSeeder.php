<?php

namespace Database\Seeders;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\TravelRegistrationStatus;
use App\Models\BAP;
use App\Models\CabangTravel;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\Pengaduan;
use App\Models\SertifikatSetting;
use App\Models\TravelCompany;
use App\Models\User;
use App\Services\RiskCalculationService;
use Database\Seeders\Concerns\SeedsSampleDocuments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data contoh untuk screenshot buku panduan per role (docs/panduan). Jalankan di
 * database terpisah, bukan database kerja:
 *
 *   DB_DATABASE=phu_panduan php artisan migrate:fresh --seed
 *   DB_DATABASE=phu_panduan php artisan db:seed --class=PanduanBukuSeeder
 */
class PanduanBukuSeeder extends Seeder
{
    use SeedsSampleDocuments;

    public function run(): void
    {
        $this->call([
            DevTravelSeeder::class,
            CabangTravelSeeder::class,
            DemoKeberangkatanSeeder::class,
            MataramTravelPackageSeeder::class,
        ]);

        $travels = collect([
            ['PT. Lombok Amanah Wisata', 'Lombok Timur', 'PPIU', 'Hj. Baiq Rahmawati', 'Jl. TGKH Zainuddin Abdul Madjid No. 12, Selong'],
            ['PT. Samawa Rihlah Mandiri', 'Sumbawa', 'PPIU', 'H. Syamsuddin', 'Jl. Garuda No. 5, Sumbawa Besar'],
            ['PT. Bima Al Haramain', 'Kota Bima', 'PIHK', 'H. Abdul Muis', 'Jl. Soekarno Hatta No. 30, Kota Bima'],
        ])->map(fn ($t, $i) => $this->travel(...$t, index: $i));

        $mataram = TravelCompany::where('Penyelenggara', 'PT. Mataram Travel')->first();
        $all = collect([$mataram])->merge($travels);

        $this->penandatangan();
        $this->baBerbagaiStatus($mataram);
        $this->pengaduan($all);
        $this->pemeriksaan($all);
        $this->cabangMenunggu($mataram);

        app(RiskCalculationService::class)->recalculateAll(logAudit: false);

        // Akun contoh langsung masuk Beranda, tanpa layar wajib ganti password.
        User::query()->update(['is_password_changed' => true]);

        $this->command->info('Data buku panduan siap.');
    }

    private function travel(string $nama, string $kab, string $status, string $pimpinan, string $alamat, int $index): TravelCompany
    {
        $travel = TravelCompany::updateOrCreate(['Penyelenggara' => $nama], [
            'Pusat' => 'SK.' . (410 + $index) . '/PPIU/2023',
            'Tanggal' => '2023-0' . ($index + 3) . '-15',
            'license_expiry' => now()->addMonths(6 + $index * 10)->toDateString(),
            'nilai_akreditasi' => ['A', 'B', 'A'][$index],
            'tanggal_akreditasi' => '2023-05-01',
            'lembaga_akreditasi' => 'Kementerian Haji dan Umroh',
            'Pimpinan' => $pimpinan,
            'alamat_kantor_lama' => $alamat,
            'alamat_kantor_baru' => $alamat,
            'Telepon' => '0370-6' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
            'Status' => $status,
            'kab_kota' => $kab,
            'can_haji' => $status === 'PIHK',
            'can_umrah' => true,
            'registration_status' => TravelRegistrationStatus::Approved,
            'dokumen_sk' => $this->sampleDocument("registrasi-travel/sk/contoh-sk-{$index}.pdf"),
        ]);
        $travel->setDefaultCapabilities();
        $travel->save();

        User::updateOrCreate(['email' => 'travel' . ($index + 2) . '@contoh.id'], [
            'nama' => 'PIC ' . $nama,
            'nomor_hp' => '08123000000' . $index,
            'password' => Hash::make('password123'),
            'role' => 'user',
            'travel_id' => $travel->id,
            'is_password_changed' => true,
        ]);

        return $travel;
    }

    private function penandatangan(): void
    {
        SertifikatSetting::updateOrCreate(['id' => 1], [
            'nama_penandatangan' => 'Nama Kepala Kanwil',
            'nip_penandatangan' => '197001011995031001',
            'jabatan_penandatangan' => 'Kepala Kantor Wilayah',
        ]);
    }

    /** BA milik PT. Mataram Travel yang belum disetujui, supaya antrean verifikasi tidak kosong. */
    private function baBerbagaiStatus(TravelCompany $travel): void
    {
        $owner = User::where('travel_id', $travel->id)->where('role', 'user')->first();

        foreach ([['DEMO-BAP-AJU', 'diajukan', 12, 20], ['DEMO-BAP-PROSES', 'diproses', 21, 30]] as [$key, $status, $berangkat, $pulang]) {
            BAP::updateOrCreate(['nomor_surat' => $key], [
                'name' => $travel->Pimpinan,
                'jabatan' => 'Pimpinan Travel',
                'ppiuname' => $travel->Penyelenggara,
                'address_phone' => $travel->alamat_kantor_baru . ' / ' . $travel->Telepon,
                'kab_kota' => $travel->kab_kota,
                'people' => 3,
                'days' => 9,
                'price' => 33500000,
                'datetime' => now()->addDays($berangkat)->toDateString(),
                'returndate' => now()->addDays($pulang)->toDateString(),
                'airlines' => 'Garuda Indonesia GA-980',
                'airlines2' => 'Garuda Indonesia GA-981',
                'status' => $status,
                'user_id' => $owner?->id,
                'pdf_file_path' => $this->sampleDocument("bap/contoh-surat-pernyataan-{$status}.pdf"),
            ]);
        }
    }

    private function pengaduan($travels): void
    {
        $data = [
            ['Ahmad Zulkarnain', 0, 'Jadwal keberangkatan diundur dua kali tanpa pemberitahuan resmi.', 'pending'],
            ['Siti Hajar', 1, 'Pengembalian uang muka belum diterima setelah 40 hari.', 'in_progress'],
            ['Lalu Muhammad Iqbal', 2, 'Hotel di Makkah tidak sesuai dengan yang dijanjikan di brosur.', 'completed'],
        ];

        foreach ($data as [$nama, $i, $hal, $status]) {
            Pengaduan::updateOrCreate(['nama_pengadu' => $nama], [
                'travels_id' => $travels[$i]->id,
                'hal_aduan' => $hal,
                'status' => $status,
                'completed_at' => $status === 'completed' ? now()->subDays(2) : null,
            ]);
        }
    }

    private function pemeriksaan($travels): void
    {
        $pengawas = User::where('role', 'pengawas')->first() ?? User::where('role', 'admin')->first();

        $this->inspeksi($travels[0], $pengawas, 'PW-2026-MTR-001', InspectionStatus::WaitingFollowup, [
            ['Pelayanan', FindingSeverity::Major, 'Informasi perubahan hotel terlambat', 'Jamaah baru diberi tahu lewat grup pesan H-1.', 'Sampaikan perubahan secara tertulis paling lambat H-7.', 7, FindingStatus::WaitingResponse],
            ['Administrasi', FindingSeverity::Minor, 'Nomor layanan pelanggan tidak aktif', 'Dua nomor layanan tidak dapat dihubungi pada jam kerja.', 'Pastikan nomor layanan aktif pukul 08.00 sampai 17.00.', 14, FindingStatus::Open],
        ]);
        $this->inspeksi($travels[1], $pengawas, 'PW-2026-LTM-001', InspectionStatus::WaitingFollowup, [
            ['Keuangan', FindingSeverity::Critical, 'Pengembalian dana jamaah tertunda', 'Tiga jamaah menunggu pengembalian dana lebih dari 30 hari.', 'Selesaikan pengembalian dana dalam 14 hari.', -2, FindingStatus::WaitingResponse],
        ]);
        $this->inspeksi($travels[2], $pengawas, 'PW-2026-SBW-001', InspectionStatus::Closed, [
            ['Administrasi', FindingSeverity::Minor, 'Penataan arsip jamaah', 'Label arsip belum seragam.', 'Seragamkan label map arsip.', -30, FindingStatus::Verified],
        ]);
    }

    private function inspeksi(TravelCompany $travel, ?User $pengawas, string $no, InspectionStatus $status, array $temuan): void
    {
        $inspeksi = Inspection::updateOrCreate(['inspection_no' => $no], [
            'travel_id' => $travel->id,
            'inspection_date' => now()->subDays(10),
            'inspection_type' => InspectionType::Routine,
            'overall_score' => $status === InspectionStatus::Closed ? 92 : 68,
            'status' => $status,
            'notes' => 'Pemeriksaan rutin kantor travel.',
            'created_by' => $pengawas?->id,
            'updated_by' => $pengawas?->id,
        ]);

        foreach ($temuan as [$kategori, $tingkat, $judul, $uraian, $saran, $tenggat, $statusTemuan]) {
            InspectionFinding::updateOrCreate(['inspection_id' => $inspeksi->id, 'title' => $judul], [
                'category' => $kategori,
                'severity' => $tingkat,
                'description' => $uraian,
                'recommendation' => $saran,
                'deadline' => now()->addDays($tenggat),
                'status' => $statusTemuan,
            ]);
        }
    }

    /** Cabang yang baru mendaftar, untuk contoh verifikasi Kabupaten/Kota dan stepper Beranda. */
    private function cabangMenunggu(TravelCompany $pusat): void
    {
        $cabang = CabangTravel::updateOrCreate(['travel_id' => $pusat->id, 'kabupaten' => 'Lombok Barat'], [
            'Penyelenggara' => $pusat->Penyelenggara,
            'pusat' => $pusat->Pusat,
            'pimpinan_pusat' => $pusat->Pimpinan,
            'alamat_pusat' => $pusat->alamat_kantor_baru,
            'SK_BA' => 'SK.021/MT-LB/2026',
            'tanggal' => now()->subDays(20)->toDateString(),
            'pimpinan_cabang' => 'Lalu Ahmad Fauzi',
            'alamat_cabang' => 'Jl. Raya Gerung No. 17, Gerung, Lombok Barat',
            'telepon' => '081339001122',
            'registration_status' => TravelRegistrationStatus::Pending,
            'nib' => '9120301234567', 'tanggal_nib' => now()->subDays(40)->toDateString(),
            'notaris_akta' => 'Hj. Siti Aminah, S.H., M.Kn.', 'nomor_akta' => '14', 'tanggal_akta' => now()->subDays(35)->toDateString(),
            'nik_kepala' => '5201011203850004', 'npwp_kepala' => '71.234.567.8-911.000',
            'nomor_skdu' => '503/118/GRG/2026', 'tanggal_skdu' => now()->subDays(30)->toDateString(), 'penerbit_skdu' => 'Kelurahan Gerung Utara',
            ...collect(CabangTravel::DOKUMEN_PENDAFTARAN)->mapWithKeys(fn ($meta, $type) => [
                $meta['column'] => $this->sampleDocument("registrasi-cabang/{$type}/contoh-lobar.pdf"),
            ])->all(),
        ]);

        User::updateOrCreate(['email' => 'cabang.lobar@contoh.id'], [
            'nama' => 'Lalu Ahmad Fauzi',
            'nomor_hp' => '081339001199',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'cabang_id' => $cabang->id_cabang,
            'kabupaten' => 'Lombok Barat',
            'is_password_changed' => true,
        ]);
    }
}

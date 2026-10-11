<?php

namespace App\Models;

use App\Enums\TravelRegistrationStatus;
use App\Models\Concerns\HasRegistrationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CabangTravel extends Model
{
    use HasFactory;
    use HasRegistrationStatus;

    protected $table = 'travel_cabang';
    protected $primaryKey = 'id_cabang';

    /**
     * Berkas yang wajib diunggah cabang saat mendaftar mandiri, beserta isian
     * nomor dan tanggal dari berkas itu. Isian dipakai di berita acara cabang
     * dan dicocokkan petugas dengan berkasnya saat verifikasi.
     */
    public const DOKUMEN_PENDAFTARAN = [
        'oss' => ['column' => 'dokumen_oss', 'label' => 'OSS Cabang', 'fields' => [
            'nib' => ['label' => 'Nomor Induk Berusaha (NIB)', 'type' => 'text', 'rule' => 'string|max:30'],
            'tanggal_nib' => ['label' => 'Tanggal Terbit NIB', 'type' => 'date', 'rule' => 'date|before_or_equal:today'],
        ]],
        'akta' => ['column' => 'dokumen_akta', 'label' => 'Akta Notaris / Pembukaan Cabang', 'fields' => [
            'notaris_akta' => ['label' => 'Nama Notaris', 'type' => 'text', 'rule' => 'string|max:255'],
            'nomor_akta' => ['label' => 'Nomor Akta', 'type' => 'text', 'rule' => 'string|max:100'],
            'tanggal_akta' => ['label' => 'Tanggal Akta', 'type' => 'date', 'rule' => 'date|before_or_equal:today'],
        ]],
        'ktp_kepala' => ['column' => 'dokumen_ktp_kepala', 'label' => 'KTP Kepala Cabang', 'fields' => [
            'nik_kepala' => ['label' => 'NIK Kepala Cabang', 'type' => 'text', 'rule' => 'digits:16'],
        ]],
        'npwp_kepala' => ['column' => 'dokumen_npwp_kepala', 'label' => 'NPWP Kepala Cabang', 'fields' => [
            'npwp_kepala' => ['label' => 'Nomor NPWP Kepala Cabang', 'type' => 'text', 'rule' => 'string|max:25'],
        ]],
        'sk_du' => ['column' => 'dokumen_sk_du', 'label' => 'SK Domisili Usaha Kelurahan', 'fields' => [
            'nomor_skdu' => ['label' => 'Nomor SKDU', 'type' => 'text', 'rule' => 'string|max:100'],
            'tanggal_skdu' => ['label' => 'Tanggal SKDU', 'type' => 'date', 'rule' => 'date|before_or_equal:today'],
            'penerbit_skdu' => ['label' => 'Diterbitkan Oleh', 'type' => 'text', 'rule' => 'string|max:255'],
        ]],
    ];

    /** @return array<string, array{label: string, type: string, rule: string}> */
    public static function detailDokumen(): array
    {
        return array_merge(...array_values(array_column(self::DOKUMEN_PENDAFTARAN, 'fields')));
    }

    /** Aturan validasi isian berkas. Pendaftar wajib mengisi, petugas boleh menyusul. */
    public static function detailDokumenRules(bool $wajib): array
    {
        return array_map(fn ($meta) => ($wajib ? 'required|' : 'nullable|') . $meta['rule'], self::detailDokumen());
    }

    protected $fillable = [
        'travel_id',
        'Penyelenggara',
        'kabupaten',
        'pusat',
        'pimpinan_pusat',
        'alamat_pusat',
        'SK_BA',
        'tanggal',
        'pimpinan_cabang',
        'alamat_cabang',
        'telepon',
        'registration_status',
        'registration_notes',
        'revision_return_status',
        'dokumen_oss',
        'dokumen_akta',
        'dokumen_ktp_kepala',
        'dokumen_sk_du',
        'dokumen_npwp_kepala',
        'dokumen_sk_pusat',
        'nib',
        'tanggal_nib',
        'notaris_akta',
        'nomor_akta',
        'tanggal_akta',
        'nik_kepala',
        'npwp_kepala',
        'nomor_skdu',
        'tanggal_skdu',
        'penerbit_skdu',
        'dokumen_rekomendasi',
        'catatan_rekomendasi',
        'recommended_at',
        'recommended_by',
        'verified_at',
        'verified_by',
        'nomor_berita_acara',
        'ba_token',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_nib' => 'date',
        'tanggal_akta' => 'date',
        'tanggal_skdu' => 'date',
        'recommended_at' => 'datetime',
        'verified_at' => 'datetime',
        'registration_status' => TravelRegistrationStatus::class,
    ];

    public function travel()
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }

    public function recommendedByUser()
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'cabang_id', 'id_cabang');
    }

    /** Sudah direkomendasikan Kabupaten/Kota dan menunggu keputusan Kanwil. */
    public function scopeAwaitingKanwil(Builder $query): Builder
    {
        return $query->where('registration_status', TravelRegistrationStatus::MenungguKanwil);
    }

    /**
     * Cabang tidak berdiri sendiri: izinnya menempel pada travel pusat. Kalau
     * pusatnya dihapus atau tidak lagi disetujui, cabangnya tidak boleh terus
     * tampil sebagai travel resmi.
     *
     * Data lama yang belum terhubung ke pusat (travel_id kosong) tetap
     * ditampilkan, karena keabsahannya tidak bisa dinilai dari relasi ini.
     */
    public function scopeWithActiveParent(Builder $query): Builder
    {
        return $query->where(fn (Builder $scoped) => $scoped
            ->whereNull('travel_id')
            ->orWhereHas('travel', fn (Builder $travel) => $travel->approved()));
    }

    /**
     * Nomor berita acara terbit sekali, saat cabang disetujui, dengan bulan dan
     * tahun verifikasi. Cabang yang disetujui sebelum fitur ini (atau diinput
     * petugas) mendapat nomornya saat berita acaranya pertama kali dibuka.
     *
     * Nomor urut berjalan sepanjang tahun seperti nomor sertifikat; baris yang
     * dibaca dikunci supaya dua persetujuan bersamaan tidak bernomor sama.
     */
    public function pastikanNomorBeritaAcara(): string
    {
        if ($this->nomor_berita_acara) {
            if (! $this->ba_token) {
                $this->forceFill(['ba_token' => Str::random(32)])->save();
            }

            return $this->nomor_berita_acara;
        }

        return DB::transaction(function () {
            // Data lama disetujui tanpa catatan waktu. Tanggal terbit dibekukan
            // sekarang, supaya tanggal di berita acara tidak berganti tiap dibuka.
            $terbit = $this->verified_at ?? now();
            $tahun = $terbit->format('Y');

            $tertinggi = self::query()
                ->where('nomor_berita_acara', 'like', "B-%/Kw.18/%/{$tahun}")
                ->lockForUpdate()
                ->pluck('nomor_berita_acara')
                ->map(fn ($nomor) => preg_match('/^B-(\d+)\//', $nomor, $cocok) ? (int) $cocok[1] : 0)
                ->max();

            $this->forceFill([
                'nomor_berita_acara' => 'B-' . ((int) $tertinggi + 1) . "/Kw.18/{$terbit->format('m')}/{$tahun}",
                'verified_at' => $terbit,
                // Token QR tanda tangan; acak, jadi tidak bisa ditebak dari nomor.
                'ba_token' => Str::random(32),
            ])->save();

            return $this->nomor_berita_acara;
        });
    }

    public function documentPath(string $type): ?string
    {
        $column = $type === 'rekomendasi'
            ? 'dokumen_rekomendasi'
            : (self::DOKUMEN_PENDAFTARAN[$type]['column'] ?? null);

        return $column ? $this->{$column} : null;
    }

    public function hasRegistrationDocument(string $type): bool
    {
        $path = $this->documentPath($type);

        return $path !== null && Storage::disk('public')->exists($path);
    }

    /**
     * SK pusat dibaca dari travel pusat yang dipilih. Pusat yang tidak terdata
     * (misalnya berkantor di luar NTB) mengunggah SK-nya sendiri.
     */
    public function skPusatPath(): ?string
    {
        return $this->travel?->dokumen_sk ?: $this->dokumen_sk_pusat;
    }

    /**
     * Berkas yang tercatat di database harus benar benar ada di storage.
     *
     * Cabang hasil input petugas memang tidak punya berkas sama sekali, dan itu
     * wajar. Yang dicegah adalah menyetujui pendaftaran mandiri yang berkasnya
     * tercatat tetapi filenya sudah hilang.
     */
    public function missingRegistrationDocuments(): array
    {
        $hilang = [];

        foreach (self::DOKUMEN_PENDAFTARAN as $type => $meta) {
            if ($this->{$meta['column']} && ! $this->hasRegistrationDocument($type)) {
                $hilang[] = $meta['label'];
            }
        }

        if ($this->dokumen_sk_pusat && ! Storage::disk('public')->exists($this->dokumen_sk_pusat)) {
            $hilang[] = 'SK Izin PPIU Pusat';
        }

        return $hilang;
    }

    /** Hapus berkas unggahan cabang ini dari storage. */
    public function deleteRegistrationDocuments(): void
    {
        $kolom = array_column(self::DOKUMEN_PENDAFTARAN, 'column');
        $kolom[] = 'dokumen_rekomendasi';
        $kolom[] = 'dokumen_sk_pusat';

        foreach ($kolom as $column) {
            if ($this->{$column}) {
                Storage::disk('public')->delete($this->{$column});
            }
        }
    }
}

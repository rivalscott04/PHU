<?php

namespace App\Models;

use App\Enums\TravelRegistrationStatus;
use App\Models\Concerns\HasRegistrationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CabangTravel extends Model
{
    use HasFactory;
    use HasRegistrationStatus;

    protected $table = 'travel_cabang';
    protected $primaryKey = 'id_cabang';

    /** Berkas yang wajib diunggah cabang saat mendaftar mandiri. */
    public const DOKUMEN_PENDAFTARAN = [
        'oss' => ['column' => 'dokumen_oss', 'label' => 'OSS Cabang'],
        'akta' => ['column' => 'dokumen_akta', 'label' => 'Akta Notaris / Pembukaan Cabang'],
        'ktp_kepala' => ['column' => 'dokumen_ktp_kepala', 'label' => 'KTP Kepala Cabang'],
        'sk_du' => ['column' => 'dokumen_sk_du', 'label' => 'SK Domisili Usaha Kelurahan'],
    ];

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
        'dokumen_oss',
        'dokumen_akta',
        'dokumen_ktp_kepala',
        'dokumen_sk_du',
        'dokumen_rekomendasi',
        'catatan_rekomendasi',
        'recommended_at',
        'recommended_by',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
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

    /** SK pusat tidak diunggah ulang, dibaca dari travel pusat yang dipilih. */
    public function skPusatPath(): ?string
    {
        return $this->travel?->dokumen_sk;
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

        return $hilang;
    }

    /** Hapus berkas unggahan cabang ini dari storage. */
    public function deleteRegistrationDocuments(): void
    {
        $kolom = array_column(self::DOKUMEN_PENDAFTARAN, 'column');
        $kolom[] = 'dokumen_rekomendasi';

        foreach ($kolom as $column) {
            if ($this->{$column}) {
                Storage::disk('public')->delete($this->{$column});
            }
        }
    }
}

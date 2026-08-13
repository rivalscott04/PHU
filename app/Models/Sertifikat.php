<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Sertifikat extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sertifikat';

    protected $fillable = [
        'uuid',
        'travel_id',
        'cabang_id',
        'nama_ppiu',
        'nama_kepala',
        'alamat',
        'tanggal_diterbitkan',
        'tanggal_kadaluarsa',
        'reminder_kadaluarsa_at',
        'tanggal_tandatangan',
        'nomor_surat',
        'nomor_dokumen',
        'qrcode_path',
        'sertifikat_path',
        'pdf_path',
        'jenis',
        'jenis_lokasi',
        'status'
    ];

    protected $casts = [
        'tanggal_diterbitkan' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'reminder_kadaluarsa_at' => 'datetime',
        'tanggal_tandatangan' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }

            // Dihitung di model supaya tidak bisa terlupa dari jalur manapun,
            // termasuk seeder dan impor.
            if (! $model->tanggal_kadaluarsa && $model->tanggal_diterbitkan) {
                $model->tanggal_kadaluarsa = self::hitungKadaluarsa($model->tanggal_diterbitkan);
            }
        });
    }

    /**
     * Sertifikat berlaku sampai 1 Januari berikutnya, berapa pun bulan
     * terbitnya. Terbit Juni 2026 maupun Desember 2026 sama sama berakhir
     * 1 Januari 2027.
     */
    public static function hitungKadaluarsa(mixed $tanggalTerbit): Carbon
    {
        return Carbon::parse($tanggalTerbit)->startOfYear()->addYear();
    }

    public function isKadaluarsa(?Carbon $pada = null): bool
    {
        if (! $this->tanggal_kadaluarsa) {
            return false;
        }

        return $this->tanggal_kadaluarsa->startOfDay()->lte(($pada ?: now())->startOfDay());
    }

    /** Sisa hari sampai kedaluwarsa. Negatif berarti sudah lewat. */
    public function sisaHariBerlaku(?Carbon $pada = null): ?int
    {
        if (! $this->tanggal_kadaluarsa) {
            return null;
        }

        return ($pada ?: now())->startOfDay()->diffInDays($this->tanggal_kadaluarsa->startOfDay(), false);
    }

    /** @param  \Illuminate\Database\Eloquent\Builder<self>  $query */
    public function scopeKadaluarsaPada($query, Carbon $tanggal)
    {
        return $query->whereDate('tanggal_kadaluarsa', $tanggal->toDateString());
    }

    public function travel()
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }

    public function cabang()
    {
        return $this->belongsTo(CabangTravel::class, 'cabang_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getStatusText()
    {
        if ($this->status === 'revoked') {
            return 'Dicabut';
        }
        
        return 'Aktif';
    }

    public function getStatusColor()
    {
        if ($this->status === 'revoked') {
            return 'danger';
        }
        
        return 'success';
    }

    public function getVerificationUrl(bool $forQr = false): string
    {
        $path = route('sertifikat.verifikasi', $this->uuid, absolute: false);

        if ($forQr) {
            return \App\Helpers\StorageHelper::absoluteUrl($path);
        }

        return $path;
    }



    /**
     * Terbitkan nomor surat berikutnya. Dipanggil dari dalam transaksi, dan
     * mengunci baris yang dibaca supaya dua petugas yang menyimpan bersamaan
     * tidak mendapat nomor yang sama.
     *
     * Nomor urut berjalan sepanjang tahun. Bulan tetap dicantumkan sebagai
     * keterangan bulan terbit, tetapi tidak mengulang hitungan.
     */
    public static function terbitkanNomorSurat(Carbon $terbit): string
    {
        $tahun = $terbit->format('Y');
        $bulan = $terbit->format('m');

        $tertinggi = self::query()
            ->whereNotNull('nomor_surat')
            ->where('nomor_surat', 'like', "B-%/Kw.18.01/HJ.00/2/%/{$tahun}")
            ->withTrashed()->lockForUpdate()
            ->pluck('nomor_surat')
            ->map(fn ($nomor) => preg_match('/^B-(\d+)\//', (string) $nomor, $cocok) ? (int) $cocok[1] : 0)
            ->max();

        $berikutnya = ((int) $tertinggi) + 1;

        return "B-{$berikutnya}/Kw.18.01/HJ.00/2/{$bulan}/{$tahun}";
    }

    /** Nomor dokumen ikut urutan yang sama, tiga digit. */
    public static function terbitkanNomorDokumen(Carbon $terbit): string
    {
        $nomor = self::query()
            ->whereYear('tanggal_diterbitkan', $terbit->format('Y'))
            ->withTrashed()->lockForUpdate()
            ->count() + 1;

        return str_pad((string) $nomor, 3, '0', STR_PAD_LEFT);
    }

    public static function getNextNomorSurat($tahun = null, $bulan = null)
    {
        $tahun = $tahun ?: now()->format('Y');
        $bulan = $bulan ?: now()->format('m');

        // Diturunkan dari nomor tertinggi yang sudah terbit, bukan dari jumlah
        // baris. Menghitung baris membuat nomor terpakai ulang begitu ada
        // sertifikat yang dihapus, dan itu menabrak nomor yang sudah beredar.
        $tertinggi = Sertifikat::query()
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->whereNotNull('nomor_surat')
            ->pluck('nomor_surat')
            ->map(fn ($nomor) => preg_match('/^B-(\d+)\//', (string) $nomor, $cocok) ? (int) $cocok[1] : 0)
            ->max();

        return ((int) $tertinggi) + 1;
    }

} 
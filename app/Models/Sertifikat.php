<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikat';

    protected $fillable = [
        'uuid',
        'travel_id',
        'cabang_id',
        'nama_ppiu',
        'nama_kepala',
        'alamat',
        'tanggal_diterbitkan',
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
        'tanggal_tandatangan' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
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

    public function getVerificationUrl()
    {
        return route('sertifikat.verifikasi', $this->uuid);
    }

    public function generateNomorSurat()
    {
        $bulan = now()->format('m');
        $tahun = now()->format('Y');
        $nomorUrut = Sertifikat::whereYear('created_at', $tahun)
                               ->whereMonth('created_at', $bulan)
                               ->count() + 1;
        
        return $nomorUrut; // Return hanya angka urut, bukan format lengkap
    }

    public function generateNomorDokumen()
    {
        $tahun = now()->format('Y');
        $bulan = now()->format('m');
        $nomorUrut = Sertifikat::whereYear('created_at', $tahun)
                               ->whereMonth('created_at', $bulan)
                               ->count() + 1;
        
        return str_pad($nomorUrut, 3, '0', STR_PAD_LEFT); // Return 3 digit dengan leading zero
    }

    // Method untuk mendapatkan nomor surat berikutnya (untuk form)
    public static function getNextNomorSurat($tahun = null, $bulan = null)
    {
        $tahun = $tahun ?: now()->format('Y');
        $bulan = $bulan ?: now()->format('m');
        
        // Hitung berdasarkan tahun dan bulan untuk memastikan urutan yang benar
        $nomorUrut = Sertifikat::whereYear('created_at', $tahun)
                               ->whereMonth('created_at', $bulan)
                               ->count() + 1;
        
        return $nomorUrut;
    }

    // Method untuk mendapatkan nomor dokumen berikutnya (untuk form)
    public static function getNextNomorDokumen($tahun = null, $bulan = null)
    {
        $tahun = $tahun ?: now()->format('Y');
        $bulan = $bulan ?: now()->format('m');
        
        // Hitung berdasarkan tahun dan bulan untuk memastikan urutan yang benar
        $nomorUrut = Sertifikat::whereYear('created_at', $tahun)
                               ->whereMonth('created_at', $bulan)
                               ->count() + 1;
        
        return str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
    }
} 
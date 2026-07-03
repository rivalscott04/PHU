<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengaduan extends Model
{
    use HasFactory;
    protected $table = 'pengaduan';

    protected $fillable = [
        'public_token',
        'nama_pengadu',
        'travels_id',
        'hal_aduan',
        'berkas_aduan',
        'status',
        'pdf_output',
        'admin_notes',
        'completed_at',
        'processed_by'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->public_token) {
                $model->public_token = (string) Str::uuid();
            }
        });
    }

    public static function findByPublicToken(string $token): self
    {
        return static::query()->where('public_token', $token)->firstOrFail();
    }

    public function getPublicViewUrl(): string
    {
        return route('pengaduan.public', $this->public_token);
    }

    public function getPublicDownloadUrl(): string
    {
        return route('pengaduan.download-pdf.public', $this->public_token);
    }

    public function travel()  // Note: singular form, not 'travels'
    {
        return $this->belongsTo(TravelCompany::class, 'travels_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'badge bg-warning',
            'in_progress' => 'badge bg-info',
            'completed' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }
}

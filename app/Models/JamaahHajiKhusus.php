<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamaahHajiKhusus extends Model
{
    use HasFactory;

    protected $table = 'jamaah_haji_khusus';
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
    
    protected $fillable = [
        'travel_id',
        'nama_lengkap',
        'no_ktp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kota',
        'kecamatan',
        'provinsi',
        'kode_pos',
        'no_hp',
        'email',
        'nama_ayah',
        'pekerjaan',
        'pendidikan_terakhir',
        'status_pernikahan',
        'pergi_haji',
        'golongan_darah',
        'alergi',
        'no_paspor',
        'tanggal_berlaku_paspor',
        'tempat_terbit_paspor',
        'nomor_porsi',
        'tahun_pendaftaran',
        'status_pendaftaran',
        'catatan_khusus',
        'dokumen_ktp',
        'dokumen_kk',
        'dokumen_paspor',
        'dokumen_foto',
        'surat_keterangan',
        'bukti_setor_bank',
        'status_verifikasi_bukti',
        'catatan_verifikasi',
        'tanggal_verifikasi',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_berlaku_paspor' => 'date',
        'tahun_pendaftaran' => 'date',
    ];

    /**
     * Get the travel company that owns the jamaah
     */
    public function travel()
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }

    /**
     * Get the user who verified the bukti setor
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        switch ($this->status_pendaftaran) {
            case 'pending':
                return 'bg-warning';
            case 'approved':
                return 'bg-success';
            case 'rejected':
                return 'bg-danger';
            case 'completed':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        switch ($this->status_pendaftaran) {
            case 'pending':
                return 'Menunggu';
            case 'approved':
                return 'Disetujui';
            case 'rejected':
                return 'Ditolak';
            case 'completed':
                return 'Selesai';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get bukti setor verification status badge class
     */
    public function getBuktiSetorStatusBadgeClass()
    {
        switch ($this->status_verifikasi_bukti) {
            case 'pending':
                return 'bg-warning';
            case 'verified':
                return 'bg-success';
            case 'rejected':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get bukti setor verification status text
     */
    public function getBuktiSetorStatusText()
    {
        switch ($this->status_verifikasi_bukti) {
            case 'pending':
                return 'Menunggu Verifikasi';
            case 'verified':
                return 'Terverifikasi';
            case 'rejected':
                return 'Ditolak';
            default:
                return 'Unknown';
        }
    }

    /**
     * Check if bukti setor is verified
     */
    public function isBuktiSetorVerified()
    {
        return $this->status_verifikasi_bukti === 'verified';
    }

    /**
     * Check if can assign porsi number
     */
    public function canAssignPorsiNumber()
    {
        return $this->isBuktiSetorVerified() && empty($this->nomor_porsi);
    }

    /**
     * Get age
     */
    public function getAge()
    {
        return $this->tanggal_lahir->age;
    }

    /**
     * Get full address
     */
    public function getFullAddress()
    {
        return "{$this->alamat}, {$this->kota}, {$this->provinsi} {$this->kode_pos}";
    }

    /**
     * Check if documents are complete
     */
    public function isDocumentsComplete()
    {
        return !empty($this->dokumen_ktp) && 
               !empty($this->dokumen_kk) && 
               !empty($this->dokumen_paspor) && 
               !empty($this->dokumen_foto) && 
               !empty($this->dokumen_medical_check);
    }

    /**
     * Get document completion percentage
     */
    public function getDocumentCompletionPercentage()
    {
        $documents = [
            'dokumen_ktp',
            'dokumen_kk', 
            'dokumen_paspor',
            'dokumen_foto',
            'dokumen_medical_check'
        ];

        $completed = 0;
        foreach ($documents as $document) {
            if (!empty($this->$document)) {
                $completed++;
            }
        }

        return round(($completed / count($documents)) * 100);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pendaftaran', $status);
    }

    /**
     * Scope for filtering by travel company
     */
    public function scopeByTravel($query, $travelId)
    {
        return $query->where('travel_id', $travelId);
    }

    /**
     * Scope for searching
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('no_ktp', 'like', "%{$search}%")
              ->orWhere('no_paspor', 'like', "%{$search}%")
              ->orWhere('nomor_porsi', 'like', "%{$search}%");
        });
    }
}

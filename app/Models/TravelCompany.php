<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelCompany extends Model
{
    use HasFactory;
    protected $table = 'travels';
    protected $fillable = [
        'Penyelenggara',
        'Pusat',
        'Tanggal',
        'nilai_akreditasi',
        'tanggal_akreditasi',
        'lembaga_akreditasi',
        'Pimpinan',
        'alamat_kantor_lama',
        'alamat_kantor_baru',
        'Telepon',
        'Status',
        'kab_kota',
        'capabilities',
        'can_haji',
        'can_umrah',
        'description',
        'license_number',
        'license_expiry'
    ];

    protected $casts = [
        'Tanggal' => 'date',
        'tanggal_akreditasi' => 'date',
        'license_expiry' => 'date',
        'capabilities' => 'array',
        'can_haji' => 'boolean',
        'can_umrah' => 'boolean',
    ];

    public function model(array $row)
    {
        return new self([
            'No' => $row['No'],
            'Penyelenggara' => $row['Penyelenggara'],
            'Pusat' => $row['Pusat'],
            'Tanggal' => $row['Tanggal'],
            'nilai_akreditasi' => $row['nilai_akreditasi'],
            'tanggal_akreditasi' => $row['tanggal_akreditasi'],
            'lembaga_akreditasi' => $row['lembaga_akreditasi'],
            'Pimpinan' => $row['Pimpinan'],
            'alamat_kantor_lama' => $row['alamat_kantor_lama'],
            'alamat_kantor_baru' => $row['alamat_kantor_baru'],
            'Telepon' => $row['Telepon'],
            'Status' => $row['Status'],
            'kab_kota' => $row['kab_kota'],
        ]);
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'travels_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'travel_id');
    }

    public function jamaahHajiKhusus()
    {
        return $this->hasMany(JamaahHajiKhusus::class, 'travel_id');
    }

    /**
     * Check if travel can handle haji
     */
    public function canHandleHaji()
    {
        return $this->can_haji || $this->Status === 'PIHK';
    }

    /**
     * Check if travel can handle umrah
     */
    public function canHandleUmrah()
    {
        return $this->can_umrah || $this->Status === 'PPIU' || $this->Status === 'PIHK';
    }

    /**
     * Check if travel can handle haji khusus
     */
    public function canHandleHajiKhusus()
    {
        return $this->Status === 'PIHK';
    }

    /**
     * Get travel type description
     */
    public function getTravelTypeDescription()
    {
        if ($this->Status === 'PIHK') {
            return 'PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)';
        } elseif ($this->Status === 'PPIU') {
            return 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)';
        }
        return 'Unknown Type';
    }

    /**
     * Get available services for this travel
     */
    public function getAvailableServices()
    {
        $services = [];
        
        if ($this->canHandleHaji()) {
            $services[] = 'Haji';
        }
        
        if ($this->canHandleUmrah()) {
            $services[] = 'Umrah';
        }

        if ($this->canHandleHajiKhusus()) {
            $services[] = 'Haji Khusus';
        }
        
        return $services;
    }

    /**
     * Check if license is expired
     */
    public function isLicenseExpired()
    {
        if (!$this->license_expiry) {
            return false;
        }
        
        return $this->license_expiry->isPast();
    }

    /**
     * Get license status
     */
    public function getLicenseStatus()
    {
        if (!$this->license_expiry) {
            return 'No License';
        }
        
        if ($this->isLicenseExpired()) {
            return 'Expired';
        }
        
        return 'Active';
    }

    /**
     * Set default capabilities based on status
     */
    public function setDefaultCapabilities()
    {
        if ($this->Status === 'PIHK') {
            $this->can_haji = true;
            $this->can_umrah = true;
            $this->capabilities = ['haji', 'umrah', 'haji_khusus'];
        } elseif ($this->Status === 'PPIU') {
            $this->can_haji = false;
            $this->can_umrah = true;
            $this->capabilities = ['umrah'];
        }
    }
}

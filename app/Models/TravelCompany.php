<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TravelCompany extends Model implements ToModel, WithHeadingRow
{
    protected $table = 'travel';
    protected $fillable = [
        'penyelenggara',
        'nomor_sk',
        'tanggal_sk',
        'akreditasi',
        'tanggal_akreditasi',
        'lembaga_akreditasi',
        'pimpinan',
        'alamat_kantor_lama',
        'alamat_kantor_baru',
        'telepon',
        'status',
        'kab_kota'
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_akreditasi' => 'date',
    ];

    public function model(array $row)
    {
        return new self([
            'penyelenggara' => $row['penyelenggara'],
            'nomor_sk' => $row['nomor_sk'],
            'tanggal_sk' => $row['tanggal_sk'],
        ]);
    }
}

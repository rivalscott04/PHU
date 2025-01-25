<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TravelCompany extends Model implements ToModel, WithHeadingRow
{
    protected $table = 'travels';
    protected $fillable = [
        'No',
        'Penyelenggara',
        'Pusat',
        'Tanggal',
        'Jml_Akreditasi',
        'tanggal_akreditasi',
        'lembaga_akreditasi',
        'Pimpinan',
        'alamat_kantor_lama',
        'alamat_kantor_baru',
        'Telepon',
        'Status',
        'kab_kota'
    ];

    protected $casts = [
        'Tanggal' => 'date',
        'tanggal_akreditasi' => 'date',
    ];

    public function model(array $row)
    {
        return new self([
            'No' => $row['No'],
            'Penyelenggara' => $row['Penyelenggara'],
            'Pusat' => $row['Pusat'],
            'Tanggal' => $row['Tanggal'],
            'Jml_Akreditasi' => $row['Jml_Akreditasi'],
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
}

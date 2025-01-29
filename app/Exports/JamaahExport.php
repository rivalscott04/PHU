<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class JamaahExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return new Collection([
            [
                'nik' => '',
                'nama' => '',
                'alamat' => '',
                'nomor_hp' => ''
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nik',
            'nama',
            'alamat',
            'nomor_hp'
        ];
    }
}

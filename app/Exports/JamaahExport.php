<?php

namespace App\Exports;

use App\Models\Jamaah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JamaahExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Jamaah::all();
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
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class JamaahExport implements FromCollection, WithHeadings, WithStyles
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

    public function styles(Worksheet $sheet)
    {
        // Format seluruh kolom A dan D sebagai text
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('@');

        // Set kolom sebagai text sebelum data dimasukkan
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
    }
}

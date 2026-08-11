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

    /**
     * Judul kolom dibaca manusia, tetapi tetap harus ter-slug menjadi
     * nik / nama / alamat / nomor_hp seperti yang dibaca JamaahImport.
     */
    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Alamat',
            'Nomor HP',
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

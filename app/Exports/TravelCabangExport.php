<?php

namespace App\Exports;

use App\Exports\Concerns\ExportsTravelSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TravelCabangExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting, WithCustomValueBinder
{
    use ExportsTravelSheet;

    public function __construct(private readonly Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Penyelenggara',
            'Kabupaten',
            'No. SK Pusat',
            'Pimpinan Pusat',
            'Alamat Pusat',
            'No. SK / BA',
            'Tanggal',
            'Pimpinan Cabang',
            'Alamat Cabang',
            'Telepon',
            'Status Registrasi',
        ];
    }

    public function map($cabang): array
    {
        return [
            $this->nextRowNumber(),
            $this->teks($cabang->Penyelenggara),
            $this->teks($cabang->kabupaten),
            $this->teks($cabang->pusat),
            $this->teks($cabang->pimpinan_pusat),
            $this->teks($cabang->alamat_pusat),
            $this->teks($cabang->SK_BA),
            $this->tanggal($cabang->tanggal),
            $this->teks($cabang->pimpinan_cabang),
            $this->teks($cabang->alamat_cabang),
            $this->teks($cabang->telepon),
            $cabang->registration_status?->label() ?? '-',
        ];
    }

    /** @return list<string> */
    protected function textColumns(): array
    {
        return ['D', 'G', 'K'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 18,
            'D' => 24,
            'E' => 22,
            'F' => 32,
            'G' => 24,
            'H' => 14,
            'I' => 22,
            'J' => 32,
            'K' => 18,
            'L' => 22,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

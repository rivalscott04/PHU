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

class TravelPusatExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting, WithCustomValueBinder
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
            'No. SK / NIB',
            'Tanggal SK',
            'Nilai Akreditasi',
            'Tanggal Akreditasi',
            'Lembaga Akreditasi',
            'Masa Berlaku',
            'Pimpinan',
            'Alamat Kantor Lama',
            'Alamat Kantor Baru',
            'Telepon',
            'Jenis Izin',
            'Kab/Kota',
            'Status Registrasi',
        ];
    }

    public function map($travel): array
    {
        return [
            $this->nextRowNumber(),
            $this->teks($travel->Penyelenggara),
            $this->teks($travel->Pusat),
            $this->tanggal($travel->Tanggal),
            $this->teks($travel->nilai_akreditasi),
            $this->tanggal($travel->tanggal_akreditasi),
            $this->teks($travel->lembaga_akreditasi),
            $this->tanggal($travel->license_expiry),
            $this->teks($travel->Pimpinan),
            $this->teks($travel->alamat_kantor_lama),
            $this->teks($travel->alamat_kantor_baru),
            $this->teks($travel->Telepon),
            $this->teks($travel->Status),
            $this->teks($travel->kab_kota),
            $travel->registration_status?->label() ?? '-',
        ];
    }

    /** @return list<string> */
    protected function textColumns(): array
    {
        return ['C', 'L'];
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
            'C' => 24,
            'D' => 14,
            'E' => 18,
            'F' => 18,
            'G' => 28,
            'H' => 16,
            'I' => 22,
            'J' => 32,
            'K' => 32,
            'L' => 18,
            'M' => 14,
            'N' => 18,
            'O' => 22,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

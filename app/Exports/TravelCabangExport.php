<?php

namespace App\Exports;

use App\Models\CabangTravel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TravelCabangExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function collection()
    {
        if ($this->user && $this->user->role === 'kabupaten') {
            // Kabupaten users can only see cabang travel in their area
            return CabangTravel::where('kabupaten', $this->user->kabupaten)->get();
        } else {
            // Admin can see all cabang travel
            return CabangTravel::all();
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Penyelenggara',
            'Kabupaten',
            'Pusat',
            'Pimpinan Pusat',
            'Alamat Pusat',
            'SK/BA',
            'Tanggal',
            'Pimpinan Cabang',
            'Alamat Cabang',
            'Telepon'
        ];
    }

    public function map($cabang): array
    {
        return [
            "'" . $cabang->id_cabang, // Add single quote to force text format
            $cabang->Penyelenggara,
            $cabang->kabupaten,
            $cabang->pusat,
            $cabang->pimpinan_pusat,
            $cabang->alamat_pusat,
            $cabang->SK_BA,
            $cabang->tanggal ? $cabang->tanggal->format('d/m/Y') : '',
            $cabang->pimpinan_cabang,
            $cabang->alamat_cabang,
            "'" . $cabang->telepon // Add single quote to force text format
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Penyelenggara
            'C' => 15,  // Kabupaten
            'D' => 15,  // Pusat
            'E' => 20,  // Pimpinan Pusat
            'F' => 30,  // Alamat Pusat
            'G' => 15,  // SK/BA
            'H' => 12,  // Tanggal
            'I' => 20,  // Pimpinan Cabang
            'J' => 30,  // Alamat Cabang
            'K' => 15,  // Telepon
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // No - ID Cabang
            'K' => NumberFormat::FORMAT_TEXT, // Telepon
        ];
    }
}


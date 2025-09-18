<?php

namespace App\Exports;

use App\Models\TravelCompany;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TravelPusatExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function collection()
    {
        if ($this->user && $this->user->role === 'kabupaten') {
            // Kabupaten users can only see travel companies in their area
            return TravelCompany::where('kab_kota', $this->user->kabupaten)->get();
        } else {
            // Admin can see all travel companies
            return TravelCompany::all();
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Penyelenggara',
            'Pusat',
            'Tanggal',
            'Nilai Akreditasi',
            'Tanggal Akreditasi',
            'Lembaga Akreditasi',
            'Pimpinan',
            'Alamat Kantor Lama',
            'Alamat Kantor Baru',
            'Telepon',
            'Status',
            'Kab/Kota',
            'Capabilities',
            'Can Haji',
            'Can Umrah',
            'Description',
            'License Number',
            'License Expiry'
        ];
    }

    public function map($travel): array
    {
        return [
            "'" . $travel->id, // Add single quote to force text format
            $travel->Penyelenggara,
            $travel->Pusat,
            $travel->Tanggal ? $travel->Tanggal->format('d/m/Y') : '',
            $travel->nilai_akreditasi,
            $travel->tanggal_akreditasi ? $travel->tanggal_akreditasi->format('d/m/Y') : '',
            $travel->lembaga_akreditasi,
            $travel->Pimpinan,
            $travel->alamat_kantor_lama,
            $travel->alamat_kantor_baru,
            "'" . $travel->Telepon, // Add single quote to force text format
            $travel->Status,
            $travel->kab_kota,
            $travel->capabilities ? implode(', ', $travel->capabilities) : '',
            $travel->can_haji ? 'Ya' : 'Tidak',
            $travel->can_umrah ? 'Ya' : 'Tidak',
            $travel->description,
            "'" . $travel->license_number, // Add single quote to force text format
            $travel->license_expiry ? $travel->license_expiry->format('d/m/Y') : ''
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
            'C' => 15,  // Pusat
            'D' => 12,  // Tanggal
            'E' => 15,  // Nilai Akreditasi
            'F' => 15,  // Tanggal Akreditasi
            'G' => 20,  // Lembaga Akreditasi
            'H' => 20,  // Pimpinan
            'I' => 30,  // Alamat Kantor Lama
            'J' => 30,  // Alamat Kantor Baru
            'K' => 15,  // Telepon
            'L' => 10,  // Status
            'M' => 15,  // Kab/Kota
            'N' => 20,  // Capabilities
            'O' => 10,  // Can Haji
            'P' => 10,  // Can Umrah
            'Q' => 30,  // Description
            'R' => 20,  // License Number
            'S' => 15,  // License Expiry
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // No - ID
            'K' => NumberFormat::FORMAT_TEXT, // Telepon
            'R' => NumberFormat::FORMAT_TEXT, // License Number
        ];
    }
}


<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class JamaahHajiKhususExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $data;
    protected $isGlobal;

    public function __construct($data, $isGlobal = false)
    {
        $this->data = $data;
        $this->isGlobal = $isGlobal;
    }

    public function collection()
    {
        if ($this->isGlobal) {
            // For global export, we need to flatten the grouped data
            $flattenedData = collect();
            
            foreach ($this->data as $travelId => $jamaahGroup) {
                $travel = $jamaahGroup->first()->travel;
                
                // Add separator row for travel
                $flattenedData->push([
                    'separator' => true,
                    'travel_name' => $travel->Penyelenggara ?? 'PPIU Tidak Diketahui',
                    'kabupaten' => $travel->kab_kota ?? 'Kabupaten Tidak Diketahui',
                    'total_jamaah' => $jamaahGroup->count(),
                    'status' => $travel->Status ?? 'N/A'
                ]);
                
                // Add jamaah data
                foreach ($jamaahGroup as $jamaah) {
                    $flattenedData->push([
                        'separator' => false,
                        'jamaah' => $jamaah
                    ]);
                }
                
                // Add empty row after each travel
                $flattenedData->push([
                    'separator' => false,
                    'empty' => true
                ]);
            }
            
            return $flattenedData;
        } else {
            // For travel-specific export, return the collection directly
            return $this->data;
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'No. KTP',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Golongan Darah',
            'Status Pernikahan',
            'Alamat',
            'Kota',
            'Provinsi',
            'Kode Pos',
            'No. HP',
            'Email',
            'Nama Ayah',
            'Pekerjaan',
            'Pendidikan Terakhir',
            'Pergi Haji',
            'Alergi',
            'Catatan Khusus',
            'No. Paspor',
            'Tanggal Berlaku Paspor',
            'No. SPPH',
            'Tahun Pendaftaran',
            'Status Bukti Setor',
            'PPIU',
            'Kabupaten/Kota',
            'Status PPIU',
            'Status Pendaftaran',
            'Tanggal Daftar'
        ];
    }

    public function map($row): array
    {
        if ($this->isGlobal) {
            if (isset($row['separator']) && $row['separator']) {
                // Separator row for travel
                return [
                    'PPIU: ' . $row['travel_name'],
                    'Kabupaten: ' . $row['kabupaten'],
                    'Total Jamaah: ' . $row['total_jamaah'],
                    'Status: ' . $row['status'],
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
                ];
            } else if (isset($row['empty']) && $row['empty']) {
                // Empty row
                return array_fill(0, 29, '');
            } else {
                // Jamaah data
                $jamaah = $row['jamaah'];
                return $this->mapJamaahData($jamaah);
            }
        } else {
            // For travel-specific export
            return $this->mapJamaahData($row);
        }
    }

    private function mapJamaahData($jamaah): array
    {
        return [
            '', // No will be handled by Excel
            $jamaah->nama_lengkap,
            $jamaah->no_ktp,
            $jamaah->tempat_lahir,
            $jamaah->tanggal_lahir ? $jamaah->tanggal_lahir->format('d/m/Y') : '',
            $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $jamaah->golongan_darah,
            $jamaah->status_pernikahan,
            $jamaah->alamat,
            $jamaah->kota,
            $jamaah->provinsi,
            $jamaah->kode_pos,
            $jamaah->no_hp,
            $jamaah->email,
            $jamaah->nama_ayah,
            $jamaah->pekerjaan,
            $jamaah->pendidikan_terakhir,
            $jamaah->pergi_haji,
            $jamaah->alergi,
            $jamaah->catatan_khusus,
            $jamaah->no_paspor,
            $jamaah->tanggal_berlaku_paspor ? $jamaah->tanggal_berlaku_paspor->format('d/m/Y') : '',
            $jamaah->nomor_porsi,
            $jamaah->tahun_pendaftaran,
            $jamaah->getBuktiSetorStatusText(),
            $jamaah->travel->Penyelenggara ?? 'Tidak Diketahui',
            $jamaah->travel->kab_kota ?? 'Tidak Diketahui',
            $jamaah->travel->Status ?? 'N/A',
            $jamaah->getStatusText(),
            $jamaah->created_at ? $jamaah->created_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        // Header style
        $sheet->getStyle('A1:AC1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '34C38F'] // Green for Haji Khusus
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Apply styles to all data rows
        for ($row = 2; $row <= $highestRow; $row++) {
            $sheet->getStyle("A{$row}:AC{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }

        // Style separator rows (travel headers)
        if ($this->isGlobal) {
            $currentRow = 2;
            foreach ($this->data as $travelId => $jamaahGroup) {
                // Style separator row
                $sheet->getStyle("A{$currentRow}:AC{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '556EE6'] // Blue for separator
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                $currentRow += $jamaahGroup->count() + 2; // +2 for separator and empty row
            }
        }

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Nama Lengkap
            'C' => 20,  // No. KTP
            'D' => 15,  // Tempat Lahir
            'E' => 15,  // Tanggal Lahir
            'F' => 12,  // Jenis Kelamin
            'G' => 12,  // Golongan Darah
            'H' => 15,  // Status Pernikahan
            'I' => 30,  // Alamat
            'J' => 15,  // Kota
            'K' => 15,  // Provinsi
            'L' => 10,  // Kode Pos
            'M' => 15,  // No. HP
            'N' => 25,  // Email
            'O' => 20,  // Nama Ayah
            'P' => 15,  // Pekerjaan
            'Q' => 20,  // Pendidikan Terakhir
            'R' => 12,  // Pergi Haji
            'S' => 15,  // Alergi
            'T' => 25,  // Catatan Khusus
            'U' => 15,  // No. Paspor
            'V' => 20,  // Tanggal Berlaku Paspor
            'W' => 15,  // No. SPPH
            'X' => 15,  // Tahun Pendaftaran
            'Y' => 20,  // Status Bukti Setor
            'Z' => 25,  // PPIU
            'AA' => 20, // Kabupaten/Kota
            'AB' => 15, // Status PPIU
            'AC' => 20, // Status Pendaftaran
            'AD' => 20, // Tanggal Daftar
        ];
    }
}

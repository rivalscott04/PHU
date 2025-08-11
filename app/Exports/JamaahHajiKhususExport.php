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
            // For global export, flatten the grouped data with separators
            $exportData = collect();
            
            foreach ($this->data as $travelId => $jamaahGroup) {
                if ($jamaahGroup->isEmpty()) {
                    continue;
                }
                
                $travel = $jamaahGroup->first()->travel;
                
                // Add separator row
                $exportData->push([
                    'SEPARATOR' => 'PPIU: ' . ($travel->Penyelenggara ?? 'Tidak Diketahui'),
                    'KABUPATEN' => $travel->kab_kota ?? 'Tidak Diketahui',
                    'TOTAL_JAMAAH' => $jamaahGroup->count(),
                    'STATUS' => $travel->Status ?? 'N/A',
                    'NAMA' => '',
                    'NIK' => '',
                    'ALAMAT' => '',
                    'NO_HP' => '',
                    'STATUS_PENDAFTARAN' => '',
                    'NOMOR_PORSI' => '',
                ]);
                
                // Add jamaah data
                foreach ($jamaahGroup as $jamaah) {
                    $exportData->push([
                        'SEPARATOR' => '',
                        'KABUPATEN' => '',
                        'TOTAL_JAMAAH' => '',
                        'STATUS' => '',
                        'NAMA' => $jamaah->nama_lengkap ?? '',
                        'NIK' => $jamaah->no_ktp ?? '',
                        'ALAMAT' => $jamaah->alamat ?? '',
                        'NO_HP' => $jamaah->no_hp ?? '',
                        'STATUS_PENDAFTARAN' => $jamaah->getStatusText(),
                        'NOMOR_PORSI' => $jamaah->nomor_porsi ?? '',
                    ]);
                }
                
                // Add empty row after each travel
                $exportData->push([
                    'SEPARATOR' => '',
                    'KABUPATEN' => '',
                    'TOTAL_JAMAAH' => '',
                    'STATUS' => '',
                    'NAMA' => '',
                    'NIK' => '',
                    'ALAMAT' => '',
                    'NO_HP' => '',
                    'STATUS_PENDAFTARAN' => '',
                    'NOMOR_PORSI' => '',
                ]);
            }
            
            return $exportData;
        } else {
            // For travel-specific export, return simple collection
            return $this->data->map(function ($jamaah) {
                return [
                    'NAMA' => $jamaah->nama_lengkap ?? '',
                    'NIK' => $jamaah->no_ktp ?? '',
                    'ALAMAT' => $jamaah->alamat ?? '',
                    'NO_HP' => $jamaah->no_hp ?? '',
                    'STATUS_PENDAFTARAN' => $jamaah->getStatusText(),
                    'NOMOR_PORSI' => $jamaah->nomor_porsi ?? '',
                ];
            });
        }
    }

    public function headings(): array
    {
        if ($this->isGlobal) {
            return [
                'PPIU',
                'Kabupaten',
                'Total Jamaah',
                'Status',
                'Nama Jamaah',
                'NIK',
                'Alamat',
                'No HP',
                'Status Pendaftaran',
                'Nomor Porsi'
            ];
        } else {
            return [
                'Nama Jamaah',
                'NIK',
                'Alamat',
                'No HP',
                'Status Pendaftaran',
                'Nomor Porsi'
            ];
        }
    }

    public function map($row): array
    {
        if ($this->isGlobal) {
            return [
                $row['SEPARATOR'],
                $row['KABUPATEN'],
                $row['TOTAL_JAMAAH'],
                $row['STATUS'],
                $row['NAMA'],
                $row['NIK'],
                $row['ALAMAT'],
                $row['NO_HP'],
                $row['STATUS_PENDAFTARAN'],
                $row['NOMOR_PORSI'],
            ];
        } else {
            return [
                $row['NAMA'],
                $row['NIK'],
                $row['ALAMAT'],
                $row['NO_HP'],
                $row['STATUS_PENDAFTARAN'],
                $row['NOMOR_PORSI'],
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        // Style for headers
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '34C38F'], // Green for Haji Khusus
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style for separator rows (PPIU headers)
        for ($row = 2; $row <= $highestRow; $row++) {
            $separatorValue = $sheet->getCell('A' . $row)->getValue();
            if (strpos($separatorValue, 'PPIU:') === 0) {
                $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '556EE6'], // Blue for separator
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        }

        // Border for all cells
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        return $sheet;
    }

    public function columnWidths(): array
    {
        if ($this->isGlobal) {
            return [
                'A' => 25, // PPIU
                'B' => 15, // Kabupaten
                'C' => 12, // Total Jamaah
                'D' => 10, // Status
                'E' => 25, // Nama Jamaah
                'F' => 20, // NIK
                'G' => 40, // Alamat
                'H' => 15, // No HP
                'I' => 20, // Status Pendaftaran
                'J' => 15, // Nomor Porsi
            ];
        } else {
            return [
                'A' => 25, // Nama Jamaah
                'B' => 20, // NIK
                'C' => 40, // Alamat
                'D' => 15, // No HP
                'E' => 20, // Status Pendaftaran
                'F' => 15, // Nomor Porsi
            ];
        }
    }
}

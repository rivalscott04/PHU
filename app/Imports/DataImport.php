<?php

namespace App\Imports;

use App\Models\TravelCompany;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            return new TravelCompany([
                'No' => $row['no'],
                'Penyelenggara' => $row['penyelenggara'],
                'Pusat' => $row['pusat'],
                'Tanggal' => $this->parseDate($row['tanggal']),
                'Jml_Akreditasi' => $row['jml_akreditasi'],
                'tanggal_akreditasi' => $this->parseDate($row['tanggal_akreditasi']),
                'lembaga_akreditasi' => $row['lembaga_akreditasi'],
                'Pimpinan' => $row['pimpinan'],
                'alamat_kantor_lama' => $row['alamat_kantor_lama'],
                'alamat_kantor_baru' => $row['alamat_kantor_baru'],
                'Telepon' => $row['telepon'],
                'Status' => $row['status'],
                'kab_kota' => $row['kab_kota'],
            ]);
        } catch (\Exception $e) {
            error_log("Error importing row: " . json_encode($row) . ". Error: " . $e->getMessage());
            return null;
        }
    }


    private function parseDate($date)
    {
        $months = [
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December'
        ];

        foreach ($months as $indonesian => $english) {
            if (strpos($date, $indonesian) !== false) {
                $date = str_replace($indonesian, $english, $date);
                break;
            }
        }

        try {
            return \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            // Log the error or print it for debugging
            error_log("Failed to parse date: " . $e->getMessage());

            try {
                return \Carbon\Carbon::createFromFormat('d F Y', $date);
            } catch (\Exception $e) {
                error_log("Failed to create date from format: " . $e->getMessage());
                return null; // or handle it according to your needs
            }
        }
    }
}

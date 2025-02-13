<?php

namespace App\Imports;

use App\Models\CabangTravel;
use App\Models\Travel;
use App\Models\TravelCompany;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CabangTravelImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new CabangTravel([
            'Penyelenggara' => $row['penyelenggara'],
            'kabupaten' => $row['kabupaten'],
            'pusat' => $row['pusat'],
            'pimpinan_pusat' => $row['pimpinan_pusat'],
            'alamat_pusat' => $row['alamat_pusat'],
            'SK_BA' => $row['sk_ba'],
            'tanggal' => $this->parseDate($row['tanggal']),
            'pimpinan_cabang' => $row['pimpinan_cabang'],
            'alamat_cabang' => $row['alamat_cabang'],
            'telepon' => $row['telepon'],
        ]);
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

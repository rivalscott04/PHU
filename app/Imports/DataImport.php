<?php

namespace App\Imports;

use App\Models\TravelCompany;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class DataImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            // Tentukan unique identifier untuk mencari data yang sama
            $uniqueColumns = [
                'Penyelenggara' => $row['penyelenggara'],
                'Pusat' => $row['pusat'],
                // Tambahkan kolom lain jika diperlukan untuk identifikasi unik
            ];

            // Data yang akan diupdate/insert
            $data = [
                'Penyelenggara' => $row['penyelenggara'],
                'Pusat' => $row['pusat'],
                'Tanggal' => $this->parseDate($row['tanggal']),
                'nilai_akreditasi' => $row['nilai_akreditasi'],
                'tanggal_akreditasi' => $this->parseDate($row['tanggal_akreditasi']),
                'lembaga_akreditasi' => $row['lembaga_akreditasi'],
                'Pimpinan' => $row['pimpinan'],
                'alamat_kantor_lama' => $row['alamat_kantor_lama'],
                'alamat_kantor_baru' => $row['alamat_kantor_baru'],
                'Telepon' => $row['telepon'],
                'Status' => $row['status'],
                'kab_kota' => $row['kab_kota'],
                'updated_at' => now()
            ];

            // Coba update dulu, kalau tidak ada baru insert
            $updated = TravelCompany::where($uniqueColumns)
                ->update($data);

            // Jika tidak ada yang diupdate (data belum ada), maka insert baru
            if (!$updated) {
                $data['created_at'] = now();
                return new TravelCompany($data);
            }

            return null; // Return null karena data sudah diupdate

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
            error_log("Failed to parse date: " . $e->getMessage());

            try {
                return \Carbon\Carbon::createFromFormat('d F Y', $date);
            } catch (\Exception $e) {
                error_log("Failed to create date from format: " . $e->getMessage());
                return null;
            }
        }
    }
}

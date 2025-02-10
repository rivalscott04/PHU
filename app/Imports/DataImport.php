<?php

namespace App\Imports;

use App\Models\TravelCompany;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        \Log::info("Importing row: ", $row);
        try {
            // Tentukan unique identifier untuk mencari data yang sama
            $uniqueColumns = [
                'Penyelenggara' => $row['penyelenggara'],
                'Pusat' => $row['pusat'],
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

            // Cari record yang existing
            $existing = TravelCompany::where($uniqueColumns)->first();

            if ($existing) {
                // Update record yang ada
                $existing->update($data);
                return null; // Return null karena sudah di-update
            } else {
                // Buat record baru
                $data['created_at'] = now();
                return new TravelCompany($data);
            }
        } catch (\Exception $e) {
            \Log::error("Error importing row: " . json_encode($row) . ". Error: " . $e->getMessage());
            throw $e; // Re-throw exception agar bisa ditangkap di controller
        }
    }

    private function parseDate($date)
    {
        if (!$date) return null;

        // Jika input adalah angka (Excel Serial Number)
        if (is_numeric($date)) {
            try {
                // Konversi Excel Serial Number ke tanggal PHP
                // Excel menggunakan 1 Januari 1900 sebagai hari ke-1
                $unix_date = ($date - 25569) * 86400;
                return Carbon::createFromTimestamp($unix_date);
            } catch (\Exception $e) {
                \Log::error("Failed to parse Excel serial date: {$date} - " . $e->getMessage());
                return null;
            }
        }

        // Untuk format DD/MM/YYYY
        if (strpos($date, '/') !== false) {
            try {
                return Carbon::createFromFormat('d/m/Y', $date);
            } catch (\Exception $e) {
                \Log::error("Failed to parse date with slash: {$date} - " . $e->getMessage());
            }
        }

        // Coba format lainnya jika diperlukan
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            \Log::error("Failed to parse date: {$date} - " . $e->getMessage());
            return null;
        }
    }
}

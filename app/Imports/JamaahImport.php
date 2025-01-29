<?php

namespace App\Imports;

use App\Models\Jamaah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class JamaahImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        // Convert keys to lowercase
        $row = array_change_key_case($row, CASE_LOWER);

        return new Jamaah([
            'nik' => strval($row['nik']), // Convert to string to handle Excel number formatting
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'nomor_hp' => strval($row['nomor_hp']), // Convert to string to handle Excel number formatting
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nik' => 'required|numeric|digits:16|unique:jamaah,nik',
            '*.nama' => 'required|string|max:255',
            '*.alamat' => 'required|string',
            '*.nomor_hp' => 'required|numeric|digits_between:10,13',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nik.required' => 'NIK wajib diisi',
            '*.nik.numeric' => 'NIK harus berupa angka',
            '*.nik.digits' => 'NIK harus 16 digit',
            '*.nik.unique' => 'NIK sudah terdaftar',
            '*.nama.required' => 'Nama wajib diisi',
            '*.alamat.required' => 'Alamat wajib diisi',
            '*.nomor_hp.required' => 'Nomor HP wajib diisi',
            '*.nomor_hp.numeric' => 'Nomor HP harus berupa angka',
            '*.nomor_hp.digits_between' => 'Nomor HP harus 10-13 digit',
        ];
    }
}

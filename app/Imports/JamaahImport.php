<?php

namespace App\Imports;

use App\Models\Jamaah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JamaahImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Jamaah([
            'nik' => $row['nik'],
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'nomor_hp' => $row['nomor_hp'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|numeric|digits:16|unique:jamaah,nik',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nomor_hp' => 'required|numeric|digits_between:10,13',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nik.required' => 'NIK wajib diisi',
            'nik.numeric' => 'NIK harus berupa angka',
            'nik.digits' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'nomor_hp.required' => 'Nomor HP wajib diisi',
            'nomor_hp.numeric' => 'Nomor HP harus berupa angka',
            'nomor_hp.digits_between' => 'Nomor HP harus 10-13 digit',
        ];
    }
}
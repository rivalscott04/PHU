<?php

namespace App\Imports;

use App\Models\Jamaah;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JamaahImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected string $jenisJamaah;

    public function __construct(string $jenisJamaah)
    {
        $this->jenisJamaah = $jenisJamaah;
    }

    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_LOWER);
        $user = Auth::user();

        return new Jamaah([
            'nik' => strval($row['nik']),
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'nomor_hp' => strval($row['nomor_hp']),
            'jenis_jamaah' => $this->jenisJamaah,
            'user_id' => $user->id,
            'travel_id' => $user->travel_id,
        ]);
    }

    public function prepareForValidation($row, $index)
    {
        $normalized = array_change_key_case($row, CASE_LOWER);

        if (!empty($normalized['nik'])) {
            $digits = preg_replace('/\D+/', '', strval($normalized['nik']));
            if (strlen($digits) === 15 && !str_starts_with($digits, '0')) {
                $digits = '0' . $digits;
            }
            $normalized['nik'] = $digits;
        }

        if (!empty($normalized['nomor_hp'])) {
            $digits = preg_replace('/\D+/', '', strval($normalized['nomor_hp']));

            if (preg_match('/^62/', $digits)) {
                $digits = preg_replace('/^62/', '0', $digits);
            } elseif (preg_match('/^8/', $digits)) {
                $digits = '0' . $digits;
            }

            $normalized['nomor_hp'] = substr($digits, 0, 15);
        }

        return $normalized;
    }

    public function rules(): array
    {
        return [
            '*.nik' => 'required|digits:16',
            '*.nama' => 'required|string|max:255',
            '*.alamat' => 'required|string',
            '*.nomor_hp' => 'required|string|max:15|regex:/^08/',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nik.required' => 'NIK wajib diisi',
            '*.nik.digits' => 'NIK harus 16 digit',
            '*.nama.required' => 'Nama wajib diisi',
            '*.alamat.required' => 'Alamat wajib diisi',
            '*.nomor_hp.required' => 'Nomor HP wajib diisi',
            '*.nomor_hp.max' => 'Nomor HP maksimal 15 digit',
            '*.nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ];
    }
}

<?php

namespace App\Imports;

use App\Models\Jamaah;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class JamaahImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $jenisJamaah;

    public function __construct()
    {
        $user = Auth::user(); // Ambil user yang sedang login
        if ($user && $user->travel) {
            $this->jenisJamaah = $user->travel->status === 'PIHK' ? 'haji' : 'umrah';
        } else {
            $this->jenisJamaah = null; // Jika tidak ada user/travel, bisa diberi default atau error handling
        }
    }

    public function model(array $row)
    {
        // Convert keys to lowercase
        $row = array_change_key_case($row, CASE_LOWER);

        $user = Auth::user();

        return new Jamaah([
            'nik' => strval($row['nik']), // Convert to string to handle Excel number formatting
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'nomor_hp' => strval($row['nomor_hp']), // Convert to string to handle Excel number formatting
            'jenis_jamaah' => $this->jenisJamaah, // Set jenis jamaah berdasarkan user login
            'user_id' => $user->id, // Set user_id dari user yang sedang login
            'travel_id' => $user->travel_id, // Set travel_id dari user yang sedang login
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nik' => 'required|numeric|digits:16',
            '*.nama' => 'required|string|max:255',
            '*.alamat' => 'required|string',
            '*.nomor_hp' => 'required|numeric|digits_between:10,13|regex:/^08/',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nik.required' => 'NIK wajib diisi',
            '*.nik.numeric' => 'NIK harus berupa angka',
            '*.nik.digits' => 'NIK harus 16 digit',
            '*.nama.required' => 'Nama wajib diisi',
            '*.alamat.required' => 'Alamat wajib diisi',
            '*.nomor_hp.required' => 'Nomor HP wajib diisi',
            '*.nomor_hp.numeric' => 'Nomor HP harus berupa angka',
            '*.nomor_hp.digits_between' => 'Nomor HP harus 10-13 digit',
            '*.nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
        ];
    }
}

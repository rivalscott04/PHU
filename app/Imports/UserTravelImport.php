<?php

namespace App\Imports;

use App\Models\User;
use App\Models\TravelCompany;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserTravelImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $travelId;
    protected $errors = [];
    protected $successCount = 0;

    public function __construct($travelId)
    {
        $this->travelId = $travelId;
    }

    public function model(array $row)
    {
        try {
            // Convert keys to lowercase
            $row = array_change_key_case($row, CASE_LOWER);

            // Validate required fields
            if (empty($row['nama']) || empty($row['email']) || empty($row['nomor_hp']) || empty($row['password'])) {
                $this->errors[] = "Row " . ($this->successCount + count($this->errors) + 1) . ": Nama, email, nomor HP, dan password wajib diisi";
                return null;
            }

            // Check if email already exists
            if (User::where('email', $row['email'])->exists()) {
                $this->errors[] = "Row " . ($this->successCount + count($this->errors) + 1) . ": Email '{$row['email']}' sudah digunakan";
                return null;
            }

            // Check if nomor_hp already exists
            if (User::where('nomor_hp', $row['nomor_hp'])->exists()) {
                $this->errors[] = "Row " . ($this->successCount + count($this->errors) + 1) . ": Nomor HP '{$row['nomor_hp']}' sudah digunakan";
                return null;
            }

            // Get travel company data for auto-fill
            $travel = TravelCompany::find($this->travelId);
            if (!$travel) {
                $this->errors[] = "Travel company tidak ditemukan";
                return null;
            }

            // Create user data
            $userData = [
                'nama' => $row['nama'],
                'email' => $row['email'],
                'nomor_hp' => $row['nomor_hp'],
                'password' => Hash::make($row['password']),
                'travel_id' => $this->travelId,
                'role' => 'user',
                'kabupaten' => $travel->kab_kota,
                'country' => 'Indonesia', // Default value
                'is_password_changed' => false,
            ];

            // Profile data akan diisi nanti oleh user melalui halaman profile

            $this->successCount++;
            return new User($userData);

        } catch (\Exception $e) {
            Log::error('UserTravelImport error: ' . $e->getMessage());
            $this->errors[] = "Row " . ($this->successCount + count($this->errors) + 1) . ": " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.email' => 'required|email|max:255',
            '*.nomor_hp' => 'required|string|max:20|regex:/^08/',
            '*.password' => 'required|string|min:5',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nama.required' => 'Nama wajib diisi',
            '*.nama.string' => 'Nama harus berupa teks',
            '*.nama.max' => 'Nama maksimal 255 karakter',
            '*.email.required' => 'Email wajib diisi',
            '*.email.email' => 'Format email tidak valid',
            '*.email.max' => 'Email maksimal 255 karakter',
            '*.nomor_hp.required' => 'Nomor HP wajib diisi',
            '*.nomor_hp.string' => 'Nomor HP harus berupa teks',
            '*.nomor_hp.max' => 'Nomor HP maksimal 20 karakter',
            '*.nomor_hp.regex' => 'Nomor HP harus diawali dengan 08',
            '*.password.required' => 'Password wajib diisi',
            '*.password.string' => 'Password harus berupa teks',
            '*.password.min' => 'Password minimal 5 karakter',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}

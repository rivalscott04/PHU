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

    protected $errors = [];
    protected $successCount = 0;
    protected $travelCompanies = [];

    public function __construct()
    {
        // Load all travel companies for fuzzy matching
        $this->travelCompanies = TravelCompany::all();
    }

    public function model(array $row)
    {
        try {
            // Convert keys to lowercase
            $row = array_change_key_case($row, CASE_LOWER);

            // Validate required fields
            if (empty($row['nama']) || empty($row['email']) || empty($row['nomor_hp']) || empty($row['password']) || empty($row['travel_company'])) {
                $this->errors[] = "Row " . ($this->successCount + count($this->errors) + 1) . ": Nama, email, nomor HP, password, dan travel company wajib diisi";
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

            // Find travel company using fuzzy matching
            $travel = $this->findTravelCompany($row['travel_company']);
            if (!$travel) {
                $this->errors[] = "Row " . ($this->successCount + count($this->errors) + 1) . ": Travel company '{$row['travel_company']}' tidak ditemukan atau tidak cocok dengan data yang ada";
                return null;
            }

            // Create user data
            $userData = [
                'nama' => $row['nama'],
                'email' => $row['email'],
                'nomor_hp' => $row['nomor_hp'],
                'password' => Hash::make($row['password']),
                'travel_id' => $travel->id,
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

    /**
     * Find travel company using fuzzy matching with 90% similarity threshold
     */
    private function findTravelCompany($inputName)
    {
        $inputName = trim($inputName);
        $bestMatch = null;
        $bestSimilarity = 0;
        $threshold = 90; // 90% similarity threshold

        foreach ($this->travelCompanies as $travel) {
            $similarity = $this->calculateSimilarity($inputName, $travel->Penyelenggara);
            
            if ($similarity >= $threshold && $similarity > $bestSimilarity) {
                $bestMatch = $travel;
                $bestSimilarity = $similarity;
            }
        }

        return $bestMatch;
    }

    /**
     * Calculate similarity percentage between two strings using Levenshtein distance
     */
    private function calculateSimilarity($str1, $str2)
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));
        
        $maxLength = max(strlen($str1), strlen($str2));
        
        if ($maxLength === 0) {
            return 100;
        }
        
        $distance = levenshtein($str1, $str2);
        $similarity = (($maxLength - $distance) / $maxLength) * 100;
        
        return round($similarity, 2);
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.email' => 'required|email|max:255',
            '*.nomor_hp' => 'required|string|max:20|regex:/^08/',
            '*.password' => 'required|string|min:5',
            '*.travel_company' => 'required|string|max:255',
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
            '*.travel_company.required' => 'Travel company wajib diisi',
            '*.travel_company.string' => 'Travel company harus berupa teks',
            '*.travel_company.max' => 'Travel company maksimal 255 karakter',
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

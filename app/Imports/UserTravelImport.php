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

            // Log setiap row sebelum diproses
            Log::debug('UserTravelImport: Processing row', $row);

            // 🔹 Normalisasi email dulu sebelum validasi
            if (!empty($row['email'])) {
                $normalizedEmail = strtolower(trim($row['email']));

                // Ganti spasi dengan titik
                $normalizedEmail = str_replace(' ', '.', $normalizedEmail);

                // Opsional: hapus karakter aneh selain huruf, angka, @, ., _
                $normalizedEmail = preg_replace('/[^a-z0-9@._-]/', '', $normalizedEmail);

                $row['email'] = $normalizedEmail;
            }

            // Validate required fields
            if (empty($row['nama']) || empty($row['email']) || empty($row['nomor_hp']) || empty($row['password']) || empty($row['travel_company'])) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Nama, email, nomor HP, password, dan travel company wajib diisi";
                $this->errors[] = $msg;
                Log::warning("UserTravelImport warning: $msg", $row);
                return null;
            }

            // Check if email already exists
            if (User::where('email', $row['email'])->exists()) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Email '{$row['email']}' sudah digunakan";
                $this->errors[] = $msg;
                Log::warning("UserTravelImport warning: $msg");
                return null;
            }

            // Check if nomor_hp already exists
            if (User::where('nomor_hp', $row['nomor_hp'])->exists()) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Nomor HP '{$row['nomor_hp']}' sudah digunakan";
                $this->errors[] = $msg;
                Log::warning("UserTravelImport warning: $msg");
                return null;
            }

            // Find travel company using fuzzy matching
            $travel = $this->findTravelCompany($row['travel_company']);
            if (!$travel) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Travel company '{$row['travel_company']}' tidak ditemukan atau tidak cocok";
                $this->errors[] = $msg;
                Log::warning("UserTravelImport warning: $msg");
                return null;
            }

            // Debug travel company yang ditemukan
            Log::info('UserTravelImport: Travel company matched', [
                'input' => $row['travel_company'],
                'matched' => $travel->Penyelenggara,
                'id' => $travel->id
            ]);

            // Create user data
            $userData = [
                'nama' => $row['nama'],
                'email' => $row['email'],
                'nomor_hp' => $row['nomor_hp'],
                'password' => Hash::make($row['password']),
                'travel_id' => $travel->id,
                'role' => 'user',
                'kabupaten' => $travel->kab_kota,
                'country' => 'Indonesia',
                'is_password_changed' => false,
            ];

            $this->successCount++;
            Log::info("UserTravelImport: User berhasil dibuat (Row {$this->successCount})", $userData);

            return new User($userData);
        } catch (\Exception $e) {
            $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": " . $e->getMessage();
            $this->errors[] = $msg;

            Log::error('UserTravelImport exception', [
                'row' => $row,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

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

    public function prepareForValidation($row, $index)
    {
        // Make a working copy
        $normalized = $row;

        // --- Normalize email: trim, lowercase, replace spaces with dots, remove weird chars ---
        if (!empty($normalized['email'])) {
            $email = strtolower(trim($normalized['email']));
            $email = str_replace(' ', '.', $email); // "PT. ABC DEF@gmail.com" -> "pt..abc.def@gmail.com"
            // remove characters not allowed (keeps letters, numbers, @ . _ -)
            $email = preg_replace('/[^a-z0-9@._\-]/i', '', $email);
            // optionally collapse multiple dots:
            $email = preg_replace('/\.{2,}/', '.', $email);

            $normalized['email'] = $email;
        }

        // --- Normalize nomor_hp: extract digits, handle country code and leading 0 ---
        if (!empty($normalized['nomor_hp'])) {
            // remove everything except digits
            $digits = preg_replace('/\D+/', '', $normalized['nomor_hp']);

            // change leading "62" to "0"
            if (preg_match('/^62/', $digits)) {
                $digits = preg_replace('/^62/', '0', $digits);
            }
            // if starts with 8 (no leading zero), add leading zero
            elseif (preg_match('/^8/', $digits)) {
                $digits = '0' . $digits;
            }

            // optional: limit length (example: max 15)
            $digits = substr($digits, 0, 20);

            Log::debug('UserTravelImport: prepareForValidation - nomor_hp normalized', [
                'index' => $index,
                'original' => $normalized['nomor_hp'],
                'normalized' => $digits,
            ]);

            $normalized['nomor_hp'] = $digits;
        }

        return $normalized;
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.email' => 'required|string|max:255',
            '*.nomor_hp' => 'required|string|max:20|regex:/^08/',
            '*.password' => 'required|min:5',
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

<?php

namespace App\Imports;

use App\Models\User;
use App\Models\CabangTravel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserCabangImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $errors = [];
    protected $successCount = 0;
    protected $cabangTravels = [];

    public function __construct()
    {
        // Load all cabang travel companies for fuzzy matching
        $this->cabangTravels = CabangTravel::all();
    }

    public function model(array $row)
    {
        try {
            // Convert keys to lowercase
            $row = array_change_key_case($row, CASE_LOWER);

            // Log setiap row sebelum diproses
            Log::debug('UserCabangImport: Processing row', $row);

            // 🔹 Normalisasi email dulu sebelum validasi
            if (!empty($row['email'])) {
                $normalizedEmail = strtolower(trim($row['email']));

                // Ganti spasi dengan titik
                $normalizedEmail = str_replace(' ', '.', $normalizedEmail);

                // Opsional: hapus karakter aneh selain huruf, angka, @, ., _
                $normalizedEmail = preg_replace('/[^a-z0-9@._-]/', '', $normalizedEmail);

                $row['email'] = $normalizedEmail;
            }

            // Convert password to string (handle Excel number formatting)
            if (!empty($row['password'])) {
                $row['password'] = strval($row['password']);
            }

            // Validate required fields
            if (empty($row['nama']) || empty($row['email']) || empty($row['nomor_hp']) || empty($row['password']) || empty($row['travel_company'])) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Semua field wajib diisi (nama, email, nomor_hp, password, travel_company)";
                $this->errors[] = $msg;
                Log::warning("UserCabangImport warning: $msg", $row);
                return null;
            }

            // Check if email already exists
            if (User::where('email', $row['email'])->exists()) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Email '{$row['email']}' sudah digunakan";
                $this->errors[] = $msg;
                Log::warning("UserCabangImport warning: $msg");
                return null;
            }

            // Check if nomor_hp already exists
            if (User::where('nomor_hp', $row['nomor_hp'])->exists()) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Nomor HP '{$row['nomor_hp']}' sudah digunakan";
                $this->errors[] = $msg;
                Log::warning("UserCabangImport warning: $msg");
                return null;
            }

            // Find cabang travel company using fuzzy matching
            $cabang = $this->findCabangTravel($row['travel_company']);
            if (!$cabang) {
                $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": Travel company '{$row['travel_company']}' tidak ditemukan atau tidak cocok";
                $this->errors[] = $msg;
                Log::warning("UserCabangImport warning: $msg");
                return null;
            }

            // Debug cabang travel yang ditemukan
            Log::info('UserCabangImport: Cabang travel matched', [
                'input' => $row['travel_company'],
                'matched' => $cabang->Penyelenggara,
                'id_cabang' => $cabang->id_cabang,
                'kabupaten' => $cabang->kabupaten
            ]);

            // Create user data
            $userData = [
                'nama' => $row['nama'],
                'email' => $row['email'],
                'nomor_hp' => $row['nomor_hp'],
                'password' => Hash::make($row['password']),
                'travel_id' => null, // No pusat reference
                'cabang_id' => $cabang->id_cabang, // Use cabang ID
                'role' => 'user',
                'kabupaten' => $cabang->kabupaten,
                'country' => 'Indonesia',
                'is_password_changed' => false,
            ];

            $this->successCount++;
            Log::info("UserCabangImport: User berhasil dibuat (Row {$this->successCount})", $userData);

            return new User($userData);
        } catch (\Exception $e) {
            $msg = "Row " . ($this->successCount + count($this->errors) + 1) . ": " . $e->getMessage();
            $this->errors[] = $msg;

            Log::error('UserCabangImport exception', [
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
     * Find cabang travel company using fuzzy matching with 90% similarity threshold
     */
    private function findCabangTravel($inputName)
    {
        $inputName = trim($inputName);
        $bestMatch = null;
        $bestSimilarity = 0;
        $threshold = 90; // 90% similarity threshold

        foreach ($this->cabangTravels as $cabang) {
            $similarity = $this->calculateSimilarity($inputName, $cabang->Penyelenggara);

            if ($similarity >= $threshold && $similarity > $bestSimilarity) {
                $bestMatch = $cabang;
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
            // Convert to string first to handle Excel number formatting
            $phoneNumber = strval($normalized['nomor_hp']);
            
            // remove everything except digits
            $digits = preg_replace('/\D+/', '', $phoneNumber);

            // Handle different phone number formats
            if (preg_match('/^62/', $digits)) {
                // Change leading "62" to "0" (Indonesian country code)
                $digits = preg_replace('/^62/', '0', $digits);
            } elseif (preg_match('/^8/', $digits)) {
                // If starts with 8 (no leading zero), add leading zero
                $digits = '0' . $digits;
            } elseif (preg_match('/^0/', $digits)) {
                // Already starts with 0, keep as is
                $digits = $digits;
            } else {
                // If no leading 0 or 62, add leading 0
                $digits = '0' . $digits;
            }

            $normalized['nomor_hp'] = $digits;
        }

        // --- Normalize password: convert to string to handle Excel number formatting ---
        if (!empty($normalized['password'])) {
            $normalized['password'] = strval($normalized['password']);
        }

        return $normalized;
    }

    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.email' => 'required|email|max:255',
            '*.nomor_hp' => 'required|max:20',
            '*.password' => 'required|min:6',
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
            '*.password.required' => 'Password wajib diisi',
            '*.password.min' => 'Password minimal 6 karakter',
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

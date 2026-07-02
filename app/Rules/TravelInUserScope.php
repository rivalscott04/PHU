<?php

namespace App\Rules;

use App\Models\TravelCompany;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TravelInUserScope implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'kabupaten') {
            $travel = TravelCompany::find($value);

            if (! $travel || $travel->kab_kota !== $user->getKabupaten()) {
                $fail('Travel tidak berada dalam wilayah Anda.');
            }

            return;
        }

        $fail('Anda tidak memiliki akses untuk memilih travel ini.');
    }
}

<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\UsesFriendlyValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFindingRequest extends FormRequest
{
    use UsesFriendlyValidation;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('deadline') && $this->input('deadline') === '') {
            $this->merge(['deadline' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:100'],
            'severity' => ['required', Rule::in(['MINOR', 'MAJOR', 'CRITICAL'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'recommendation' => ['required', 'string', 'max:5000'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /** @return array<string, string> */
    protected function friendlyValidationOverrides(): array
    {
        return [
            'deadline.after_or_equal' => 'Deadline temuan tidak boleh sebelum hari ini.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFindingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
}

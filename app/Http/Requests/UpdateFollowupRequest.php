<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'string', 'min:20'],
            'attachment' => ['sometimes', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip', 'max:10240'],
            'status' => ['sometimes', Rule::in([
                'SUBMITTED', 'PENDING', 'REVISION_REQUIRED', 'VERIFIED', 'REJECTED', 'CLOSED',
            ])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}

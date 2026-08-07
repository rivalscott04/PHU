<?php

namespace App\Http\Requests;

use App\Helpers\ValidationHelper;
use App\Http\Requests\Concerns\UsesFriendlyValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFollowupRequest extends FormRequest
{
    use UsesFriendlyValidation;

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
            'remarks' => ['nullable', 'string', 'max:'.ValidationHelper::TEXT_MAX],
        ];
    }

    protected function friendlyValidationOverrides(): array
    {
        return array_merge(
            ValidationHelper::fileMaxMb('attachment', 10),
            [
                'description.min' => 'Deskripsi tindak lanjut minimal :min karakter.',
            ]
        );
    }
}

<?php

namespace App\Http\Requests;

use App\Helpers\ValidationHelper;
use App\Http\Requests\Concerns\UsesFriendlyValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreFollowupRequest extends FormRequest
{
    use UsesFriendlyValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'finding_id' => ['required', 'exists:pengawasan_temuan,id'],
            'description' => ['required', 'string', 'min:20'],
            'attachment' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip', 'max:10240'],
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

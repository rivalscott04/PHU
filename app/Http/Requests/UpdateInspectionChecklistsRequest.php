<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\UsesFriendlyValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInspectionChecklistsRequest extends FormRequest
{
    use UsesFriendlyValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:pengawasan_checklists,id'],
            'items.*.answer' => ['nullable', 'string', 'max:5000'],
            'items.*.note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

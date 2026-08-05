<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\UsesFriendlyValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionRequest extends FormRequest
{
    use UsesFriendlyValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'travel_id' => ['required', 'exists:travels,id', new \App\Rules\TravelInUserScope()],
            'inspection_date' => ['required', 'date'],
            'inspection_type' => ['required', Rule::in(['ROUTINE', 'SPOT_CHECK', 'COMPLAINT_BASED', 'SPECIAL'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}

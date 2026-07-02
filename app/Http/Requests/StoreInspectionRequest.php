<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'travel_id' => ['required', 'exists:travels,id', new \App\Rules\TravelInUserScope()],
            'inspection_no' => ['required', 'string', 'max:50', 'unique:pengawasan,inspection_no'],
            'inspection_date' => ['required', 'date'],
            'inspection_type' => ['required', Rule::in(['ROUTINE', 'SPOT_CHECK', 'COMPLAINT_BASED', 'SPECIAL'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}

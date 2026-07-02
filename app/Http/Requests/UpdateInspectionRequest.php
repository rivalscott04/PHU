<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $inspectionId = $this->route('pengawasan')?->id ?? $this->route('pengawasan');

        return [
            'travel_id' => ['sometimes', 'exists:travels,id', new \App\Rules\TravelInUserScope()],
            'inspection_no' => ['sometimes', 'string', 'max:50', Rule::unique('pengawasan', 'inspection_no')->ignore($inspectionId)],
            'inspection_date' => ['sometimes', 'date'],
            'inspection_type' => ['sometimes', Rule::in(['ROUTINE', 'SPOT_CHECK', 'COMPLAINT_BASED', 'SPECIAL'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}

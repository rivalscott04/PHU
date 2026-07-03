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
        return [
            'travel_id' => ['sometimes', 'exists:travels,id', new \App\Rules\TravelInUserScope()],
            'inspection_date' => ['sometimes', 'date'],
            'inspection_type' => ['sometimes', Rule::in(['ROUTINE', 'SPOT_CHECK', 'COMPLAINT_BASED', 'SPECIAL'])],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in([
                'DRAFT', 'SCHEDULED', 'ON_PROGRESS', 'WAITING_FOLLOWUP',
                'FOLLOWUP_UPLOADED', 'VERIFIED', 'CLOSED', 'CANCELLED',
            ])],
        ];
    }
}

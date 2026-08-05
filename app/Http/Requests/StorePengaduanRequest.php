<?php

namespace App\Http\Requests;

use App\Helpers\ValidationHelper;
use App\Http\Requests\Concerns\UsesFriendlyValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePengaduanRequest extends FormRequest
{
    use UsesFriendlyValidation;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_pengadu' => strip_tags(trim((string) $this->input('nama_pengadu', ''))),
            'hal_aduan' => strip_tags(trim((string) $this->input('hal_aduan', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pengadu' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'travels_id' => ['required', 'integer', 'exists:travels,id'],
            'hal_aduan' => ['required', 'string', 'min:10', 'max:5000'],
            'berkas_aduan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }

    protected function friendlyValidationOverrides(): array
    {
        return ValidationHelper::fileMaxMb('berkas_aduan', 2);
    }
}

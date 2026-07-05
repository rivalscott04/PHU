<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengaduanRequest extends FormRequest
{
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

    public function messages(): array
    {
        return [
            'nama_pengadu.regex' => 'Nama pengadu hanya boleh berisi huruf, spasi, tanda hubung, titik, dan apostrof.',
            'hal_aduan.min' => 'Hal yang diadukan minimal 10 karakter.',
            'hal_aduan.max' => 'Hal yang diadukan maksimal 5000 karakter.',
            'berkas_aduan.mimes' => 'Lampiran harus berformat PDF, JPG, atau PNG.',
            'berkas_aduan.max' => 'Ukuran lampiran maksimal 2MB.',
        ];
    }
}

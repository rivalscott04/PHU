<?php

namespace App\Models;

use App\Support\KanwilContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifikatSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_penandatangan',
        'nip_penandatangan',
        'jabatan_penandatangan',
    ];

    /** @return object{nama: string, nip: string, jabatan: string} */
    public static function signatory(): object
    {
        $settings = static::first();

        return (object) [
            'nama' => (string) ($settings?->nama_penandatangan ?? ''),
            'nip' => (string) ($settings?->nip_penandatangan ?? ''),
            'jabatan' => (string) ($settings?->jabatan_penandatangan ?: KanwilContact::get('sertifikat_kanwil_jabatan')),
        ];
    }
}

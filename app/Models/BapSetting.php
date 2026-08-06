<?php

namespace App\Models;

use App\Support\KanwilContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BapSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_penandatangan',
        'jabatan_penandatangan',
    ];

    /** @return object{nama: string, jabatan: string} */
    public static function signatory(): object
    {
        $settings = static::first();

        return (object) [
            'nama' => (string) ($settings?->nama_penandatangan ?? ''),
            'jabatan' => (string) ($settings?->jabatan_penandatangan ?: KanwilContact::get('bap_kanwil_jabatan')),
        ];
    }
}

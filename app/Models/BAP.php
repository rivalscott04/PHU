<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BAP extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $table = 'bap';

    protected $fillable = [
        'name',
        'jabatan',
        'ppiuname',
        'address_phone',
        'kab_kota',
        'people',
        'package',
        'days',
        'price',
        'datetime',
        'airlines',
        'returndate',
        'airlines2',
    ];
}

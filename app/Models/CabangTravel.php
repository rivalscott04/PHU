<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabangTravel extends Model
{
    use HasFactory;

    protected $table = 'travel_cabang';

    protected $fillable = [
        'travel_id',
        'kabupaten',
        'pusat',
        'pimpinan_pusat',
        'alamat_pusat',
        'SK_BA',
        'tanggal',
        'pimpinan_cabang',
        'alamat_cabang',
        'telepon',
    ];

    public function travel()
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }
}

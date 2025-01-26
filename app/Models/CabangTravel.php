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
        'SK_BA',
        'tanggal',
        'pimpinan_cabang',
        'alamat',
        'telepon',
    ];

    public function travel()
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }
}

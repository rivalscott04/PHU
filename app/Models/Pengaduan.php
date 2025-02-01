<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;
    protected $table = 'pengaduan';

    protected $fillable = [
        'nama_pengadu',
        'travels_id',
        'hal_aduan',
        'berkas_aduan',
        'status'
    ];

    public function travel()  // Note: singular form, not 'travels'
    {
        return $this->belongsTo(TravelCompany::class, 'travels_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Jamaah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jamaah';
    
    protected $fillable = [
        'nik',
        'nama',
        'alamat',
        'nomor_hp'
    ];

    protected $dates = ['deleted_at'];

    public function generateQrCode()
    {
        return QrCode::size(200)
                    ->format('svg')
                    ->generate(route('jamaah.detail', $this->id));
    }
}
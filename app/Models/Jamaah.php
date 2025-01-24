<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengunduran extends Model
{
    use HasFactory;

    protected $table = 'pengunduran';
    protected $fillable = ['user_id', 'berkas_pengunduran'];

    public function travel()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengunduran extends Model
{
    use HasFactory;

    protected $table = 'pengunduran';
    protected $fillable = ['user_id', 'berkas_pengunduran', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

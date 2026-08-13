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
        'user_id',
        'cabang_id',
        'status',
        'pdf_file_path',
        'nomor_surat',
        'tanggal_terbit',
        'travel_token',
        'kanwil_token',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function jamaah()
    {
        return $this->belongsToMany(Jamaah::class, 'bap_jamaah', 'bap_id', 'jamaah_id')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

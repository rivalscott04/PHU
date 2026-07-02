<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionPhoto extends Model
{
    public $timestamps = false;

    protected $table = 'pengawasan_photos';

    protected $fillable = [
        'inspection_id',
        'photo',
        'caption',
        'latitude',
        'longitude',
        'taken_at',
    ];

    protected $casts = [
        'inspection_id' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'taken_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}

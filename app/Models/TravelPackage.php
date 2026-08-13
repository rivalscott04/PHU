<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelPackage extends Model
{
    protected $fillable = [
        'travel_id',
        'cabang_id',
        'name',
        'price',
        'days',
        'default_airline',
        'service_notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function travel(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }
}

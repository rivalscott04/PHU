<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $table = 'risk_scores';

    protected $fillable = [
        'travel_id',
        'complaint_score',
        'inspection_score',
        'followup_score',
        'certificate_score',
        'bap_score',
        'activity_score',
        'total_score',
        'risk_level',
        'last_calculated_at',
    ];

    protected $casts = [
        'travel_id' => 'integer',
        'complaint_score' => 'decimal:2',
        'inspection_score' => 'decimal:2',
        'followup_score' => 'decimal:2',
        'certificate_score' => 'decimal:2',
        'bap_score' => 'decimal:2',
        'activity_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'risk_level' => RiskLevel::class,
        'last_calculated_at' => 'datetime',
    ];

    public function travel(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }
}

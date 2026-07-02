<?php

namespace App\Models;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionFinding extends Model
{
    protected $table = 'pengawasan_temuan';

    protected $fillable = [
        'inspection_id',
        'category',
        'severity',
        'title',
        'description',
        'recommendation',
        'deadline',
        'status',
    ];

    protected $casts = [
        'inspection_id' => 'integer',
        'severity' => FindingSeverity::class,
        'deadline' => 'date',
        'status' => FindingStatus::class,
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class, 'finding_id');
    }
}

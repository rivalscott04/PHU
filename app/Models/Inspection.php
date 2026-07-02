<?php

namespace App\Models;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    protected $table = 'pengawasan';

    protected $fillable = [
        'travel_id',
        'inspection_no',
        'inspection_date',
        'inspection_type',
        'overall_score',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'travel_id' => 'integer',
        'inspection_date' => 'date',
        'inspection_type' => InspectionType::class,
        'overall_score' => 'decimal:2',
        'status' => InspectionStatus::class,
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function travel(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(InspectionChecklist::class, 'inspection_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(InspectionFinding::class, 'inspection_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class, 'inspection_id');
    }
}

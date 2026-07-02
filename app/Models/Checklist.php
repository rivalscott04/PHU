<?php

namespace App\Models;

use App\Enums\ChecklistInputType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    protected $table = 'master_checklists';

    protected $fillable = [
        'category_id',
        'code',
        'title',
        'description',
        'input_type',
        'weight',
        'required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'input_type' => ChecklistInputType::class,
        'weight' => 'integer',
        'required' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChecklistCategory::class, 'category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ChecklistOption::class, 'checklist_id');
    }

    public function inspectionChecklists(): HasMany
    {
        return $this->hasMany(InspectionChecklist::class, 'master_checklist_id');
    }
}

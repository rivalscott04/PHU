<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistOption extends Model
{
    protected $table = 'master_checklist_options';

    protected $fillable = [
        'checklist_id',
        'label',
        'value',
        'score',
        'sort_order',
    ];

    protected $casts = [
        'checklist_id' => 'integer',
        'score' => 'integer',
        'sort_order' => 'integer',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class, 'checklist_id');
    }
}

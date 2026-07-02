<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionChecklist extends Model
{
    protected $table = 'pengawasan_checklists';

    protected $fillable = [
        'inspection_id',
        'master_checklist_id',
        'answer',
        'score',
        'note',
    ];

    protected $casts = [
        'inspection_id' => 'integer',
        'master_checklist_id' => 'integer',
        'score' => 'integer',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function masterChecklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class, 'master_checklist_id');
    }
}

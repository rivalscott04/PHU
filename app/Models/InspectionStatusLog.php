<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionStatusLog extends Model
{
    public $timestamps = false;

    protected $table = 'pengawasan_status_logs';

    protected $fillable = [
        'inspection_id',
        'from_status',
        'to_status',
        'created_by',
    ];

    protected $casts = [
        'inspection_id' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowupLog extends Model
{
    public $timestamps = false;

    protected $table = 'pengawasan_followup_logs';

    protected $fillable = [
        'followup_id',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'followup_id' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
    ];

    public function followup(): BelongsTo
    {
        return $this->belongsTo(Followup::class, 'followup_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

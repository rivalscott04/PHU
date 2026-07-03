<?php

namespace App\Models;

use App\Enums\WorkQueueStatus;
use App\Enums\WorkQueueType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisionWorkQueue extends Model
{
    protected $table = 'pengawasan_antrian';

    protected $fillable = [
        'type',
        'priority',
        'travel_id',
        'kabupaten',
        'reference_type',
        'reference_id',
        'title',
        'summary',
        'action_url',
        'status',
        'due_at',
        'assigned_to',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'type' => WorkQueueType::class,
        'status' => WorkQueueStatus::class,
        'priority' => 'integer',
        'due_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function travel(): BelongsTo
    {
        return $this->belongsTo(TravelCompany::class, 'travel_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isActionable(): bool
    {
        return in_array($this->status, [WorkQueueStatus::Open, WorkQueueStatus::InProgress], true);
    }
}

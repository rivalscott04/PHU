<?php

namespace App\Models;

use App\Enums\FollowupStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Followup extends Model
{
    protected $table = 'pengawasan_followups';

    protected $fillable = [
        'finding_id',
        'description',
        'attachment',
        'status',
        'submitted_at',
        'verified_by',
        'verified_at',
        'remarks',
    ];

    protected $casts = [
        'finding_id' => 'integer',
        'status' => FollowupStatus::class,
        'submitted_at' => 'datetime',
        'verified_by' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function finding(): BelongsTo
    {
        return $this->belongsTo(InspectionFinding::class, 'finding_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FollowupLog::class, 'followup_id');
    }
}

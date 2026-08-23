<?php

namespace App\Models;

use App\Enums\TeamMembershipEndReason;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMembership extends Model
{
    protected $fillable = [
        'team_id',
        'employee_id',
        'start_date',
        'end_date',
        'is_current',
        'end_reason',
        'created_by',
        'ended_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'end_reason' => TeamMembershipEndReason::class,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereNull('end_date')->where('is_current', true);
    }

    public function scopeAdminCurrent(Builder $query): Builder
    {
        return $query->whereNull('end_date');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereNotNull('end_date')->whereNull('is_current');
    }
}

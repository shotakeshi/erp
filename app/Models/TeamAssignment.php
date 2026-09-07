<?php

namespace App\Models;

use App\Enums\TeamAssignmentEndReason;
use App\Enums\TeamAssignmentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamAssignment extends Model
{
    protected $fillable = [
        'team_id',
        'employee_id',
        'type',
        'role',
        'start_date',
        'end_date',
        'is_current',
        'end_reason',
        'end_reason_note',
        'created_by',
        'ended_by',
    ];

    protected $casts = [
        'type' => TeamAssignmentType::class,
        'end_reason' => TeamAssignmentEndReason::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
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

    public function scopeForType(Builder $query, TeamAssignmentType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeMembers(Builder $query): Builder
    {
        return $query->forType(TeamAssignmentType::MEMBER);
    }

    public function scopeManagers(Builder $query): Builder
    {
        return $query->forType(TeamAssignmentType::MANAGER);
    }

    public function scopeCurrentAssignment(Builder $query): Builder
    {
        return $query->whereNull('end_date')->where('is_current', true);
    }

    public function scopePastAssignment(Builder $query): Builder
    {
        return $query->whereNotNull('end_date')->whereNull('is_current');
    }
}

<?php

namespace App\Models;

use App\Enums\ProjectAssignmentEndReason;
use Database\Factories\ProjectAssignmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAssignment extends Model
{
    /** @use HasFactory<ProjectAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'employee_id',
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
        'start_date' => 'date',
        'end_date' => 'date',
        'end_reason' => ProjectAssignmentEndReason::class,
        'is_current' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

    public function scopeCurrentAssignment(Builder $query): Builder
    {
        return $query->whereNull('end_date')->where('is_current', true);
    }
}

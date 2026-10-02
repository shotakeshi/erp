<?php

namespace App\Models;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'client_id',
        'project_type',
        'description',
        'project_avatar',
        'status',
        'priority',
        'start_date',
        'planned_end_date',
        'actual_end_date',
        'manager_id',
        'team_id',
        'department_id',
        'project_url',
        'created_by',
    ];

    protected $casts = [
        'project_type' => ProjectType::class,
        'status' => ProjectStatus::class,
        'priority' => ProjectPriority::class,
        'start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id')->withTrashed();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_members')
            ->withTrashed()
            ->withPivot([
                'role',
                'start_date',
                'end_date',
                'end_reason',
                'end_reason_note',
                'created_by',
            ])
            ->withTimestamps()
            ->wherePivotNull('end_date');
    }
}

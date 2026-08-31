<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\Gender;
use App\Enums\TeamAssignmentRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'employee_id', 'first_name', 'last_name', 'email', 'phone',
        'dob', 'gender', 'nationality', 'address', 'city', 'state', 'country', 'postal_code',
        'department_id', 'position_id', 'reporting_manager_id', 'date_of_joining',
        'contract_type', 'salary', 'avatar',
    ];

    protected $casts = [
        'dob' => 'date',
        'date_of_joining' => 'date',
        'salary' => 'decimal:2',
        'gender' => Gender::class,
        'contract_type' => ContractType::class,
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getEmployeeIdFullNameAttribute(): string
    {
        return "{$this->employee_id} | {$this->first_name} {$this->last_name}";
    }

    public function getDateAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function teamAssignments(): HasMany
    {
        return $this->hasMany(TeamAssignment::class);
    }

    public function teamMemberships(): HasMany
    {
        return $this->teamAssignments()->members();
    }

    public function teamManagerAssignments(): HasMany
    {
        return $this->teamAssignments()->managers();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_assignments')
            ->withPivot(['role', 'start_date', 'end_date', 'is_current', 'end_reason', 'end_reason_note', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivot('role', TeamAssignmentRole::MEMBER->value)
            ->wherePivotNull('end_date')
            ->wherePivot('is_current', true);
    }

    public function managedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_assignments')
            ->withPivot(['role', 'start_date', 'end_date', 'is_current', 'end_reason', 'end_reason_note', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivot('role', TeamAssignmentRole::MANAGER->value)
            ->wherePivotNull('end_date')
            ->wherePivot('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('status', UserStatus::ACTIVE);
        });
    }
}

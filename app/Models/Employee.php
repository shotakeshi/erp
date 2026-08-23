<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\Gender;
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

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class);
    }

    public function teamManagerAssignments(): HasMany
    {
        return $this->hasMany(TeamManager::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_memberships')
            ->withPivot(['start_date', 'end_date', 'is_current', 'end_reason', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivotNull('end_date');
    }

    public function managedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_managers')
            ->withPivot(['start_date', 'end_date', 'is_current', 'end_reason', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivotNull('end_date');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('status', UserStatus::ACTIVE);
        });
    }
}

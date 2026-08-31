<?php

namespace App\Models;

use App\Enums\TeamAssignmentRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = trim($value);
    }

    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = Str::upper(trim($value));
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeamAssignment::class);
    }

    public function memberAssignments(): HasMany
    {
        return $this->assignments()->members();
    }

    public function managerAssignments(): HasMany
    {
        return $this->assignments()->managers();
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_assignments')
            ->withTrashed()
            ->withPivot(['role', 'start_date', 'end_date', 'is_current', 'end_reason', 'end_reason_note', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivot('role', TeamAssignmentRole::MEMBER->value)
            ->wherePivotNull('end_date')
            ->wherePivot('is_current', true);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_assignments')
            ->withTrashed()
            ->withPivot(['role', 'start_date', 'end_date', 'is_current', 'end_reason', 'end_reason_note', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivot('role', TeamAssignmentRole::MANAGER->value)
            ->wherePivotNull('end_date')
            ->wherePivot('is_current', true);
    }
}

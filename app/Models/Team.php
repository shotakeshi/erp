<?php

namespace App\Models;

use App\Enums\TeamAssignmentType;
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
        'logo',
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
            ->withPivot(['type', 'role', 'start_date', 'end_date', 'is_current', 'end_reason', 'end_reason_note', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivot('type', TeamAssignmentType::MEMBER->value)
            ->wherePivotNull('end_date')
            ->wherePivot('is_current', true);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_assignments')
            ->withTrashed()
            ->withPivot(['type', 'role', 'start_date', 'end_date', 'is_current', 'end_reason', 'end_reason_note', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivot('type', TeamAssignmentType::MANAGER->value)
            ->wherePivotNull('end_date')
            ->wherePivot('is_current', true);
    }
}

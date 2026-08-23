<?php

namespace App\Models;

use App\Enums\TeamStatus;
use Illuminate\Database\Eloquent\Builder;
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
        'status',
    ];

    protected $casts = [
        'status' => TeamStatus::class,
    ];

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = trim($value);
    }

    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = Str::upper(trim($value));
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class);
    }

    public function managerAssignments(): HasMany
    {
        return $this->hasMany(TeamManager::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_memberships')
            ->withTrashed()
            ->withPivot(['start_date', 'end_date', 'is_current', 'end_reason', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivotNull('end_date');
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_managers')
            ->withTrashed()
            ->withPivot(['start_date', 'end_date', 'is_current', 'end_reason', 'created_by', 'ended_by'])
            ->withTimestamps()
            ->wherePivotNull('end_date');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', TeamStatus::ACTIVE);
    }
}

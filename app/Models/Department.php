<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'description', 'head_id', 'parent_id', 'cost_center'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function descendantIds(): array
    {
        $ids = [];

        $this->loadMissing('children');

        foreach ($this->children as $child) {
            $ids[] = $child->id;

            $ids = array_merge(
                $ids,
                $child->descendantIds()
            );
        }

        return $ids;
    }
}

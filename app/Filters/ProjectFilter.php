<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProjectFilter
{
    public function apply(Builder $query, array $filters = []): Builder
    {
        $search = trim($filters['search'] ?? '');

        return $query->when($search !== '', function (Builder $query) use ($search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        });
    }
}

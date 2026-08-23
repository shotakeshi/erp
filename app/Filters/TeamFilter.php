<?php

namespace App\Filters;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class TeamFilter
{
    public function apply(Builder $query, array $filters = []): Builder
    {
        $search = trim($filters['search']) ?? null;
        return $query
            ->when($search !== null && $search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
    }
}

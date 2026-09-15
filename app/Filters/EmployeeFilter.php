<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EmployeeFilter
{
    public function apply(
        Builder $query,
        array $filters = []
    ): Builder {
        return $query
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, $value) =>
                $this->search($query, $value)
            )
            ->when(
                $filters['status'] ?? null,
                fn (Builder $query, $value) =>
                $this->status($query, $value)
            )
            ->when(
                $filters['department_id'] ?? null,
                fn (Builder $query, $value) =>
                $query->where('department_id', $value)
            )
            ->when(
                $filters['position_id'] ?? null,
                fn (Builder $query, $value) =>
                $query->where('position_id', $value)
            )
            ->when(
                $filters['contract_type'] ?? null,
                fn (Builder $query, $value) =>
                $query->where('contract_type', $value)
            );
    }

    public function searchTeamMember(Builder $query, string $search): Builder
    {
        foreach (preg_split('/\s+/u', trim(Str::ascii($search)), -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $query->where(function (Builder $query) use ($word): void {
                $query->where('first_name', 'like', "%{$word}%")
                    ->orWhere('last_name', 'like', "%{$word}%")
                    ->orWhereHas('position', fn (Builder $position) => $position->where('name', 'like', "%{$word}%"));
            });
        }

        return $query;
    }

    private function status(
        Builder $query,
        string $value
    ): void {
        $query->whereHas('user', function (Builder $query) use ($value) {
            $query->where('status', $value);
        });
    }

    private function search(
        Builder $query,
        string $value
    ): void {
        $query->where(function (Builder $query) use ($value) {

            if (is_numeric($value)) {
                $query->where('employees.id', $value);
            }

            $query
                ->orWhere('first_name', 'like', "%{$value}%")
                ->orWhere('last_name', 'like', "%{$value}%")
                ->orWhereRaw(
                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                    ["%{$value}%"]
                )
                ->orWhere('phone', 'like', "%{$value}%")
                ->orWhereHas('user', function (Builder $query) use ($value) {
                    $query->where('email', 'like', "%{$value}%");
                });
        });
    }
}
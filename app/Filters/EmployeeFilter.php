<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

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
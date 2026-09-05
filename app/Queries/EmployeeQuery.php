<?php

namespace App\Queries;

use App\Filters\EmployeeFilter;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class EmployeeQuery
{
    public function __construct(
        private readonly EmployeeFilter $employeeFilter,
    ) {}

    public function forSelect(?Employee $exceptEmployee = null): EloquentCollection
    {
        $query = Employee::query()
            ->active()
            ->select([
                'employees.id',
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name',
                'employees.avatar',
                'employees.position_id',
            ])
            ->with('position:id,name');

        if ($exceptEmployee) {
            $excludedIds = collect([$exceptEmployee->id])
                ->merge($this->getSubordinateIds($exceptEmployee))
                ->unique()
                ->values();

            $query->whereNotIn('id', $excludedIds);
        }

        return $query->orderBy('id')->get();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->paginateEmployees(Employee::query(), $filters);
    }

    public function paginateTrashed(array $filters): LengthAwarePaginator
    {
        return $this->paginateEmployees(Employee::onlyTrashed(), $filters);
    }

    private function paginateEmployees(Builder $query, array $filters): LengthAwarePaginator
    {
        return $this->employeeFilter
            ->apply($query, $filters)
            ->with([
                'user',
                'department',
                'position',
            ])
            ->paginate(20)
            ->withQueryString();
    }

    private function getSubordinateIds(Employee $employee): array
    {
        $ids = [];

        $children = Employee::query()
            ->where('reporting_manager_id', $employee->id)
            ->pluck('id');

        foreach ($children as $childId) {
            $ids[] = $childId;

            $child = Employee::find($childId);

            if ($child) {
                $ids = array_merge(
                    $ids,
                    $this->getSubordinateIds($child)
                );
            }
        }

        return array_values(array_unique($ids));
    }
}

<?php

namespace App\Queries;

use App\Filters\EmployeeFilter;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
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

    public function searchTeamEmployees(?Team $team, string $search, array $excludedIds = []): Paginator
    {
        return $this->searchAssignableEmployees(
            'teamAssignments',
            'team_id',
            $team?->getKey(),
            $search,
            $excludedIds
        );
    }

    public function searchProjectEmployees(?Project $project, string $search, array $excludedIds = []): Paginator
    {
        return $this->searchAssignableEmployees(
            'projectAssignments',
            'project_id',
            $project?->getKey(),
            $search,
            $excludedIds
        );
    }

    public function searchAssignableEmployees(
        string $relation,
        string $foreignKey,
        ?int $assignableId,
        string $search,
        array $excludedIds = [],
    ): Paginator {
        $query = Employee::query()->active()
            ->select(['id', 'first_name', 'last_name', 'avatar', 'position_id'])
            ->with('position:id,name')
            ->when($assignableId !== null, fn (Builder $query) => $query->whereDoesntHave(
                $relation,
                fn (Builder $assignments) => $assignments
                    ->where($foreignKey, $assignableId)
                    ->currentAssignment(),
            ))
            ->when(
                $excludedIds !== [],
                fn (Builder $query) => $query->whereNotIn('id', $excludedIds),
            );
        
        return $this->employeeFilter->searchEmployeeOption($query, $search)
                    ->orderBy('id')
                    ->simplePaginate(20);
    }

    public function selectedEmployees(array $employeeIds): EloquentCollection
    {
        return Employee::query()
            ->active()
            ->select(['id', 'first_name', 'last_name', 'avatar', 'position_id'])
            ->with('position:id,name')
            ->whereIn('id', $employeeIds)
            ->get();
    }

    /**
     * Map employees to the item shape used by the employee picker (modal list and preselected items).
     */
    public function employeePickerItems(iterable $employees): array
    {
        return collect($employees)
            ->map(static fn (Employee $employee): array => [
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'detail' => $employee->position?->name ?? '-',
                'avatar_url' => $employee->avatar ? image_url($employee->avatar) : null,
            ])
            ->values()
            ->all();
    }

    /**
     * Rebuild the assignment rows of a create/edit form from old('members') input.
     */
    public function initialAssignmentMembers(array $members): array
    {
        $members = collect($members)->filter(fn ($member) => ! empty($member['employee_id']));
        $items = collect($this->employeePickerItems(
            $this->selectedEmployees($members->pluck('employee_id')->all()),
        ))->keyBy('employee_id');
        
        return $members
            ->map(fn ($member): ?array => ($item = $items->get((int) $member['employee_id']))
                ? [...$item, 'role' => $member['role'] ?? '']
                : null)
            ->filter()
            ->values()
            ->all();
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

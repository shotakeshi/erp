<?php

namespace App\Services\Shared;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Team;
use App\Queries\DepartmentQuery;
use App\Queries\EmployeeQuery;
use App\Queries\PositionQuery;
use App\Queries\ProjectQuery;
use App\Queries\TeamQuery;
use Illuminate\Contracts\Pagination\Paginator;

class FormOptionService
{
    public function __construct(
        protected EmployeeQuery $employeeQuery,
        protected DepartmentQuery $departmentQuery,
        protected PositionQuery $positionQuery,
        protected ProjectQuery $projectQuery,
        protected TeamQuery $teamQuery
    ) {}

    public function employeeOptions(?Employee $exceptEmployee = null)
    {
        return $this->employeeQuery->forSelect($exceptEmployee);
    }

    public function selectedEmployees(array $employeeIds)
    {
        return $this->employeeQuery->selectedEmployees($employeeIds);
    }

    public function searchTeamEmployees(?Team $team, string $search, array $excludedIds)
    {
        return $this->employeeQuery->searchTeamEmployees($team, $search, $excludedIds);
    }

    public function searchProjectEmployees(?Project $project, string $search, array $excludedIds)
    {
        return $this->employeeQuery->searchProjectEmployees($project, $search, $excludedIds);
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
     * Rebuild the assignment rows of a create form (employee picker item + role) from old('members') input.
     */
    public function initialAssignmentMembers(array $members): array
    {
        $members = collect($members)->filter(static fn ($member): bool => ! empty($member['employee_id']));
        $items = collect($this->employeePickerItems(
            $this->selectedEmployees($members->pluck('employee_id')->all()),
        ))->keyBy('employee_id');

        return $members
            ->map(static fn ($member): ?array => ($item = $items->get((int) $member['employee_id']))
                ? [...$item, 'role' => $member['role'] ?? '']
                : null)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * JSON payload for the employee picker (employee selection modal).
     */
    public function employeePickerPayload(Paginator $employees): array
    {
        return [
            'data' => $this->employeePickerItems($employees->items()),
            'has_more' => $employees->hasMorePages(),
        ];
    }

    public function departmentOptions()
    {
        return $this->departmentQuery->forSelect();
    }

    public function departmentForParentSelect(?Department $department = null)
    {
        return $this->departmentQuery->forParentSelect($department);
    }

    public function departmentOptionsWithPositions()
    {
        return $this->departmentQuery->forSelectWithPositions();
    }

    public function positionOptions()
    {
        return $this->positionQuery->forSelect();
    }

    public function roleTeamAssignmentOptions()
    {
        return $this->teamQuery->forSelectRoles();
    }

    public function roleProjectAssignmentOptions()
    {
        return $this->projectQuery->forSelectRoles();
    }

    public function teamOptions()
    {
        return $this->teamQuery->forSelect();
    }
}

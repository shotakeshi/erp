<?php

namespace App\Services\Shared;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use App\Queries\DepartmentQuery;
use App\Queries\EmployeeQuery;
use App\Queries\PositionQuery;
use App\Queries\ProjectQuery;
use App\Queries\TeamQuery;

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

    public function searchEmployees(string $search, array $excludedIds)
    {
        return $this->employeeQuery->searchEmployees($search, $excludedIds);
    }

    public function searchTeamEmployees(?Team $team, string $search, array $excludedIds)
    {
        return $this->employeeQuery->searchTeamEmployees($team, $search, $excludedIds);
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

    public function roleProjectMemberOptions()
    {
        return $this->projectQuery->forSelectRoles();
    }

    public function teamOptions()
    {
        return $this->teamQuery->forSelect();
    }
}

<?php

namespace App\Services\Shared;

use App\Models\Department;
use App\Queries\DepartmentQuery;
use App\Queries\EmployeeQuery;
use App\Queries\PositionQuery;
use App\Models\Employee;
use App\Queries\TeamQuery;

class FormOptionService
{
    public function __construct(
        protected EmployeeQuery $employeeQuery,
        protected DepartmentQuery $departmentQuery,
        protected PositionQuery $positionQuery,
        protected TeamQuery $teamQuery
    ) {}

    public function employeeOptions(?Employee $exceptEmployee = null)
    {
        return $this->employeeQuery->forSelect($exceptEmployee);
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

    public function roleAssignmentOptions()
    {
        return $this->teamQuery->forSelectRoles();
    }
}

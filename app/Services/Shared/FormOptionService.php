<?php

namespace App\Services\Shared;
use App\Models\Department;
use App\Models\Employee;
use App\Queries\DepartmentQuery;
use App\Queries\EmployeeQuery;

class FormOptionService
{
    public function __construct(
        protected EmployeeQuery $employeeQuery,
        protected DepartmentQuery $departmentQuery,
    ) {

    }

    public function employeeOptions(){
        return $this->employeeQuery->forSelect();
    }

    public function departmentOptions(){
        return $this->departmentQuery->forSelect();
    }

    public function departmentForParentSelect(?Department $department = null){
        return $this->departmentQuery->forParentSelect($department);
    }

    public function departmentOptionsWithPositions()
    {
        return $this->departmentQuery->forSelectWithPositions();
    }
}
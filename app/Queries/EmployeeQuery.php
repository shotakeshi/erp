<?php
namespace App\Queries;
use App\Models\Employee;

class EmployeeQuery
{
    public function forSelect()
    {
        return Employee::query()
            ->active()
            ->select([
                'employees.id',
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name',
            ])
            ->orderBy('id')
            ->get();
    }

    public function paginate(array $filters)
    {
        return Employee::query()
            ->filter($filters)
            ->paginate();
    }
}
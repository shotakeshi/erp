<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository
{
    public function getAll(): Collection
    {
        return Employee::orderBy('full_name')->get();
    }

    public function getForSelect(): Collection
    {
        return Employee::query()
            ->select([
                'id',
                'employee_id',
                'first_name',
                'last_name',
            ])
            ->get();
    }

    public function getManagers(): Collection
    {
        return Employee::query()
            ->select([
                'id',
                'employee_code',
                'full_name',
            ])
            ->where('is_manager', true)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return Employee::query()
            ->where('department_id', $departmentId)
            ->orderBy('full_name')
            ->get();
    }
}
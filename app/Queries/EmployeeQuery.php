<?php
namespace App\Queries;
use App\Models\Employee;

class EmployeeQuery
{
    public function forSelect(?Employee $exceptEmployee = null)
    {
        $query = Employee::query()
            ->active()
            ->select([
                'employees.id',
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name',
            ]);
         if ($exceptEmployee) {
             $excludedIds = collect([$exceptEmployee->id])
                 ->merge($this->getSubordinateIds($exceptEmployee))
                 ->unique()
                 ->values();

             $query->whereNotIn('id', $excludedIds);
         }
        return $query->orderBy('id')->get();
    }

    public function paginate(array $filters)
    {
        return Employee::query()
            ->filter($filters)
            ->paginate();
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
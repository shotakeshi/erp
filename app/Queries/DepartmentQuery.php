<?php
namespace App\Queries;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Department;
use App\Models\Employee;

class DepartmentQuery
{
    public function forSelect()
    {
        return Department::query()
            ->select([ 'id', 'name'])
            ->orderBy('id')
            ->get();
    }

    public function paginate(array $filters)
    {
        return Employee::query()
            ->filter($filters)
            ->paginate();
    }

    /**
     * Danh sách phòng ban cho dropdown khi chọn phòng ban cha.
     * List of departments for the dropdown when selecting a parent department.
     */
    public function forParentSelect(?Department $department = null): Collection
    {
        $query = Department::query()
            ->orderBy('name');

        if ($department) {
            $excludeIds = array_merge(
                [$department->id],
                $this->descendantIds($department)
            );

            $query->whereNotIn('id', $excludeIds);
        }

        return $query->get([
            'id',
            'name',
        ]);
    }

    public function descendantIds(Department $department): array
    {
        $ids = [];

        $department->loadMissing('children');

        foreach ($department->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }

        return $ids;
    }

    public function forSelectWithPositions()
    {
        return Department::query()
            ->with([
                'positions:id,department_id,name'
            ])
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);
    }
}
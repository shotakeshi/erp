<?php

namespace App\Queries;

use App\Filters\EmployeeFilter;
use App\Filters\ProjectFilter;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class ProjectQuery
{
    public const HISTORY_PAGE_NAME = 'history_page';

    public function __construct(
        private readonly ProjectFilter $projectFilter,
        private readonly EmployeeFilter $employeeFilter,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        $employee = new Employee;

        return $this->projectFilter
            ->apply(Project::query(), $filters)
            ->select([
                'id',
                'code',
                'name',
                'description',
                'project_avatar',
                'status',
                'project_url',
                'updated_at',
            ])
            ->withCount([
                'assignments as current_members_count' => static fn (Builder $query) => $query->currentAssignment(),
            ])
            ->with([
                'members' => static fn (BelongsToMany $query) => $query
                    ->select([
                        $employee->qualifyColumn('id'),
                        $employee->qualifyColumn('first_name'),
                        $employee->qualifyColumn('last_name'),
                        $employee->qualifyColumn('avatar'),
                    ])
                    ->orderByPivot('start_date')
                    ->limit(3),
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function paginateTrashed(array $filters): LengthAwarePaginator
    {
        return $this->projectFilter
            ->apply(Project::onlyTrashed(), $filters)
            ->select(['id', 'code', 'name', 'description', 'project_avatar', 'deleted_at'])
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Project member history.
     */
    public function assignmentHistories(Project $project): LengthAwarePaginator
    {
        return $project->assignments()
            ->select([
                'id',
                'project_id',
                'employee_id',
                'role',
                'start_date',
                'end_date',
                'is_current',
                'end_reason',
                'end_reason_note',
                'created_by',
                'ended_by',
            ])
            ->with(
                'employee:id,first_name,last_name,avatar,email,deleted_at',
                'createdBy:id,name',
                'endedBy:id,name',
            )
            ->orderByDesc('is_current')
            ->orderBy('id')
            ->paginate(20, pageName: self::HISTORY_PAGE_NAME)
            ->withQueryString();
    }

    public function forSelectRoles(): Collection
    {
        return ProjectAssignment::query()
            ->distinct()
            ->orderBy('role')
            ->pluck('role');
    }

    public function findForShow(Project $project): Project
    {
        return $project->load([
            'manager:id,first_name,last_name',
            'team:id,name',
            'department:id,name',
        ]);
    }

    /**
     * Get the list of members currently assigned to the project.
     */
    public function currentAssignments(Project $project, string $search = ''): LengthAwarePaginator
    {
        return $project->assignments()
            ->currentAssignment()
            ->when($search !== '', fn (Builder $query) => $query->whereHas(
                'employee', fn (Builder $employee) => $this->employeeFilter->searchEmployeeOption($employee, $search),
            ))
            ->select([
                'id',
                'project_id',
                'employee_id',
                'role',
                'start_date',
            ])
            ->with([
                'employee' => static fn (Relation $query) => $query
                    ->select([
                        'id',
                        'avatar',
                        'email',
                        'phone',
                        'employee_id',
                        'deleted_at',
                        'first_name',
                        'last_name',
                        'department_id',
                        'position_id',
                    ])
                    ->with([
                        'department:id,name',
                        'position:id,name',
                    ]),
            ])
            ->orderBy('start_date')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();
    }
}

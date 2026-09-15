<?php

namespace App\Queries;

use App\Filters\EmployeeFilter;
use App\Filters\TeamFilter;
use App\Models\Employee;
use App\Models\Team;
use App\Models\TeamAssignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class TeamQuery
{
    public function __construct(
        private readonly TeamFilter $teamFilter,
        private readonly EmployeeQuery $employeeQuery,
        private readonly EmployeeFilter $employeeFilter,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->paginateTeams(Team::query(), $filters, withPreviews: true);
    }

    public function paginateTrashed(array $filters): LengthAwarePaginator
    {
        return $this->paginateTeams(Team::onlyTrashed(), $filters);
    }

    public function forSelectRoles(): Collection
    {
        return TeamAssignment::query()
            ->distinct()
            ->pluck('role');
    }

    private function paginateTeams(
        Builder $query,
        array $filters,
        bool $withPreviews = false,
    ): LengthAwarePaginator {
        $query = $this->teamFilter
            ->apply($query, $filters)
            ->select([
                'id',
                'name',
                'code',
                'logo',
                'description',
            ]);

        if ($withPreviews) {
            $this->withCurrentAssignmentCounts($query);
            $this->withCardPeoplePreviews($query);
        }

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Load a small preview of people for each card on the Team index.
     */
    private function withCardPeoplePreviews(Builder $query): Builder
    {
        $employee = new Employee;
        $columns = [
            $employee->qualifyColumn('id'),
            $employee->qualifyColumn('first_name'),
            $employee->qualifyColumn('last_name'),
            $employee->qualifyColumn('avatar'),
        ];

        $preview = static function (BelongsToMany $relation) use ($columns): void {
            $relation->select($columns)
                ->orderByPivot('start_date')
                ->limit(3);
        };

        return $query->with([
            'members' => $preview,
        ]);
    }

    private function withCurrentAssignmentCounts(Builder $query): Builder
    {
        return $query->withCount([
            'assignments as current_members_count' => static fn (Builder $query) => $query->currentAssignment(),
        ]);
    }

    /**
     * Get the list of members currently assigned to the team.
     */
    public function currentMembers(Team $team, string $search = ''): LengthAwarePaginator
    {
        return $team->assignments()
            ->currentAssignment()
            ->when($search !== '', fn (Builder $query) => $query->whereHas(
                'employee', fn (Builder $employee) => $this->employeeFilter->searchTeamMember($employee, $search),
            ))
            ->select([
                'id',
                'team_id',
                'employee_id',
                'role',
                'start_date',
            ])
            ->with($this->assignmentEmployeeRelations())
            ->orderBy('start_date')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Team member history, filterable by current or past assignment.
     */
    public function memberHistory(Team $team, array $filters = []): LengthAwarePaginator
    {
        $filter = $filters['filter'] ?? 'all';

        return $team->assignments()
            ->select([
                'id',
                'team_id',
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
            ->with($this->assignmentHistoryRelations())
            ->when(
                $filter === 'current',
                static fn (Builder $query) => $query->currentAssignment()
            )
            ->when(
                $filter === 'past',
                static fn (Builder $query) => $query->pastAssignment()
            )
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Get the teams an employee is currently assigned to.
     */
    public function employeeCurrentTeams(Employee $employee): EloquentCollection
    {
        return $employee->teamAssignments()
            ->currentAssignment()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
            ])
            ->with([
                'team:id,name,code,deleted_at',
            ])
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * Employee team membership history.
     */
    public function employeeTeamHistory(Employee $employee): LengthAwarePaginator
    {
        return $employee->teamAssignments()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
                'end_date',
                'end_reason',
                'end_reason_note',
            ])
            ->with([
                'team:id,name,code,deleted_at',
            ])
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Declare employee relations to eager load for assignments.
     */
    private function assignmentEmployeeRelations(): array
    {
        $columns = [
            'id',
            'avatar',
            'email',
            'phone',
            'employee_id',
            'first_name',
            'last_name',
            'department_id',
            'position_id',
        ];

        return [
            'employee' => fn (Relation $query) => $query
                ->select($columns)->with([
                    'department:id,name',
                    'position:id,name',
                ]),
        ];
    }

    /**
     * Declare relations to eager load for assignment history.
     */
    private function assignmentHistoryRelations(): array
    {
        return [
            'employee:id,first_name,last_name,avatar,email',
            'createdBy:id,name',
            'endedBy:id,name',
        ];
    }
}

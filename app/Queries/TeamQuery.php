<?php

namespace App\Queries;

use App\Enums\TeamAssignmentRole;
use App\Filters\TeamFilter;
use App\Models\Employee;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class TeamQuery
{
    public function __construct(
        private readonly TeamFilter $teamFilter,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->paginateTeams(Team::query(), $filters, withPreviews: true);
    }

    public function paginateTrashed(array $filters): LengthAwarePaginator
    {
        return $this->paginateTeams(Team::onlyTrashed(), $filters);
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
            ])
            ->tap(fn (Builder $query): Builder => $this->withCurrentAssignmentCounts($query));

        if ($withPreviews) {
            $this->withCardPeoplePreviews($query);
        }

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Load a small, role-aware preview of people for each card on the Team index.
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
            'managers' => $preview,
        ]);
    }

    /**
     * Lấy chi tiết team cùng các member và manager đang được phân công.
     */
    public function detail(Team $team): Team
    {
        return Team::query()
            ->whereKey($team)
            ->with([
                'memberAssignments' => function (Relation $query): void {
                    $query->currentAssignment()
                        ->select([
                            'id',
                            'team_id',
                            'employee_id',
                            'start_date',
                        ])
                        ->with($this->assignmentEmployeeRelations())
                        ->orderBy('start_date')
                        ->orderBy('id');
                },
                'managerAssignments' => function (Relation $query): void {
                    $query->currentAssignment()
                        ->select([
                            'id',
                            'team_id',
                            'employee_id',
                            'start_date',
                        ])
                        ->with($this->assignmentEmployeeRelations())
                        ->orderBy('start_date')
                        ->orderBy('id');
                },
            ])
            ->firstOrFail();
    }

    /**
     * Lấy thông tin của team và số lượng member, manager hiện tại cho các tab.
     */
    public function detailForTabs(Team $team): Team
    {
        return $this->withCurrentAssignmentCounts(
            Team::query()
                ->whereKey($team)
                ->select([
                    'id',
                    'name',
                    'code',
                    'logo',
                    'description',
                ]),
        )->firstOrFail();
    }

    private function withCurrentAssignmentCounts(Builder $query): Builder
    {
        return $query->withCount([
            'memberAssignments as current_members_count' => static fn (Builder $query) => $query->currentAssignment(),

            'managerAssignments as current_managers_count' => static fn (Builder $query) => $query->currentAssignment(),
        ]);
    }

    /**
     * Lấy danh sách member đang được phân công vào team.
     */
    public function currentMembers(Team $team): EloquentCollection
    {
        return $team->memberAssignments()
            ->currentAssignment()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
            ])
            ->with($this->assignmentEmployeeRelations())
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * Lịch sử member của team, có thể lọc theo current hoặc past assignment.
     */
    public function memberHistory(Team $team, array $filters = []): LengthAwarePaginator
    {
        return $this->assignmentHistory($team, TeamAssignmentRole::MEMBER, $filters);
    }

    /**
     * Lịch sử manager của team, có thể lọc theo current hoặc past assignment.
     */
    public function managerHistory(Team $team, array $filters = []): LengthAwarePaginator
    {
        return $this->assignmentHistory($team, TeamAssignmentRole::MANAGER, $filters);
    }

    /**
     * Lịch sử assignment của team theo role, có thể lọc theo current hoặc past assignment.
     */
    private function assignmentHistory(
        Team $team,
        TeamAssignmentRole $role,
        array $filters = [],
    ): LengthAwarePaginator {
        $filter = $filters['filter'] ?? 'all';

        return $team->assignments()
            ->forRole($role)
            ->select([
                'id',
                'team_id',
                'employee_id',
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
     * Lấy danh sách manager đang được phân công vào team.
     */
    public function currentManagers(Team $team): EloquentCollection
    {
        return $team->managerAssignments()
            ->currentAssignment()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
            ])
            ->with($this->assignmentEmployeeRelations())
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * Lấy các team mà employee đang là member.
     */
    public function employeeCurrentTeams(Employee $employee): EloquentCollection
    {
        return $employee->teamMemberships()
            ->currentAssignment()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
            ])
            ->with([
                'team:id,name,code,logo,deleted_at',
            ])
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * Lịch sử membership team của employee.
     */
    public function employeeTeamHistory(Employee $employee): LengthAwarePaginator
    {
        return $employee->teamMemberships()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
                'end_date',
                'is_current',
                'end_reason',
                'end_reason_note',
                'created_by',
                'ended_by',
            ])
            ->with([
                'team:id,name,code,logo,deleted_at',
                'createdBy:id,name',
                'endedBy:id,name',
            ])
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Khai báo các quan hệ employee cần eager load cho assignment.
     */
    private function assignmentEmployeeRelations(): array
    {
        return [
            'employee' => static function (Relation $query): void {
                $query->select([
                    'id',
                    'user_id',
                    'employee_id',
                    'first_name',
                    'last_name',
                    'department_id',
                    'position_id',
                    'deleted_at',
                ])->with([
                    'user:id,status',
                    'department:id,name',
                    'position:id,name',
                ]);
            },
        ];
    }

    /**
     * Khai báo các quan hệ cần eager load cho lịch sử assignment.
     */
    private function assignmentHistoryRelations(): array
    {
        return [
            ...$this->assignmentEmployeeRelations(),
            'team:id,name,code,logo,deleted_at',
            'createdBy:id,name',
            'endedBy:id,name',
        ];
    }
}

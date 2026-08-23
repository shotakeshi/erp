<?php

namespace App\Queries;

use App\Enums\UserStatus;
use App\Filters\TeamFilter;
use App\Models\Employee;
use App\Models\Team;
use App\Models\TeamManager;
use App\Models\TeamMembership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\Relation;

class TeamQuery
{
    public function __construct(
        private readonly TeamFilter $teamFilter,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->paginateTeams(Team::query(), $filters);
    }

    public function paginateTrashed(array $filters): LengthAwarePaginator
    {
        return $this->paginateTeams(Team::onlyTrashed(), $filters);
    }

    private function paginateTeams(Builder $query, array $filters): LengthAwarePaginator
    {
        return $this->teamFilter
            ->apply($query, $filters)
            ->select([
                'id',
                'name',
                'code',
                'updated_at',
                'deleted_at',
            ])
            ->withCount([
                'memberships as current_members_count' => static function (Builder $query): void {
                    $query->currentAssignment();
                },
                'managerAssignments as current_managers_count' => static function (Builder $query): void {
                    $query->currentAssignment();
                },
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function detail(Team $team): Team
    {
        return Team::query()
            ->whereKey($team)
            ->with([
                'memberships' => function (Relation $query): void {
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

    public function currentMembers(Team $team): EloquentCollection
    {
        return $team->memberships()
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

    public function memberHistory(Team $team, array $filters = []): LengthAwarePaginator
    {
        $filter = $filters['filter'] ?? 'all';

        return $team->memberships()
            ->select([
                'id',
                'team_id',
                'employee_id',
                'start_date',
                'end_date',
                'is_current',
                'end_reason',
                'created_by',
                'ended_by',
            ])
            ->with($this->memberHistoryRelations())
            ->when($filter === 'current', static function (Builder $query): void {
                $query->currentAssignment();
            })
            ->when($filter === 'past', static function (Builder $query): void {
                $query->pastAssignment();
            })
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

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
                'team:id,name,code,deleted_at',
            ])
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();
    }

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
                'created_by',
                'ended_by',
            ])
            ->with([
                'team:id,name,code,deleted_at',
                'createdBy:id,name',
                'endedBy:id,name',
            ])
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function operationalEmployeesForTeams(iterable $teams): EloquentCollection
    {
        $teamIds = collect($teams)
            ->map(static function (Team|int $team): int {
                return $team instanceof Team ? (int) $team->getKey() : $team;
            })
            ->unique()
            ->values();

        if ($teamIds->isEmpty()) {
            return new EloquentCollection;
        }

        return Employee::query()
            ->select([
                'id',
                'user_id',
                'employee_id',
                'first_name',
                'last_name',
                'position_id',
            ])
            ->whereNull('deleted_at')
            ->whereIn('id', TeamMembership::query()
                ->currentAssignment()
                ->whereIn('team_id', Team::query()
                    ->whereKey($teamIds->all())
                    ->select('id'))
                ->select('employee_id'))
            ->active()
            ->with([
                'user:id,status',
                'position:id,name',
            ])
            ->distinct()
            ->orderBy('id')
            ->get();
    }

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
                    'position_id',
                    'deleted_at',
                ])->with([
                    'user:id,status',
                    'position:id,name',
                ]);
            },
        ];
    }

    private function memberHistoryRelations(): array
    {
        return [
            ...$this->assignmentEmployeeRelations(),
            'team:id,name,code,deleted_at',
            'createdBy:id,name',
            'endedBy:id,name',
        ];
    }
}

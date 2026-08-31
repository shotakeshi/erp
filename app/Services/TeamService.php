<?php

namespace App\Services;

use App\Enums\TeamAssignmentEndReason;
use App\Enums\TeamAssignmentRole;
use App\Enums\UserStatus;
use App\Models\Employee;
use App\Models\Team;
use App\Models\TeamAssignment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TeamService
{
    public function deleteTeam(Team $team, User $actor): void
    {
        DB::transaction(function () use ($team, $actor): void {
            $lockedTeam = $this->lockTeam($team);
            $endDate = CarbonImmutable::today();
            $assignments = $this->currentAssignmentsForTeam($lockedTeam);

            foreach ($assignments as $assignment) {
                if ($endDate->lt($assignment->start_date)) {
                    $this->throwValidationConflict('site.teams.conflicts.end_date_before_start_date');
                }
            }

            $this->closeAssignments(
                $assignments,
                $endDate,
                TeamAssignmentEndReason::TEAM_DELETED,
                $actor->getKey(),
            );

            $lockedTeam->delete();
        });
    }

    public function addAssignments(
        Team $team,
        array $employeeIds,
        string $startDate,
        User $actor,
        TeamAssignmentRole $role,
    ): Collection {
        $employeeIds = array_map('intval', $employeeIds);

        try {
            return DB::transaction(function () use ($team, $employeeIds, $startDate, $actor, $role): Collection {
                $actorId = $actor->getKey();
                $lockedTeam = $this->lockTeam($team);
                $resolvedStartDate = $this->resolveAssignmentDate($startDate);
                $employees = $this->eligibleEmployees($employeeIds);
                $assignmentHistory = $this->assignmentHistory(
                    $lockedTeam,
                    $employeeIds,
                    $role,
                )->groupBy('employee_id');

                foreach ($employees as $employee) {
                    $history = $assignmentHistory->get($employee->getKey()) ?? collect();
                    $this->canCreateCurrentAssignment($history, $resolvedStartDate);
                }

                $createdAssignments = collect();

                foreach ($employees as $employee) {
                    $createdAssignments->push(TeamAssignment::query()->create([
                        'team_id' => $lockedTeam->getKey(),
                        'employee_id' => $employee->getKey(),
                        'role' => $role,
                        'start_date' => $resolvedStartDate->toDateString(),
                        'end_date' => null,
                        'is_current' => true,
                        'end_reason' => null,
                        'end_reason_note' => null,
                        'created_by' => $actorId,
                        'ended_by' => null,
                    ]));
                }

                return $createdAssignments;
            });
        } catch (QueryException $e) {
            report($e);

            $this->throwValidationConflict('site.teams.conflicts.assignment_already_current');
        }
    }

    public function removeAssignment(
        Team $team,
        Employee $employee,
        string $endDate,
        User $actor,
        TeamAssignmentRole $role,
        ?string $endReasonNote,
    ): TeamAssignment {
        return DB::transaction(function () use ($team, $employee, $endDate, $actor, $role, $endReasonNote): TeamAssignment {
            $lockedTeam = $this->lockTeam($team);
            $resolvedEndDate = $this->resolveAssignmentDate($endDate);
            $history = $this->assignmentHistory(
                $lockedTeam,
                [$employee->getKey()],
                $role,
            );
            $currentAssignment = $history->firstWhere('is_current', true);;
            if ($currentAssignment === null) {
                $this->throwValidationConflict('site.teams.conflicts.assignment_not_current');
            }

            if ($resolvedEndDate->lt($currentAssignment->start_date)) {
                $this->throwValidationConflict('site.teams.conflicts.end_date_before_start_date');
            }

            $this->closeAssignments(
                collect([$currentAssignment]),
                $resolvedEndDate,
                TeamAssignmentEndReason::REMOVED,
                $actor->getKey(),
                $endReasonNote,
            );

            return $currentAssignment;
        });
    }

    private function closeAssignments(
        Collection $assignments,
        CarbonImmutable $endDate,
        TeamAssignmentEndReason $endReason,
        ?int $actorId,
        ?string $endReasonNote = null,
    ): void {
        foreach ($assignments as $assignment) {
            $assignment->update([
                'end_date' => $endDate,
                'is_current' => null,
                'end_reason' => $endReason,
                'end_reason_note' => $endReasonNote,
                'ended_by' => $actorId,
            ]);
        }
    }

    private function lockTeam(Team $team): Team
    {
        $lockedTeam = Team::withTrashed()
            ->whereKey($team->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedTeam->trashed()) {
            $this->throwValidationConflict('site.teams.conflicts.team_unavailable');
        }

        return $lockedTeam;
    }

    private function eligibleEmployees(array $employeeIds): EloquentCollection
    {
        $employees = Employee::withTrashed()
            ->whereIn('id', $employeeIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($employees->count() !== count($employeeIds)) {
            $this->throwValidationConflict('site.teams.conflicts.employee_not_eligible');
        }

        $users = User::query()
            ->whereIn('id', $employees->pluck('user_id')->filter())
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($employees as $employee) {
            if ($employee->trashed() || $users->get($employee->user_id)?->status !== UserStatus::ACTIVE) {
                $this->throwValidationConflict('site.teams.conflicts.employee_not_eligible');
            }
        }

        return $employees;
    }

    private function assignmentHistory(
        Team $team,
        array $employeeIds,
        TeamAssignmentRole $role,
    ): EloquentCollection {
        return $team->assignments()
            ->forRole($role)
            ->whereIn('employee_id', $employeeIds)
            ->orderBy('employee_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function currentAssignmentsForTeam(Team $team): EloquentCollection
    {
        return $team->assignments()
            ->currentAssignment()
            ->orderBy('employee_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function canCreateCurrentAssignment(Collection $history, CarbonImmutable $startDate): void
    {
        foreach ($history as $assignment) {
            if ($assignment->end_date === null && $assignment->is_current) {
                $this->throwValidationConflict('site.teams.conflicts.assignment_already_current');
            }

            if ($assignment->end_date !== null && $startDate->lt($assignment->end_date)) {
                $this->throwValidationConflict('site.teams.conflicts.assignment_interval_overlaps');
            }
        }
    }

    private function resolveAssignmentDate(string $date): CarbonImmutable
    {
        $date = CarbonImmutable::parse($date);

        if ($date->gt(CarbonImmutable::today(config('app.timezone')))) {
            $this->throwValidationConflict('site.teams.conflicts.assignment_date_in_future');
        }

        return $date;
    }

    private function throwValidationConflict(string $translationKey): never
    {
        throw ValidationException::withMessages([
            'team' => __($translationKey),
        ])->status(Response::HTTP_CONFLICT);
    }
}

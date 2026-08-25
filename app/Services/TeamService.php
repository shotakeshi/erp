<?php

namespace App\Services;

use App\Enums\TeamManagerEndReason;
use App\Enums\TeamMembershipEndReason;
use App\Enums\UserStatus;
use App\Models\Employee;
use App\Models\Team;
use App\Models\TeamManager;
use App\Models\TeamMembership;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DomainException;
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
            $actorId = $this->actorId($actor);
            $lockedTeam = $this->lockTeam($team);
            $effectiveDate = $this->today();
            $memberships = $this->lockCurrentAssignmentsForTeam(TeamMembership::class, $lockedTeam);
            $managerAssignments = $this->lockCurrentAssignmentsForTeam(TeamManager::class, $lockedTeam);

            $this->ensureEffectiveDateIsNotBeforeAssignments(
                $effectiveDate,
                $memberships,
                $managerAssignments,
            );

            $this->closeAssignments(
                $memberships,
                $effectiveDate,
                TeamMembershipEndReason::TEAM_DELETED,
                $actorId,
            );

            $this->closeAssignments(
                $managerAssignments,
                $effectiveDate,
                TeamManagerEndReason::TEAM_DELETED,
                $actorId,
            );

            $lockedTeam->delete();
        });
    }

    private function closeAssignments(
        Collection $assignments,
        CarbonImmutable $effectiveDate,
        TeamMembershipEndReason|TeamManagerEndReason $endReason,
        int $actorId,
    ): void {
        foreach ($assignments as $assignment) {
            $assignment->update([
                'end_date' => $effectiveDate,
                'is_current' => null,
                'end_reason' => $endReason,
                'ended_by' => $actorId,
            ]);
        }
    }

    public function addMembers(Team $team, array $employeeIds, string $startDate, User $actor): Collection
    {
        /** @var Collection<int, TeamMembership> $memberships */
        $memberships = $this->addAssignments(
            TeamMembership::class,
            $team,
            $employeeIds,
            $startDate,
            $actor,
        );

        return $memberships;
    }

    public function removeMember(Team $team, Employee $employee, string $endDate, User $actor): TeamMembership
    {
        /** @var TeamMembership $membership */
        $membership = $this->removeAssignment(
            TeamMembership::class,
            $team,
            $employee,
            $endDate,
            $actor,
            TeamMembershipEndReason::MANUAL_REMOVE,
        );

        return $membership;
    }

    public function addManagers(Team $team, array $employeeIds, string $startDate, User $actor): Collection
    {
        /** @var Collection<int, TeamManager> $managerAssignments */
        $managerAssignments = $this->addAssignments(
            TeamManager::class,
            $team,
            $employeeIds,
            $startDate,
            $actor,
        );

        return $managerAssignments;
    }

    public function removeManager(Team $team, Employee $employee, string $endDate, User $actor): TeamManager
    {
        /** @var TeamManager $managerAssignment */
        $managerAssignment = $this->removeAssignment(
            TeamManager::class,
            $team,
            $employee,
            $endDate,
            $actor,
            TeamManagerEndReason::REMOVED,
        );

        return $managerAssignment;
    }

    /**
     * @param  class-string<TeamMembership|TeamManager>  $assignmentModel
     */
    private function addAssignments(
        string $assignmentModel,
        Team $team,
        array $employeeIds,
        string $startDate,
        User $actor,
    ): Collection {
        $employeeIds = $this->normalizeEmployeeIds($employeeIds);

        try {
            return DB::transaction(function () use (
                $assignmentModel,
                $team,
                $employeeIds,
                $startDate,
                $actor,
            ): Collection {
                $actorId = $this->actorId($actor);
                $lockedTeam = $this->lockTeam($team);
                $resolvedStartDate = $this->resolveAssignmentDate($startDate);
                $employees = $this->lockEligibleEmployees($employeeIds);
                $assignmentHistory = $this->lockAssignmentHistory(
                    $assignmentModel,
                    $lockedTeam,
                    $employeeIds,
                )->groupBy('employee_id');

                foreach ($employees as $employee) {
                    $history = $assignmentHistory->get($employee->getKey(), new Collection);
                    $this->ensureCanOpenAssignment($history, $resolvedStartDate);
                }

                $createdAssignments = new Collection;

                foreach ($employees as $employee) {
                    $attributes = [
                        'team_id' => $lockedTeam->getKey(),
                        'employee_id' => $employee->getKey(),
                        'start_date' => $resolvedStartDate->toDateString(),
                        'end_date' => null,
                        'is_current' => true,
                        'end_reason' => null,
                        'created_by' => $actorId,
                        'ended_by' => null,
                    ];

                    $createdAssignments->push($assignmentModel::query()->create($attributes));
                }

                return $createdAssignments;
            });
        } catch (QueryException $exception) {
            if ($this->isDuplicateCurrentAssignment($exception, $assignmentModel)) {
                $this->throwConflict('site.teams.conflicts.assignment_already_current');
            }

            throw $exception;
        }
    }

    private function removeAssignment(
        string $assignmentModel,
        Team $team,
        Employee $employee,
        string $endDate,
        User $actor,
        TeamMembershipEndReason|TeamManagerEndReason $endReason,
    ): TeamMembership|TeamManager {
        return DB::transaction(function () use ($assignmentModel, $team, $employee, $endDate, $actor, $endReason): TeamMembership|TeamManager {
            $actorId = $this->actorId($actor);
            $lockedTeam = $this->lockTeam($team);
            $resolvedEndDate = $this->resolveAssignmentDate($endDate);
            $history = $this->lockAssignmentHistory(
                $assignmentModel,
                $lockedTeam,
                [(int) $employee->getKey()],
            );
            $currentAssignment = $history->first(
                static fn (TeamMembership|TeamManager $assignment): bool => $assignment->end_date === null
                    && $assignment->is_current,
            );

            if ($currentAssignment === null) {
                $this->throwConflict('site.teams.conflicts.assignment_not_current');
            }

            $this->ensureCanCloseAssignment($currentAssignment, $history, $resolvedEndDate);

            $attributes = [
                'end_date' => $resolvedEndDate,
                'is_current' => null,
                'end_reason' => $endReason,
                'ended_by' => $actorId,
            ];

            $currentAssignment->update($attributes);

            return $currentAssignment;
        });
    }

    private function lockTeam(Team $team): Team
    {
        $lockedTeam = Team::withTrashed()
            ->whereKey($team->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedTeam->trashed()) {
            $this->throwConflict('site.teams.conflicts.team_unavailable');
        }

        return $lockedTeam;
    }

    private function lockEligibleEmployees(array $employeeIds): EloquentCollection
    {
        $employees = Employee::withTrashed()
            ->whereIn('id', $employeeIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($employees->count() !== count($employeeIds)) {
            $this->throwConflict('site.teams.conflicts.employee_not_eligible');
        }

        $userIds = $employees->pluck('user_id')
            ->filter()
            ->sort()
            ->values()
            ->all();
        $users = User::query()
            ->whereIn('id', $userIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($employees as $employee) {
            $user = $employee->user_id === null
                ? null
                : $users->get($employee->user_id);

            if ($employee->trashed() || $user === null || $user->status !== UserStatus::ACTIVE) {
                $this->throwConflict('site.teams.conflicts.employee_not_eligible');
            }
        }

        return $employees;
    }

    /**
     * @param  class-string<TeamMembership|TeamManager>  $assignmentModel
     */
    private function lockAssignmentHistory(
        string $assignmentModel,
        Team $team,
        array $employeeIds,
    ): EloquentCollection {
        return $assignmentModel::query()
            ->where('team_id', $team->getKey())
            ->whereIn('employee_id', $employeeIds)
            ->orderBy('employee_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    /**
     * @param  class-string<TeamMembership|TeamManager>  $assignmentModel
     */
    private function lockCurrentAssignmentsForTeam(string $assignmentModel, Team $team): EloquentCollection
    {
        return $assignmentModel::query()
            ->where('team_id', $team->getKey())
            ->currentAssignment()
            ->orderBy('employee_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function ensureCanOpenAssignment(Collection $history, CarbonImmutable $startDate): void
    {
        foreach ($history as $assignment) {
            if ($assignment->end_date === null && $assignment->is_current) {
                $this->throwConflict('site.teams.conflicts.assignment_already_current');
            }

            if ($this->intervalsOverlap(
                $startDate,
                null,
                $this->asApplicationDate($assignment->start_date),
                $assignment->end_date === null ? null : $this->asApplicationDate($assignment->end_date),
            )) {
                $this->throwConflict('site.teams.conflicts.assignment_interval_overlaps');
            }
        }
    }

    private function ensureCanCloseAssignment(
        TeamMembership|TeamManager $currentAssignment,
        EloquentCollection $history,
        CarbonImmutable $endDate,
    ): void {
        $startDate = $this->asApplicationDate($currentAssignment->start_date);

        if ($endDate->lt($startDate)) {
            $this->throwConflict('site.teams.conflicts.end_date_before_start_date');
        }

        foreach ($history as $assignment) {
            if ($assignment->is($currentAssignment)) {
                continue;
            }

            if ($this->intervalsOverlap(
                $startDate,
                $endDate,
                $this->asApplicationDate($assignment->start_date),
                $assignment->end_date === null ? null : $this->asApplicationDate($assignment->end_date),
            )) {
                $this->throwConflict('site.teams.conflicts.assignment_interval_overlaps');
            }
        }
    }

    private function ensureEffectiveDateIsNotBeforeAssignments(
        CarbonImmutable $effectiveDate,
        EloquentCollection $memberships,
        EloquentCollection $managerAssignments,
    ): void {
        foreach ($memberships->concat($managerAssignments) as $assignment) {
            if ($effectiveDate->lt($this->asApplicationDate($assignment->start_date))) {
                $this->throwConflict('site.teams.conflicts.end_date_before_start_date');
            }
        }
    }

    private function intervalsOverlap(
        CarbonImmutable $candidateStartDate,
        ?CarbonImmutable $candidateEndDate,
        CarbonImmutable $existingStartDate,
        ?CarbonImmutable $existingEndDate,
    ): bool {
        return ($candidateEndDate === null || $existingStartDate->lte($candidateEndDate))
            && ($existingEndDate === null || $existingEndDate->gte($candidateStartDate));
    }

    private function normalizeEmployeeIds(array $employeeIds): array
    {
        $normalizedEmployeeIds = array_map(
            static fn (int|string $employeeId): int => (int) $employeeId,
            $employeeIds,
        );

        sort($normalizedEmployeeIds);

        if (count($normalizedEmployeeIds) !== count(array_unique($normalizedEmployeeIds))) {
            throw new DomainException('Employee IDs must be distinct.');
        }

        return $normalizedEmployeeIds;
    }

    private function resolveAssignmentDate(string $date): CarbonImmutable
    {
        $resolvedDate = $this->asApplicationDate($date);

        if ($resolvedDate->gt($this->today())) {
            $this->throwConflict('site.teams.conflicts.assignment_date_in_future');
        }

        return $resolvedDate;
    }

    private function asApplicationDate(CarbonInterface|string $date): CarbonImmutable
    {
        $timezone = config('app.timezone');

        if ($date instanceof CarbonInterface) {
            return CarbonImmutable::instance($date)
                ->setTimezone($timezone)
                ->startOfDay();
        }

        return CarbonImmutable::parse($date, $timezone)->startOfDay();
    }

    private function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('app.timezone'))->startOfDay();
    }

    private function actorId(User $actor): int
    {
        if (! $actor->exists || $actor->getKey() === null) {
            throw new DomainException('The team assignment actor must be a persisted user.');
        }

        return (int) $actor->getKey();
    }

    private function isDuplicateCurrentAssignment(QueryException $exception, string $assignmentModel): bool
    {
        $message = $exception->getMessage();
        $table = (new $assignmentModel)->getTable();

        return str_contains($message, $table)
            && (
                str_contains($message, 'UNIQUE constraint failed')
                || str_contains($message, 'Duplicate entry')
                || str_contains($message, 'duplicate key')
            );
    }

    private function throwConflict(string $translationKey): never
    {
        throw ValidationException::withMessages([
            'team' => __($translationKey),
        ])->status(Response::HTTP_CONFLICT);
    }
}

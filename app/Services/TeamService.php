<?php

namespace App\Services;

use App\Enums\TeamAssignmentEndReason;
use App\Models\Employee;
use App\Models\Team;
use App\Models\TeamAssignment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeamService extends BaseService
{
    public function createTeam(array $teamAttributes, array $members, User $actor): void
    {
        DB::transaction(function () use ($teamAttributes, $members, $actor) {
            if ($members) {
                $this->eligibleEmployees(array_column($members, 'employee_id'));
            }

            $actorId = $actor->getKey();
            $startDate = $this->today()->toDateString();

            $team = Team::query()->create($teamAttributes);
            $assignments = array_map(
                static fn (array $member): array => [
                    'employee_id' => (int) $member['employee_id'],
                    'role' => filled($member['role'] ?? null) ? trim($member['role']) : 'member',
                    'start_date' => $startDate,
                    'is_current' => true,
                    'created_by' => $actorId,
                ],
                $members,
            );

            $team->assignments()->createMany($assignments);
        });
    }

    public function updateTeam(Team $team, array $teamAttributes, array $members): void
    {
        DB::transaction(function () use ($team, $teamAttributes, $members): void {
            $assignments = $this->currentAssignmentsForTeam($team)->keyBy('id');

            foreach ($members as $member) {
                $assignment = $assignments->get($member['assignment_id']);

                if ($assignment === null) {
                    $this->fail(__('site.teams.conflicts.assignment_not_current'));
                }

                $assignment->update([
                    'role' => filled($member['role'] ?? null) ? trim($member['role']) : 'member',
                ]);
            }

            $team->update($teamAttributes);
        });
    }

    public function deleteTeam(Team $team, User $actor): void
    {
        DB::transaction(function () use ($team, $actor): void {
            $lockedTeam = $this->lockTeam($team);
            $assignments = $this->currentAssignmentsForTeam($lockedTeam);
            $endDate = $this->today();

            foreach ($assignments as $assignment) {
                if ($endDate->lt($assignment->start_date)) {
                    $this->fail(__('site.teams.conflicts.end_date_before_start_date'));
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
        User $actor,
    ): void {
        $employeeIds = array_map('intval', $employeeIds);

        DB::transaction(function () use ($team, $employeeIds, $actor): void {
            $actorId = $actor->getKey();
            $lockedTeam = $this->lockTeam($team);
            $resolvedStartDate = $this->today();
            $employees = $this->eligibleEmployees($employeeIds);
            $this->validateAssignmentHistory($lockedTeam, $employeeIds, $resolvedStartDate);

            foreach ($employees as $employee) {
                TeamAssignment::query()->create([
                    'team_id' => $lockedTeam->getKey(),
                    'employee_id' => $employee->getKey(),
                    'role' => 'member',
                    'start_date' => $resolvedStartDate->toDateString(),
                    'end_date' => null,
                    'is_current' => true,
                    'end_reason' => null,
                    'end_reason_note' => null,
                    'created_by' => $actorId,
                    'ended_by' => null,
                ]);
            }

        });
    }

    public function removeAssignment(
        Team $team,
        Employee $employee,
        string $endDate,
        User $actor,
        ?string $endReasonNote,
    ): void {
        DB::transaction(function () use ($team, $employee, $endDate, $actor, $endReasonNote): void {
            $lockedTeam = $this->lockTeam($team);
            $resolvedEndDate = $this->resolveAssignmentDate($endDate);
            $currentAssignment = $lockedTeam->assignments()
                ->where('employee_id', $employee->getKey())
                ->currentAssignment()
                ->lockForUpdate()
                ->first();
            if ($currentAssignment === null) {
                $this->fail(__('site.teams.conflicts.assignment_not_current'));
            }

            if ($resolvedEndDate->lt($currentAssignment->start_date)) {
                $this->fail(__('site.teams.conflicts.end_date_before_start_date'));
            }

            $this->closeAssignments(
                collect([$currentAssignment]),
                $resolvedEndDate,
                TeamAssignmentEndReason::REMOVED,
                $actor->getKey(),
                $endReasonNote,
            );

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
            $this->fail(__('site.teams.conflicts.team_unavailable'));
        }

        return $lockedTeam;
    }

    private function eligibleEmployees(array $employeeIds): EloquentCollection
    {
        $employees = Employee::query()
            ->whereIn('id', $employeeIds)
            ->active()
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($employees->count() !== count($employeeIds)) {
            $this->fail(__('site.teams.conflicts.employee_not_eligible'));
        }

        return $employees;
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

    private function validateAssignmentHistory(Team $team, array $employeeIds, CarbonImmutable $startDate): void
    {
        $assignments = $team->assignments()->whereIn('employee_id', $employeeIds);

        if ((clone $assignments)->currentAssignment()->lockForUpdate()->first(['id']) !== null) {
            $this->fail(__('site.teams.conflicts.assignment_already_current'));
        }

        if ((clone $assignments)->where('end_date', '>', $startDate->endOfDay()->toDateString())
            ->lockForUpdate()->first(['id']) !== null) {
            $this->fail(__('site.teams.conflicts.assignment_interval_overlaps'));
        }
    }

    private function resolveAssignmentDate(string $date): CarbonImmutable
    {
        $date = CarbonImmutable::parse($date);

        if ($date->gt($this->today())) {
            $this->fail(__('site.teams.conflicts.assignment_date_in_future'));
        }

        return $date;
    }

    private function today(): CarbonImmutable
    {
        return CarbonImmutable::today(config('app.timezone'));
    }
}

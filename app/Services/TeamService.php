<?php

namespace App\Services;

use App\Enums\TeamAssignmentEndReason;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class TeamService extends BaseService
{
    private const DEFAULT_ROLE = 'member';

    public function createTeam(array $teamAttributes, array $members, User $actor): void
    {
        DB::transaction(function () use ($teamAttributes, $members, $actor) {
            $this->validateEmployees(array_column($members, 'employee_id'));

            $team = Team::query()->create($teamAttributes);
            $team->assignments()->createMany(array_map(
                static fn (array $member): array => [
                    'employee_id' => (int) $member['employee_id'],
                    'role' => $member['role'] ?? self::DEFAULT_ROLE,
                    'start_date' => today()->toDateString(),
                    'is_current' => true,
                    'created_by' => $actor->getKey(),
                ],
                $members,
            ));
        });
    }

    public function updateTeam(Team $team, array $teamAttributes, array $members): void
    {
        DB::transaction(function () use ($team, $teamAttributes, $members): void {
            $assignments = $team->assignments()->currentAssignment()->get()->keyBy('id');

            foreach ($members as $member) {
                $assignment = $assignments->get($member['assignment_id']);
                if ($assignment === null) {
                    $this->fail(__('site.teams.conflicts.assignment_not_current'));
                }

                $assignment->update(['role' => $member['role'] ?? self::DEFAULT_ROLE]);
            }

            $team->update($teamAttributes);
        });
    }

    public function addAssignments(Team $team, array $employeeIds, User $actor): void
    {
        DB::transaction(function () use ($team, $employeeIds, $actor): void {
            $employeeIds = array_map('intval', $employeeIds);
            $this->validateEmployees($employeeIds);

            if ($team->assignments()->currentAssignment()->whereIn('employee_id', $employeeIds)->exists()) {
                $this->fail(__('site.teams.conflicts.assignment_already_current'));
            }

            $team->assignments()->createMany(array_map(
                static fn (int $employeeId): array => [
                    'employee_id' => $employeeId,
                    'role' => self::DEFAULT_ROLE,
                    'start_date' => today()->toDateString(),
                    'is_current' => true,
                    'created_by' => $actor->getKey(),
                ],
                $employeeIds,
            ));
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
            $endDate = CarbonImmutable::parse($endDate)->startOfDay();
            $assignment = $team->assignments()
                ->currentAssignment()
                ->where('employee_id', $employee->getKey())
                ->first();

            if ($assignment === null) {
                $this->fail(__('site.teams.conflicts.assignment_not_current'));
            }

            if ($endDate->lt($assignment->start_date)) {
                $this->fail(__('site.teams.conflicts.end_date_before_start_date'));
            }

            $assignment->update([
                'end_date' => $endDate,
                'is_current' => null,
                'end_reason' => TeamAssignmentEndReason::REMOVED,
                'end_reason_note' => $endReasonNote,
                'ended_by' => $actor->getKey(),
            ]);
        });
    }

    private function validateEmployees(array $employeeIds): void
    {
        if ($employeeIds === []) {
            return;
        }

        $activeCount = Employee::query()
            ->whereIn('id', $employeeIds)
            ->active()
            ->count();

        if ($activeCount !== count($employeeIds)) {
            $this->fail(__('site.teams.conflicts.employee_not_eligible'));
        }
    }
}

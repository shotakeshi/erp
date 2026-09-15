<?php

namespace App\Services;

use App\Enums\TeamAssignmentEndReason;
use App\Enums\UserStatus;
use App\Models\Employee;
use App\Models\TeamAssignment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class EmployeeLifecycleService extends BaseService
{
    /**
     * Transition user status and close current team assignments.
     */
    public function transitionUserStatus(
        User $targetUser,
        UserStatus $targetStatus,
        User $actor,
    ): User {
        return DB::transaction(function () use (
            $targetUser,
            $targetStatus,
            $actor,
        ): User {
            $targetUser->refresh();

            if (! $targetUser->status->canTransitionTo($targetStatus)) {
                $this->fail(__('site.teams.conflicts.invalid_status_transition'));
            }

            if ($targetStatus === UserStatus::TERMINATED) {
                $employee = $targetUser->employee()->first();

                if ($employee) {
                    $this->closeAssignments(
                        $employee,
                        TeamAssignmentEndReason::EMPLOYEE_TERMINATED,
                        $actor->getKey(),
                        today(),
                    );
                }
            }

            $targetUser->update([
                'status' => $targetStatus,
            ]);

            return $targetUser;
        });
    }

    /**
     * Soft delete employee and close current assignments.
     */
    public function softDeleteEmployee(
        Employee $employee,
        User $actor,
    ): Employee {
        return DB::transaction(function () use ($employee, $actor): Employee {
            $employee = Employee::withTrashed()->whereKey($employee->getKey())->firstOrFail();
            if ($employee->trashed()) {
                return $employee;
            }

            $this->closeAssignments(
                $employee,
                TeamAssignmentEndReason::EMPLOYEE_DELETED,
                $actor->getKey(),
                today(),
            );

            $employee->delete();

            return $employee;
        });
    }

    private function closeAssignments(
        Employee $employee,
        TeamAssignmentEndReason $endReason,
        int $actorId,
        CarbonInterface $endDate,
    ): void {
        $assignments = $employee->teamAssignments()
            ->currentAssignment()
            ->get();

        if ($assignments->contains(
            fn (TeamAssignment $assignment) => $endDate->lt($assignment->start_date)
        )) {
            $this->fail(__('site.teams.conflicts.end_date_before_start_date'));
        }

        // Close assignments and record lifecycle audit data.
        foreach ($assignments as $assignment) {
            $assignment->update([
                'end_date' => $endDate,
                'is_current' => null,
                'end_reason' => $endReason,
                'end_reason_note' => null,
                'ended_by' => $actorId,
            ]);
        }
    }
}

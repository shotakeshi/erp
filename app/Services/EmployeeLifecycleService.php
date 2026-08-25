<?php

namespace App\Services;

use App\Enums\TeamManagerEndReason;
use App\Enums\TeamMembershipEndReason;
use App\Enums\UserStatus;
use App\Models\Employee;
use App\Models\TeamManager;
use App\Models\TeamMembership;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeLifecycleService
{
    public function transitionUserStatus(
        User $user,
        UserStatus $targetStatus,
        ?User $actor = null,
        ?CarbonInterface $effectiveDate = null,
    ): User {
        return DB::transaction(function () use ($user, $targetStatus, $actor, $effectiveDate): User {
            $employee = Employee::withTrashed()
                ->where('user_id', $user->getKey())
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedUser->status->canTransitionTo($targetStatus)) {
                throw new DomainException('The requested user status transition is not allowed.');
            }

            if ($employee !== null) {
                $endReasons = $this->assignmentEndReasonsFor($targetStatus);

                if ($endReasons !== null) {
                    [$membershipEndReason, $managerEndReason] = $endReasons;

                    $this->closeCurrentAssignments(
                        $employee,
                        $membershipEndReason,
                        $managerEndReason,
                        $this->actorId($actor),
                        $this->resolveEffectiveDate($effectiveDate),
                    );
                } else {
                    $this->lockCurrentAssignments($employee);
                }
            }

            $lockedUser->status = $targetStatus;
            $lockedUser->save();

            return $lockedUser;
        });
    }

    public function softDeleteEmployee(
        Employee $employee,
        ?User $actor = null,
        ?CarbonInterface $effectiveDate = null,
    ): Employee {
        return DB::transaction(function () use ($employee, $actor, $effectiveDate): Employee {
            $lockedEmployee = Employee::withTrashed()
                ->whereKey($employee->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedEmployee->trashed()) {
                return $lockedEmployee;
            }

            $linkedUser = null;

            if ($lockedEmployee->user_id !== null) {
                $linkedUser = User::query()
                    ->whereKey($lockedEmployee->user_id)
                    ->lockForUpdate()
                    ->first();
            }

            if ($linkedUser !== null) {
                $this->closeCurrentAssignments(
                    $lockedEmployee,
                    TeamMembershipEndReason::EMPLOYEE_DELETED,
                    TeamManagerEndReason::EMPLOYEE_DELETED,
                    $this->actorId($actor),
                    $this->resolveEffectiveDate($effectiveDate),
                );
            } else {
                $this->lockCurrentAssignments($lockedEmployee);
            }

            $lockedEmployee->delete();

            return $lockedEmployee;
        });
    }

    private function closeCurrentAssignments(
        Employee $employee,
        TeamMembershipEndReason $membershipEndReason,
        TeamManagerEndReason $managerEndReason,
        ?int $actorId,
        CarbonImmutable $effectiveDate,
    ): void {
        [$memberships, $managerAssignments] = $this->lockCurrentAssignments($employee);

        $this->ensureEffectiveDateIsValid(
            $effectiveDate,
            $memberships,
            $managerAssignments,
        );

        $this->closeAssignments($memberships, $membershipEndReason, $actorId, $effectiveDate);
        $this->closeAssignments($managerAssignments, $managerEndReason, $actorId, $effectiveDate);
    }

    /**
     * @return array{Collection<int, TeamMembership>, Collection<int, TeamManager>}
     */
    private function lockCurrentAssignments(Employee $employee): array
    {
        return [
            $this->lockCurrentAssignmentsFor(TeamMembership::class, $employee),
            $this->lockCurrentAssignmentsFor(TeamManager::class, $employee),
        ];
    }

    /**
     * @param  class-string<TeamMembership|TeamManager>  $assignmentModel
     * @return Collection<int, TeamMembership|TeamManager>
     */
    private function lockCurrentAssignmentsFor(string $assignmentModel, Employee $employee): Collection
    {
        return $assignmentModel::query()
            ->where('employee_id', $employee->getKey())
            ->currentAssignment()
            ->orderBy('team_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function closeAssignments(
        Collection $assignments,
        TeamMembershipEndReason|TeamManagerEndReason $endReason,
        ?int $actorId,
        CarbonImmutable $effectiveDate,
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

    /**
     * @param  Collection<int, TeamMembership>  $memberships
     * @param  Collection<int, TeamManager>  $managerAssignments
     */
    private function ensureEffectiveDateIsValid(
        CarbonImmutable $effectiveDate,
        Collection $memberships,
        Collection $managerAssignments,
    ): void {
        foreach ($memberships->concat($managerAssignments) as $assignment) {
            if ($effectiveDate->lt($assignment->start_date)) {
                throw new DomainException('The effective date cannot be earlier than a current assignment start date.');
            }
        }
    }

    /**
     * @return array{TeamMembershipEndReason, TeamManagerEndReason}|null
     */
    private function assignmentEndReasonsFor(UserStatus $targetStatus): ?array
    {
        return match ($targetStatus) {
            UserStatus::INACTIVE => [
                TeamMembershipEndReason::EMPLOYEE_INACTIVATED,
                TeamManagerEndReason::EMPLOYEE_INACTIVATED,
            ],
            UserStatus::TERMINATED => [
                TeamMembershipEndReason::EMPLOYEE_TERMINATED,
                TeamManagerEndReason::TERMINATED,
            ],
            default => null,
        };
    }

    private function actorId(?User $actor): ?int
    {
        if ($actor === null) {
            return null;
        }

        if (! $actor->exists || $actor->getKey() === null) {
            throw new DomainException('The lifecycle actor must be a persisted user.');
        }

        return (int) $actor->getKey();
    }

    private function resolveEffectiveDate(?CarbonInterface $effectiveDate): CarbonImmutable
    {
        $timezone = config('app.timezone');
        $resolvedDate = $effectiveDate === null
            ? CarbonImmutable::now($timezone)
            : CarbonImmutable::instance($effectiveDate)->setTimezone($timezone);

        $resolvedDate = $resolvedDate->startOfDay();

        if ($resolvedDate->isFuture()) {
            throw new DomainException('The effective date cannot be in the future.');
        }

        return $resolvedDate;
    }
}

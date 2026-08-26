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
    /**
     * Chuyển User status và đóng current team assignments khi cần.
     */
    public function transitionUserStatus(
        User $user,
        UserStatus $targetStatus,
        ?User $actor = null,
        ?CarbonInterface $effectiveDate = null,
    ): User {
        return DB::transaction(function () use (
            $user,
            $targetStatus,
            $actor,
            $effectiveDate
        ): User {
            // Lock Employee trước để đồng bộ với các thao tác liên quan đến assignment.
            $employee = Employee::withTrashed()
                ->where('user_id', $user->getKey())
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            // Lock User và lấy status mới nhất trước khi kiểm tra transition.
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // Chỉ cho phép các transition được định nghĩa trong UserStatus.
            if (! $lockedUser->status->canTransitionTo($targetStatus)) {
                throw new DomainException(__('exceptions.employee_lifecycle.invalid_status_transition'));
            }

            if ($employee !== null) {
                // Chỉ định status cần đóng assignment.
                $endReasons = $this->assignmentEndReasonsFor($targetStatus);

                if ($endReasons !== null) {
                    [$membershipEndReason, $managerEndReason] = $endReasons;

                    // Đóng cả membership và manager assignment cùng với status transition.
                    $this->closeCurrentAssignments(
                        $employee,
                        $membershipEndReason,
                        $managerEndReason,
                        $this->actorId($actor),
                        $this->resolveEffectiveDate($effectiveDate),
                    );
                }
            }

            // Chỉ lưu status khi toàn bộ xử lý assignment đã thành công.
            $lockedUser->status = $targetStatus;
            $lockedUser->save();

            return $lockedUser;
        });
    }

    /**
     * Soft delete employee và đóng current assignments khi có linked User.
     */
    public function softDeleteEmployee(
        Employee $employee,
        ?User $actor = null,
        ?CarbonInterface $effectiveDate = null,
    ): Employee {
        return DB::transaction(function () use ($employee, $actor, $effectiveDate): Employee {
            // Lock Employee để tránh xử lý trùng với thao tác lifecycle khác.
            $lockedEmployee = Employee::withTrashed()
                ->whereKey($employee->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // Giữ thao tác idempotent nếu Employee đã bị soft delete trước đó.
            if ($lockedEmployee->trashed()) {
                return $lockedEmployee;
            }

            $linkedUser = null;

            if ($lockedEmployee->user_id !== null) {
                // Lock linked User để xác định Employee có account hợp lệ hay không.
                $linkedUser = User::query()
                    ->whereKey($lockedEmployee->user_id)
                    ->lockForUpdate()
                    ->first();
            }

            if ($linkedUser !== null) {
                // Đóng assignment khi Employee còn liên kết với một User.
                $this->closeCurrentAssignments(
                    $lockedEmployee,
                    TeamMembershipEndReason::EMPLOYEE_DELETED,
                    TeamManagerEndReason::EMPLOYEE_DELETED,
                    $this->actorId($actor),
                    $this->resolveEffectiveDate($effectiveDate),
                );
            }

            // Chỉ soft delete sau khi các assignment liên quan đã được xử lý.
            $lockedEmployee->delete();

            return $lockedEmployee;
        });
    }

    /**
     * Lock, validate và đóng current team assignments của employee.
     */
    private function closeCurrentAssignments(
        Employee $employee,
        TeamMembershipEndReason $membershipEndReason,
        TeamManagerEndReason $managerEndReason,
        ?int $actorId,
        CarbonImmutable $effectiveDate,
    ): void {
        // Lock toàn bộ assignment hiện tại trước khi kiểm tra và cập nhật.
        [$memberships, $managerAssignments] = $this->lockCurrentAssignments($employee);

        // Không cho phép ngày kết thúc sớm hơn ngày bắt đầu assignment.
        $this->ensureEffectiveDateIsValid(
            $effectiveDate,
            $memberships,
            $managerAssignments,
        );

        // Đóng từng loại assignment với end reason tương ứng.
        $this->closeAssignments($memberships, $membershipEndReason, $actorId, $effectiveDate);
        $this->closeAssignments($managerAssignments, $managerEndReason, $actorId, $effectiveDate);
    }

    /**
     * Lock và trả về current membership và manager assignments của employee.
     */
    private function lockCurrentAssignments(Employee $employee): array
    {
        return [
            $this->lockCurrentAssignmentsFor(TeamMembership::class, $employee),
            $this->lockCurrentAssignmentsFor(TeamManager::class, $employee),
        ];
    }

    /**
     * Lock và trả về current assignments của employee cho một assignment type.
     */
    private function lockCurrentAssignmentsFor(string $assignmentModel, Employee $employee): Collection
    {
        // Chỉ lấy assignment chưa kết thúc và lock theo thứ tự.
        return $assignmentModel::query()
            ->where('employee_id', $employee->getKey())
            ->currentAssignment()
            ->orderBy('team_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    /**
     * Đóng assignments và ghi lại lifecycle audit data.
     * Closes assignments and records the lifecycle audit data.
     */
    private function closeAssignments(
        Collection $assignments,
        TeamMembershipEndReason|TeamManagerEndReason $endReason,
        ?int $actorId,
        CarbonImmutable $effectiveDate,
    ): void {
        foreach ($assignments as $assignment) {
            // Ghi nhận thời điểm, lý do và actor đã kết thúc assignment.
            $assignment->update([
                'end_date' => $effectiveDate,
                'is_current' => null,
                'end_reason' => $endReason,
                'ended_by' => $actorId,
            ]);
        }
    }

    /**
     * Ensures the effective date does not precede a current assignment's start date.
     */
    private function ensureEffectiveDateIsValid(
        CarbonImmutable $effectiveDate,
        Collection $memberships,
        Collection $managerAssignments,
    ): void {
        foreach ($memberships->concat($managerAssignments) as $assignment) {
            // Mỗi assignment phải bắt đầu không muộn hơn effective date.
            if ($effectiveDate->lt($assignment->start_date)) {
                throw new DomainException(__('exceptions.employee_lifecycle.effective_date_before_assignment_start'));
            }
        }
    }

    /**
     * Xác định end reasons cho các status transition cần đóng assignment.
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

    /**
     * Trả về ID của actor đã được lưu, hoặc null khi action không có actor.
     */
    private function actorId(?User $actor): ?int
    {
        // Một số lifecycle action có thể chạy không có actor.
        if ($actor === null) {
            return null;
        }

        // Không ghi audit bằng User chưa được lưu xuống database.
        if (! $actor->exists || $actor->getKey() === null) {
            throw new DomainException(__('exceptions.employee_lifecycle.actor_must_be_persisted'));
        }

        return (int) $actor->getKey();
    }

    /**
     * Chuẩn hóa effective date về đầu ngày và từ chối ngày trong tương lai.
     */
    private function resolveEffectiveDate(?CarbonInterface $effectiveDate): CarbonImmutable
    {
        // Dùng ngày hiện tại khi caller không truyền effective date.
        $resolvedDate = CarbonImmutable::instance($effectiveDate ?? now())->startOfDay();

        // Không cho phép đóng assignment vào một ngày trong tương lai.
        if ($resolvedDate->isFuture()) {
            throw new DomainException(__('exceptions.employee_lifecycle.effective_date_in_future'));
        }

        return $resolvedDate;
    }
}

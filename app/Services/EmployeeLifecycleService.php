<?php

namespace App\Services;

use App\Enums\TeamAssignmentEndReason;
use App\Enums\UserStatus;
use App\Models\Employee;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EmployeeLifecycleService
{
    /**
     * Chuyển User status và đóng current team assignments khi cần.
     */
    public function transitionUserStatus(
        User $targetUser,
        UserStatus $targetStatus,
        User $actor,
        ?CarbonInterface $endDate = null,
    ): User {
        return DB::transaction(function () use (
            $targetUser,
            $targetStatus,
            $actor,
            $endDate
        ): User {
            // Lock Employee trước để đồng bộ với các thao tác liên quan đến assignment.
            $employee = Employee::withTrashed()
                ->where('user_id', $targetUser->getKey())
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            // Lock User và lấy status mới nhất trước khi kiểm tra transition.
            $lockedUser = User::query()
                ->whereKey($targetUser->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // Chỉ cho phép các transition được định nghĩa trong UserStatus.
            if (! $lockedUser->status->canTransitionTo($targetStatus)) {
                $this->throwValidationConflict('site.teams.conflicts.invalid_status_transition');
            }

            // Chỉ định status cần đóng assignment.
            $endReason = $this->assignmentEndReasonFor($targetStatus);

            if ($employee !== null && $endReason !== null) {
                // Đóng cả membership và manager assignment cùng với status transition.
                $this->closeCurrentAssignments(
                    $employee,
                    $endReason,
                    $actor->getKey(),
                    $this->resolveEffectiveDate($endDate),
                );
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
        User $actor,
        ?CarbonInterface $endDate = null,
    ): Employee {
        return DB::transaction(function () use ($employee, $actor, $endDate): Employee {
            // Lock Employee để tránh xử lý trùng với thao tác lifecycle khác.
            $lockedEmployee = Employee::withTrashed()
                ->whereKey($employee->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            // Giữ thao tác idempotent nếu Employee đã bị soft delete trước đó.
            if ($lockedEmployee->trashed()) {
                return $lockedEmployee;
            }

            $this->closeCurrentAssignments(
                $lockedEmployee,
                TeamAssignmentEndReason::EMPLOYEE_DELETED,
                $actor->getKey(),
                $this->resolveEffectiveDate($endDate),
            );

            $lockedEmployee->delete();

            return $lockedEmployee;
        });
    }

    private function closeCurrentAssignments(
        Employee $employee,
        TeamAssignmentEndReason $endReason,
        ?int $actorId,
        CarbonImmutable $endDate,
    ): void {
        $assignments = $employee->teamAssignments()
            ->currentAssignment()
            ->orderBy('team_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $this->ensureValidEndDate($endDate, $assignments);
        $this->closeAssignments($assignments, $endReason, $actorId, $endDate);
    }

    /**
     * Đóng assignments và ghi lại lifecycle audit data.
     */
    private function closeAssignments(
        EloquentCollection $assignments,
        TeamAssignmentEndReason $endReason,
        int $actorId,
        CarbonImmutable $endDate,
    ): void {
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

    /**
     * Đảm bảo End date không trước ngày bắt đầu hiện tại.
     */
    private function ensureValidEndDate(
        CarbonImmutable $endDate,
        EloquentCollection $assignments,
    ): void {
        foreach ($assignments as $assignment) {
            if ($endDate->lt($assignment->start_date)) {
                $this->throwValidationConflict('site.teams.conflicts.end_date_before_start_date');
            }
        }
    }

    /**
     * Xác định end reasons cho các status transition cần đóng assignment.
     */
    private function assignmentEndReasonFor(UserStatus $targetStatus): ?TeamAssignmentEndReason
    {
        return match ($targetStatus) {
            UserStatus::INACTIVE => TeamAssignmentEndReason::EMPLOYEE_INACTIVATED,
            UserStatus::TERMINATED => TeamAssignmentEndReason::EMPLOYEE_TERMINATED,
            default => null,
        };
    }

    /**
     * Chuẩn hóa effective date và không lấy ngày trong tương lai.
     */
    private function resolveEffectiveDate(?CarbonInterface $endDate): CarbonImmutable
    {
        $timezone = config('app.timezone');
        $resolvedDate = CarbonImmutable::instance($endDate ?? now())
            ->setTimezone($timezone)
            ->startOfDay();

        if ($resolvedDate->gt(today($timezone))) {
            $this->throwValidationConflict('site.teams.conflicts.effective_date_in_future');
        }

        return $resolvedDate;
    }

    private function throwValidationConflict(string $translationKey): never
    {
        throw ValidationException::withMessages([
            'employee' => __($translationKey),
        ])->status(Response::HTTP_CONFLICT);
    }
}

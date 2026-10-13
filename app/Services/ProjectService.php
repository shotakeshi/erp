<?php

namespace App\Services;

use App\Enums\ProjectAssignmentEndReason;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ProjectService extends BaseService
{
    private const DEFAULT_ROLE = 'member';

    public function createProject(array $projectAttributes, array $members, User $actor): Project
    {
        return DB::transaction(function () use ($projectAttributes, $members, $actor): Project {
            $employeeIds = collect($members)->pluck('employee_id');

            if (filled($projectAttributes['manager_id'] ?? null)) {
                $employeeIds->push($projectAttributes['manager_id']);
            }

            $this->validateEmployees($employeeIds->unique()->values()->all());

            $project = Project::query()->create([
                ...$projectAttributes,
                'created_by' => $actor->getKey(),
            ]);

            $project->assignments()->createMany(
                collect($members)
                    ->map(static fn (array $assignment): array => [
                        'employee_id' => (int) $assignment['employee_id'],
                        'role' => trim($assignment['role']),
                        'start_date' => today()->toDateString(),
                        'is_current' => true,
                        'created_by' => $actor->getKey(),
                    ])
                    ->all(),
            );

            return $project;
        });
    }

    public function updateProject(Project $project, array $projectAttributes, array $members = []): void
    {
        $managerId = $projectAttributes['manager_id'] ?? null;

        if (filled($managerId) && (int) $managerId !== $project->manager_id) {
            $this->validateEmployees([(int) $managerId]);
        }

        DB::transaction(function () use ($project, $projectAttributes, $members): void {
            $assignments = $project->assignments()->currentAssignment()->get()->keyBy('id');

            foreach ($members as $member) {
                $assignment = $assignments->get((int) $member['assignment_id']);

                if ($assignment === null) {
                    $this->fail(__('site.projects.conflicts.assignment_not_current'));
                }

                $assignment->update(['role' => trim($member['role'])]);
            }

            $project->update($projectAttributes);
        });
    }

    public function addAssignments(Project $project, array $employeeIds, User $actor): void
    {
        DB::transaction(function () use ($project, $employeeIds, $actor): void {
            $employeeIds = array_map('intval', $employeeIds);
            $this->validateEmployees($employeeIds);

            if ($project->assignments()->currentAssignment()->whereIn('employee_id', $employeeIds)->exists()) {
                $this->fail(__('site.projects.conflicts.assignment_already_current'));
            }

            $project->assignments()->createMany(array_map(
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

    public function updateAssignment(Project $project, int $employeeId, string $role): void
    {
        $assignment = $project->assignments()
            ->currentAssignment()
            ->where('employee_id', $employeeId)
            ->first();

        if ($assignment === null) {
            $this->fail(__('site.projects.conflicts.assignment_not_current'));
        }

        $assignment->update(['role' => trim($role)]);
    }

    public function removeAssignment(
        Project $project,
        int $employeeId,
        string $endDate,
        User $actor,
        ?string $endReasonNote,
    ): void {
        DB::transaction(function () use ($project, $employeeId, $endDate, $actor, $endReasonNote): void {
            $endDate = CarbonImmutable::parse($endDate)->startOfDay();
            $assignment = $project->assignments()
                ->currentAssignment()
                ->where('employee_id', $employeeId)
                ->first();

            if ($assignment === null) {
                $this->fail(__('site.projects.conflicts.assignment_not_current'));
            }

            if ($endDate->lt($assignment->start_date)) {
                $this->fail(__('site.projects.conflicts.end_date_before_start_date'));
            }

            $assignment->update([
                'end_date' => $endDate,
                'is_current' => null,
                'end_reason' => ProjectAssignmentEndReason::REMOVED,
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

        $employees = Employee::query()
            ->whereIn('id', $employeeIds)
            ->active()
            ->get();

        if ($employees->count() !== count($employeeIds)) {
            $this->fail(__('site.projects.conflicts.employee_not_eligible'));
        }
    }
}

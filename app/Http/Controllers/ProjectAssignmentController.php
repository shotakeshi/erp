<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeePickerRequest;
use App\Http\Requests\ProjectAssignmentRequest;
use App\Http\Requests\RemoveAssignmentRequest;
use App\Models\Employee;
use App\Models\Project;
use App\Services\ProjectService;
use App\Services\Shared\FormOptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProjectAssignmentController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function employeeOptions(EmployeePickerRequest $request, ?Project $project = null): JsonResponse
    {
        $employees = $this->formOptionService->searchProjectEmployees(
            $project,
            $request->validated('search') ?? '',
            $request->validated('exclude_ids') ?? [],
        );

        return response()->json($this->formOptionService->employeePickerResponse($employees));
    }

    public function store(ProjectAssignmentRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->addAssignments(
            $project,
            $request->validated('employee_ids'),
            $request->user(),
        );

        return redirect()->route('projects.show', [$project, 'tab' => ProjectController::MEMBERS_TAB])->with('success', __('common.messages.created'));
    }

    public function update(
        ProjectAssignmentRequest $request,
        Project $project,
        Employee $employee,
    ): RedirectResponse|JsonResponse {
        $role = $request->validated('role');

        $this->projectService->updateAssignment($project, $employee->getKey(), $role);

        if ($request->wantsJson()) {
            return response()->json(['message' => __('common.messages.updated'), 'value' => $role]);
        }

        return redirect()->route('projects.show', [$project, 'tab' => ProjectController::MEMBERS_TAB])->with('success', __('common.messages.updated'));
    }

    public function destroy(
        RemoveAssignmentRequest $request,
        Project $project,
        Employee $employee,
    ): RedirectResponse {
        $validated = $request->validated();

        $this->projectService->removeAssignment(
            $project,
            $employee->getKey(),
            $validated['end_date'],
            $request->user(),
            $validated['end_reason_note'] ?? null,
        );

        return redirect()->route('projects.show', [$project, 'tab' => ProjectController::MEMBERS_TAB])->with('success', __('common.messages.updated'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceException;
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

        return response()->json($this->formOptionService->employeePickerPayload($employees));
    }

    public function store(ProjectAssignmentRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->projectService->addAssignments(
                $project,
                $validated['employee_ids'],
                $request->user(),
            );
        } catch (ServiceException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('projects.show', [$project, 'tab' => ProjectController::MEMBERS_TAB])->with('success', __('common.messages.created'));
    }

    public function update(
        ProjectAssignmentRequest $request,
        Project $project,
        Employee $employee,
    ): RedirectResponse {
        try {
            $this->projectService->updateAssignment($project, $employee->getKey(), $request->validated('role'));
        } catch (ServiceException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('projects.show', [$project, 'tab' => ProjectController::MEMBERS_TAB])->with('success', __('common.messages.updated'));
    }

    public function destroy(
        RemoveAssignmentRequest $request,
        Project $project,
        Employee $employee,
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $this->projectService->removeAssignment(
                $project,
                $employee->getKey(),
                $validated['end_date'],
                $request->user(),
                $validated['end_reason_note'] ?? null,
            );
        } catch (ServiceException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('projects.show', [$project, 'tab' => ProjectController::MEMBERS_TAB])->with('success', __('common.messages.updated'));
    }
}

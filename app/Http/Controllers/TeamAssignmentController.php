<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeePickerRequest;
use App\Http\Requests\RemoveAssignmentRequest;
use App\Http\Requests\TeamAssignmentsRequest;
use App\Models\Employee;
use App\Models\Team;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TeamAssignmentController extends Controller
{
    public function __construct(
        private readonly TeamService $teamService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function employeeOptions(EmployeePickerRequest $request, ?Team $team = null): JsonResponse
    {
        $employees = $this->formOptionService->searchTeamEmployees(
            $team,
            $request->validated('search') ?? '',
            $request->validated('exclude_ids') ?? [],
        );

        return response()->json($this->formOptionService->employeePickerPayload($employees));
    }

    public function store(TeamAssignmentsRequest $request, Team $team): RedirectResponse
    {
        $validated = $request->validated();

        $this->teamService->addAssignments(
            $team,
            $validated['employee_ids'],
            $request->user(),
        );

        return redirect()
            ->route('teams.show', [$team, 'tab' => TeamController::MEMBERS_TAB])
            ->with('success', __('common.messages.created'));
    }

    public function destroy(
        RemoveAssignmentRequest $request,
        Team $team,
        Employee $employee,
    ): RedirectResponse {
        $validated = $request->validated();

        $this->teamService->removeAssignment(
            $team,
            $employee,
            $validated['end_date'],
            $request->user(),
            $validated['end_reason_note'] ?? null,
        );

        return redirect()
            ->route('teams.show', [$team, 'tab' => TeamController::MEMBERS_TAB])
            ->with('success', __('common.messages.updated'));
    }
}

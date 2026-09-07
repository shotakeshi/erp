<?php

namespace App\Http\Controllers;

use App\Enums\TeamAssignmentType;
use App\Http\Requests\RemoveAssignmentRequest;
use App\Http\Requests\TeamAssignmentsRequest;
use App\Models\Employee;
use App\Models\Team;
use App\Queries\TeamQuery;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamAssignmentController extends Controller
{
    public function __construct(
        private readonly TeamQuery $teamQuery,
        private readonly TeamService $teamService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function index(Team $team, string $type): View
    {
        $type = TeamAssignmentType::from($type);
        $team = $this->teamQuery->detailForTabs($team);

        $config = match ($type) {
            TeamAssignmentType::MEMBER => [
                'assignments' => $this->teamQuery->currentMembers($team),
                'view' => 'teams.members.index',
                'key' => 'memberships',
            ],
            TeamAssignmentType::MANAGER => [
                'assignments' => $this->teamQuery->currentManagers($team),
                'view' => 'teams.managers.index',
                'key' => 'managerAssignments',
            ],
        };

        return view($config['view'], [
            'team' => $team,
            'employees' => $this->formOptionService->employeeOptions(),
            'assignedEmployeeIds' => $config['assignments']->pluck('employee_id'),
            $config['key'] => $config['assignments'],
        ]);
    }

    public function memberStore(TeamAssignmentsRequest $request, Team $team): RedirectResponse
    {
        $validated = $request->validated();

        $this->teamService->addAssignments(
            $team,
            $validated['employee_ids'],
            $validated['start_date'],
            $request->user(),
            TeamAssignmentType::MEMBER,
        );

        return redirect()
            ->route('teams.members.index', $team)
            ->with('success', __('common.messages.created'));
    }

    public function memberDestroy(
        RemoveAssignmentRequest $request,
        Team $team,
        Employee $employee,
    ): RedirectResponse {
        return $this->destroy(
            $request,
            $team,
            $employee,
            TeamAssignmentType::MEMBER,
            'teams.members.index',
        );
    }

    public function memberHistory(Request $request, Team $team): View
    {
        $team = $this->teamQuery->detailForTabs($team);

        return view('teams.members.history', [
            'team' => $team,
            'memberships' => $this->teamQuery->memberHistory($team, $request->only('filter')),
        ]);
    }

    public function managerHistory(Request $request, Team $team): View
    {
        $team = $this->teamQuery->detailForTabs($team);

        return view('teams.managers.history', [
            'team' => $team,
            'memberships' => $this->teamQuery->managerHistory($team, $request->only('filter')),
        ]);
    }

    public function managerStore(TeamAssignmentsRequest $request, Team $team): RedirectResponse
    {
        $validated = $request->validated();

        $this->teamService->addAssignments(
            $team,
            $validated['employee_ids'],
            $validated['start_date'],
            $request->user(),
            TeamAssignmentType::MANAGER,
        );

        return redirect()
            ->route('teams.managers.index', $team)
            ->with('success', __('common.messages.created'));
    }

    public function managerDestroy(
        RemoveAssignmentRequest $request,
        Team $team,
        Employee $employee,
    ): RedirectResponse {
        return $this->destroy(
            $request,
            $team,
            $employee,
            TeamAssignmentType::MANAGER,
            'teams.managers.index',
        );
    }

    private function destroy(
        RemoveAssignmentRequest $request,
        Team $team,
        Employee $employee,
        TeamAssignmentType $type,
        string $redirectRoute,
    ): RedirectResponse {
        $validated = $request->validated();

        $this->teamService->removeAssignment(
            $team,
            $employee,
            $validated['end_date'],
            $request->user(),
            $type,
            $validated['end_reason_note'] ?? null,
        );

        return redirect()
            ->route($redirectRoute, $team)
            ->with('success', __('common.messages.updated'));
    }
}

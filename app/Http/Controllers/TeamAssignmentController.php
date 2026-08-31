<?php

namespace App\Http\Controllers;

use App\Enums\TeamAssignmentRole;
use App\Http\Requests\RemoveAssignmentRequest;
use App\Http\Requests\TeamAssignmentsRequest;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
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

    public function index(Team $team, string $role): View
    {
        $role = TeamAssignmentRole::from($role);
        $team = $this->teamQuery->detailForTabs($team);

        $config = match ($role) {
            TeamAssignmentRole::MEMBER => [
                'assignments' => $this->teamQuery->currentMembers($team),
                'view' => 'teams.members.index',
                'key' => 'memberships',
            ],
            TeamAssignmentRole::MANAGER => [
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
            TeamAssignmentRole::MEMBER,
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
            TeamAssignmentRole::MEMBER,
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

    public function managerStore(TeamAssignmentsRequest $request, Team $team): RedirectResponse
    {
        $validated = $request->validated();

        $this->teamService->addAssignments(
            $team,
            $validated['employee_ids'],
            $validated['start_date'],
            $request->user(),
            TeamAssignmentRole::MANAGER,
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
            TeamAssignmentRole::MANAGER,
            'teams.managers.index',
        );
    }

    private function destroy(
        RemoveAssignmentRequest $request,
        Team $team,
        Employee $employee,
        TeamAssignmentRole $role,
        string $redirectRoute,
    ): RedirectResponse {
        $validated = $request->validated();

        $this->teamService->removeAssignment(
            $team,
            $employee,
            $validated['end_date'],
            $request->user(),
            $role,
            $validated['end_reason_note'] ?? null,
        );

        return redirect()
            ->route($redirectRoute, $team)
            ->with('success', __('common.messages.updated'));
    }
}

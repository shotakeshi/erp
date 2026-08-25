<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemoveManagerRequest;
use App\Http\Requests\TeamManagersRequest;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use App\Queries\TeamQuery;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamManagerController extends Controller
{
    public function __construct(
        private readonly TeamQuery $teamQuery,
        private readonly TeamService $teamService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function index(Team $team): View
    {
        $team = $this->teamQuery->detailForTabs($team);
        $managerAssignments = $this->teamQuery->currentManagers($team);

        return view('teams.managers.index', [
            'team' => $team,
            'managerAssignments' => $managerAssignments,
            'employees' => $this->formOptionService->employeeOptions(),
            'assignedEmployeeIds' => $managerAssignments->pluck('employee_id'),
        ]);
    }

    public function store(TeamManagersRequest $request, Team $team): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $attributes = $request->validated();

        $this->teamService->addManagers(
            $team,
            $attributes['employee_ids'],
            $attributes['start_date'],
            $actor,
        );

        return redirect()
            ->route('teams.managers.index', $team)
            ->with('success', __('common.messages.created'));
    }

    public function destroy(
        RemoveManagerRequest $request,
        Team $team,
        Employee $employee,
    ): RedirectResponse {
        /** @var User $actor */
        $actor = $request->user();

        $this->teamService->removeManager(
            $team,
            $employee,
            $request->validated('end_date'),
            $actor,
        );

        return redirect()
            ->route('teams.managers.index', $team)
            ->with('success', __('common.messages.updated'));
    }
}

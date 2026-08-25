<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemoveMemberRequest;
use App\Http\Requests\TeamMembersRequest;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use App\Queries\TeamQuery;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    public function __construct(
        private readonly TeamQuery $teamQuery,
        private readonly TeamService $teamService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function index(Team $team): View
    {
        $team = $this->teamQuery->detailForTabs($team);
        $memberships = $this->teamQuery->currentMembers($team);

        return view('teams.members.index', [
            'team' => $team,
            'memberships' => $memberships,
            'employees' => $this->formOptionService->employeeOptions(),
            'assignedEmployeeIds' => $memberships->pluck('employee_id'),
        ]);
    }

    public function store(TeamMembersRequest $request, Team $team): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $attributes = $request->validated();

        $this->teamService->addMembers(
            $team,
            $attributes['employee_ids'],
            $attributes['start_date'],
            $actor,
        );

        return redirect()
            ->route('teams.members.index', $team)
            ->with('success', __('common.messages.created'));
    }

    public function destroy(
        RemoveMemberRequest $request,
        Team $team,
        Employee $employee,
    ): RedirectResponse {
        /** @var User $actor */
        $actor = $request->user();

        $this->teamService->removeMember(
            $team,
            $employee,
            $request->validated('end_date'),
            $actor,
        );

        return redirect()
            ->route('teams.members.index', $team)
            ->with('success', __('common.messages.updated'));
    }

    public function history(Request $request, Team $team): View
    {
        $team = $this->teamQuery->detailForTabs($team);

        return view('teams.members.history', [
            'team' => $team,
            'memberships' => $this->teamQuery->memberHistory($team, $request->only('filter')),
        ]);
    }
}

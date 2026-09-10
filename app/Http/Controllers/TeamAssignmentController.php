<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemoveAssignmentRequest;
use App\Http\Requests\TeamAssignmentsRequest;
use App\Models\Employee;
use App\Models\Team;
use App\Queries\TeamQuery;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request, Team $team): View
    {
        $mode = $request->query('mode') === 'list' ? 'list' : 'grid';

        $viewData = [
            'team' => $team,
            'employees' => $this->formOptionService->assignableEmployeeOptions($team),
            'memberships' => $this->teamQuery->currentMembers($team),
            'mode' => $mode,
        ];

        return $mode === 'grid'
            ? view('teams.members.grid', $viewData)
            : view('teams.members.list', $viewData);
    }

    public function store(TeamAssignmentsRequest $request, Team $team): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $this->teamService->addAssignments(
            $team,
            $validated['employee_ids'],
            $request->user(),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('common.messages.created'),
                'redirect_url' => route('teams.members.index', $team),
            ]);
        }

        return redirect()
            ->route('teams.members.index', $team)
            ->with('success', __('common.messages.created'));
    }

    public function history(Request $request, Team $team): View
    {
        return view('teams.members.history', [
            'team' => $team,
            'memberships' => $this->teamQuery->memberHistory($team, $request->only('filter')),
        ]);
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
            ->route('teams.members.index', $team)
            ->with('success', __('common.messages.updated'));
    }
}

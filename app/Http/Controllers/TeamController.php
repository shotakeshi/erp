<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceException;
use App\Http\Requests\AssignmentPageRequest;
use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Queries\TeamQuery;
use App\Services\FileUploadService;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TeamController extends Controller
{
    public const MEMBERS_TAB = 'members';

    public const HISTORY_TAB = 'history';

    public const GRID_MODE = 'grid';

    public const LIST_MODE = 'list';

    public function __construct(
        private readonly TeamQuery $teamQuery,
        private readonly TeamService $teamService,
        private readonly FileUploadService $fileUploadService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function index(Request $request): View
    {
        return view('teams.index', [
            'teams' => $this->teamQuery->paginate($request->only('search')),
        ]);
    }

    public function trash(Request $request): View
    {
        return view('teams.trash', [
            'teams' => $this->teamQuery->paginateTrashed($request->only('search')),
        ]);
    }

    public function create(Request $request): View
    {
        return view('teams.create', [
            'initialMembers' => $this->formOptionService->initialAssignmentMembers($request->old('members', [])),
            'roles' => $this->formOptionService->roleTeamAssignmentOptions(),
        ]);
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        $teamLogo = null;

        try {
            $validated = $request->safe()->except(['members', 'logo', 'remove_logo']);

            if ($request->hasFile('logo')) {
                $teamLogo = $this->fileUploadService->upload(
                    $request->file('logo'),
                    'teams/logos',
                );
                $validated['logo'] = $teamLogo;
            }

            $this->teamService->createTeam($validated, $request->validated('members', []), $request->user());

            return redirect()
                ->route('teams.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
            if ($teamLogo) {
                $this->fileUploadService->delete($teamLogo);
            }

            throw_if($e instanceof ServiceException, $e);

            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    public function show(AssignmentPageRequest $request, Team $team): View
    {
        $activeTab = $team->trashed()
            ? self::HISTORY_TAB
            : ($request->validated('tab') === self::HISTORY_TAB ? self::HISTORY_TAB : self::MEMBERS_TAB);

        return view('teams.show', [
            'team' => $team,
            'activeTab' => $activeTab,
            'mode' => $request->validated('mode') === self::LIST_MODE ? self::LIST_MODE : self::GRID_MODE,
            'assignments' => $this->teamQuery
                ->currentAssignments($team, $request->validated('search') ?? '')
                ->appends('tab', self::MEMBERS_TAB),
            'histories' => $this->teamQuery
                ->assignmentHistories($team)
                ->appends('tab', self::HISTORY_TAB),
            'initialEmployees' => $this->formOptionService->employeePickerItems(
                $this->formOptionService->selectedEmployees($request->old('employee_ids', [])),
            ),
        ]);
    }

    public function edit(Team $team): View
    {
        return view('teams.edit', [
            'team' => $team,
            'assignments' => $team->assignments()
                ->currentAssignment()
                ->with('employee.position')
                ->orderBy('id')->get(),
            'roles' => $this->formOptionService->roleTeamAssignmentOptions(),
        ]);
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $newLogo = null;
        try {
            $oldLogo = $team->logo;
            $teamRequests = $request->safe()->except(['members', 'logo', 'remove_logo']);

            if ($request->hasFile('logo')) {
                $newLogo = $this->fileUploadService->upload(
                    $request->file('logo'),
                    'teams/logos',
                );
                $teamRequests['logo'] = $newLogo;
            } elseif ($request->boolean('remove_logo')) {
                $teamRequests['logo'] = null;
            }

            $this->teamService->updateTeam($team, $teamRequests, $request->validated('members', []));

            if ($oldLogo && ($newLogo || $request->boolean('remove_logo'))) {
                $this->fileUploadService->delete($oldLogo);
            }

            return redirect()
                ->route('teams.index')
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
            if ($newLogo) {
                $this->fileUploadService->delete($newLogo);
            }

            throw_if($e instanceof ServiceException, $e);

            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }
    }

    public function destroy(Team $team): RedirectResponse
    {
        try {
            $team->delete();

            return redirect()
                ->route('teams.index')
                ->with('success', __('common.messages.deleted'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('error', __('common.messages.delete_failed'));
        }
    }

    public function restore(Team $team): RedirectResponse
    {
        try {
            $team->restore();

            return redirect()
                ->route('teams.trash')
                ->with('success', __('common.messages.restored'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('error', __('common.messages.restore_failed'));
        }
    }
}

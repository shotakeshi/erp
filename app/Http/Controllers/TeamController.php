<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Models\User;
use App\Queries\TeamQuery;
use App\Services\FileUploadService;
use App\Services\Shared\FormOptionService;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TeamController extends Controller
{
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

    public function create(): View
    {
        return view('teams.create', [
            'employees' => $this->formOptionService->employeeOptions(),
            'roles' => $this->formOptionService->roleAssignmentOptions(),
        ]);
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        $teamLogo = null;

        try {
            $validated = $request->validated();
            $members = $validated['members'] ?? [];
            unset($validated['members']);

            if ($request->hasFile('logo')) {
                $teamLogo = $this->fileUploadService->upload(
                    $request->file('logo'),
                    'teams/logos',
                );
                $validated['logo'] = $teamLogo;
            }

            $this->teamService->createTeam($validated, $members, $request->user());

            return redirect()
                ->route('teams.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
            if ($teamLogo) {
                $this->fileUploadService->delete($teamLogo);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    public function show(Team $team): View
    {
        return view('teams.show', [
            'team' => $this->teamQuery->detailForTabs($team),
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
            'roles' => $this->formOptionService->roleAssignmentOptions(),
        ]);
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $newLogo = null;
        try {
            $oldLogo = $team->logo;
            $teamRequests = $request->validated();
            $members = $teamRequests['members'] ?? [];
            unset(
                $teamRequests['remove_logo'],
                $teamRequests['logo'],
                $teamRequests['members'],
            );

            if ($request->hasFile('logo')) {
                $newLogo = $this->fileUploadService->upload(
                    $request->file('logo'),
                    'teams/logos',
                );
                $teamRequests['logo'] = $newLogo;
            } elseif ($request->boolean('remove_logo')) {
                $teamRequests['logo'] = null;
            }

            $this->teamService->updateTeam($team, $teamRequests, $members);

            if ($oldLogo && ($newLogo || $request->boolean('remove_logo'))) {
                $this->fileUploadService->delete($oldLogo);
            }

            return redirect()
                ->route('teams.show', $team)
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
            if ($newLogo) {
                $this->fileUploadService->delete($newLogo);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }
    }

    public function destroy(Request $request, Team $team): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $this->teamService->deleteTeam($team, $actor);

        return redirect()
            ->route('teams.index')
            ->with('success', __('common.messages.deleted'));
    }

    public function restore(Team $team): RedirectResponse
    {
        try {
            DB::transaction(function () use ($team) {
                $team->restore();
            });

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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Models\User;
use App\Queries\TeamQuery;
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
        return view('teams.create');
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        try {
            Team::create($request->validated());

            return redirect()
                ->route('teams.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
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
        return view('teams.edit', compact('team'));
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        try {
            $team->fill($request->validated());
            if (! $team->isDirty()) {
                return back()->with('warning', __('common.messages.not_changed'));
            }
            $team->save();

            return redirect()
                ->route('teams.show', $team)
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
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

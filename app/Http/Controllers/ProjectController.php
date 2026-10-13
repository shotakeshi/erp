<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceException;
use App\Http\Requests\AssignmentPageRequest;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Queries\ProjectQuery;
use App\Services\FileUploadService;
use App\Services\ProjectService;
use App\Services\Shared\FormOptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Throwable;

class ProjectController extends Controller
{
    public const OVERVIEW_TAB = 'overview';

    public const MEMBERS_TAB = 'members';

    public const HISTORY_TAB = 'history';

    private const AVATAR_DIRECTORY = 'projects/avatars';

    public function __construct(
        private readonly ProjectQuery $projectQuery,
        private readonly ProjectService $projectService,
        private readonly FileUploadService $fileUploadService,
        private readonly FormOptionService $formOptionService,
    ) {}

    public function index(Request $request): View
    {
        return view('projects.index', [
            'projects' => $this->projectQuery->paginate($request->only('search')),
        ]);
    }

    public function create(Request $request): View
    {
        return view('projects.create', [
            ...$this->formOptions(),
            'initialMembers' => $this->formOptionService->initialAssignmentMembers($request->old('members', [])),
            'roles' => $this->formOptionService->roleProjectAssignmentOptions(),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $newAvatar = null;

        try {
            $validated = Arr::except($request->validated(), ['members', 'project_avatar', 'remove_project_avatar']);

            if ($request->hasFile('project_avatar')) {
                $newAvatar = $this->fileUploadService->upload(
                    $request->file('project_avatar'),
                    self::AVATAR_DIRECTORY,
                );
                $validated['project_avatar'] = $newAvatar;
            }

            $this->projectService->createProject($validated, $request->validated('members', []), $request->user());

            return redirect()
                ->route('projects.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
            if ($newAvatar) {
                $this->fileUploadService->delete($newAvatar);
            }

            throw_if($e instanceof ServiceException, $e);

            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    public function show(AssignmentPageRequest $request, Project $project): View
    {
        $search = $request->validated('search') ?? '';
        $activeTab = $request->validated('tab') ?? ($search !== '' ? self::MEMBERS_TAB : self::OVERVIEW_TAB);

        return view('projects.show', [
            'project' => $this->projectQuery->findForShow($project),
            'activeTab' => $activeTab,
            'assignments' => $this->projectQuery
                ->currentAssignments($project, $search)
                ->appends('tab', self::MEMBERS_TAB),
            'histories' => $this->projectQuery
                ->assignmentHistories($project)
                ->appends('tab', self::HISTORY_TAB),
            'initialEmployees' => $this->formOptionService->employeePickerItems(
                $this->formOptionService->selectedEmployees($request->old('employee_ids', [])),
            ),
        ]);
    }

    public function edit(Project $project): View
    {
        $project->load([
            'manager:id,employee_id,first_name,last_name,avatar,deleted_at',
            'team:id,name,deleted_at',
        ]);

        return view('projects.edit', [
            'project' => $project,
            'assignments' => $project->assignments()
                ->currentAssignment()
                ->with('employee.position')
                ->orderBy('id')
                ->get(),
            'roles' => $this->formOptionService->roleProjectAssignmentOptions(),
            ...$this->formOptions($project),
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $newAvatar = null;

        try {
            $oldAvatar = $project->project_avatar;
            $validated = Arr::except($request->validated(), ['members', 'project_avatar', 'remove_project_avatar']);

            if ($request->hasFile('project_avatar')) {
                $newAvatar = $this->fileUploadService->upload(
                    $request->file('project_avatar'),
                    self::AVATAR_DIRECTORY,
                );
                $validated['project_avatar'] = $newAvatar;
            } elseif ($request->boolean('remove_project_avatar')) {
                $validated['project_avatar'] = null;
            }

            $this->projectService->updateProject($project, $validated, $request->validated('members', []));

            if ($oldAvatar && ($newAvatar || $request->boolean('remove_project_avatar'))) {
                $this->fileUploadService->delete($oldAvatar);
            }

            return redirect()
                ->route('projects.index')
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
            if ($newAvatar) {
                $this->fileUploadService->delete($newAvatar);
            }

            throw_if($e instanceof ServiceException, $e);

            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }
    }

    public function destroy(Project $project): RedirectResponse
    {
        try {
            $project->delete();

            return redirect()
                ->route('projects.index')
                ->with('success', __('common.messages.deleted'));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', __('common.messages.delete_failed'));
        }
    }

    public function trash(Request $request): View
    {
        return view('projects.trash', [
            'projects' => $this->projectQuery->paginateTrashed($request->only('search')),
        ]);
    }

    public function restore(Project $project): RedirectResponse
    {
        $project->restore();

        return redirect()
            ->route('projects.trash')
            ->with('success', __('common.messages.restored'));
    }

    private function formOptions(?Project $project = null): array
    {
        $managers = $this->formOptionService->employeeOptions();
        $teams = $this->formOptionService->teamOptions();

        if ($project?->manager && ! $managers->contains('id', $project->manager->getKey())) {
            $managers->push($project->manager);
        }

        if ($project?->team && ! $teams->contains('id', $project->team->getKey())) {
            $teams->push($project->team);
        }

        return [
            'managers' => $managers,
            'teams' => $teams,
            'departments' => $this->formOptionService->departmentOptions(),
        ];
    }
}

@php
    $activeTab = $activeTab ?? \App\Http\Controllers\ProjectController::OVERVIEW_TAB;
    $overviewTab = \App\Http\Controllers\ProjectController::OVERVIEW_TAB;
    $membersTab = \App\Http\Controllers\ProjectController::MEMBERS_TAB;
    $historyTab = \App\Http\Controllers\ProjectController::HISTORY_TAB;
@endphp

<div class="card">
    <div class="card-body">
        <div class="media align-items-center">
            <img
                src="{{ image_url($project->project_avatar, 'images/widgets/project1.jpg') }}"
                alt="{{ $project->name }}"
                class="rounded-circle thumb-xl mr-3"
            >
            <div class="media-body">
                <div class="d-flex align-items-center flex-wrap">
                    <p class="header-title mb-1 mr-2">{{ $project->name }}</p>
                    <span class="{{ $project->status->badgeClass() }}">
                        {{ $project->status->label() }}
                    </span>
                </div>
                <span class="badge badge-classic">{{ $project->code }}</span>
            </div>
        </div>
    </div>
    <div class="card-body d-flex align-items-center">
        <ul class="nav nav-pills mb-0 mr-3" id="project-tabs" role="tablist">
            <li class="nav-item">
                <a
                    @class(['nav-link', 'active' => $activeTab === $overviewTab])
                    id="project-overview-tab"
                    data-toggle="pill"
                    href="#project-overview"
                    role="tab"
                    aria-controls="project-overview"
                    aria-selected="{{ $activeTab === $overviewTab ? 'true' : 'false' }}"
                >
                    {{ __('site.projects.overview') }}
                </a>
            </li>
            <li class="nav-item">
                <a
                    @class(['nav-link', 'active' => $activeTab === $membersTab])
                    id="project-members-tab"
                    data-toggle="pill"
                    href="#project-members"
                    role="tab"
                    aria-controls="project-members"
                    aria-selected="{{ $activeTab === $membersTab ? 'true' : 'false' }}"
                >
                    {{ __('site.projects.members') }}
                </a>
            </li>
            <li class="nav-item">
                <a
                    @class(['nav-link', 'active' => $activeTab === $historyTab])
                    id="project-history-tab"
                    data-toggle="pill"
                    href="#project-history"
                    role="tab"
                    aria-controls="project-history"
                    aria-selected="{{ $activeTab === $historyTab ? 'true' : 'false' }}"
                >
                    {{ __('site.projects.member_history') }}
                </a>
            </li>
        </ul>
        <div class="ml-auto flex-shrink-0">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-warning mr-1">
                <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                {{ __('common.button.edit') }}
            </a>
            <x-form.confirm-button
                :action="route('projects.destroy', $project)"
                method="DELETE"
                title="{{ __('site.projects.delete_confirmation_title') }}"
                text="{{ __('site.projects.delete_confirmation') }}"
                confirm-text="{{ __('site.projects.archive') }}"
                cancel-text="{{ __('common.button.cancel') }}"
                icon="fas fa-trash"
                class="btn btn-sm btn-outline-danger"
                label="{{ __('site.projects.archive') }}"
            />
        </div>
    </div>
</div>

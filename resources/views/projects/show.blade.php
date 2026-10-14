@push('title', $project->name)
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.projects.detail') }}"
        :breadcrumbs="[
            ['title' => __('site.projects.title'), 'url' => route('projects.index')],
            ['title' => $project->name],
        ]"
    />

    @include('projects.partials._project-tabs', ['project' => $project, 'activeTab' => $activeTab])

    <div class="tab-content mt-3" id="project-tab-content">
        <div
            @class(['tab-pane fade', 'show active' => $activeTab === \App\Http\Controllers\ProjectController::OVERVIEW_TAB])
            id="project-overview"
            role="tabpanel"
            aria-labelledby="project-overview-tab"
        >
            <div class="card">
                <div class="card-header">{{ __('site.projects.detail') }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.project_type') }}</p>
                            <p class="mb-0 font-weight-bold">{{ __('site.projects.types.' . $project->project_type->value) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.priority') }}</p>
                            <p class="mb-0 font-weight-bold">{{ __('site.projects.priorities.' . $project->priority->value) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.manager') }}</p>
                            <p class="mb-0 font-weight-bold">{{ $project->manager?->full_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.team') }}</p>
                            <p class="mb-0 font-weight-bold">{{ $project->team?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.department') }}</p>
                            <p class="mb-0 font-weight-bold">{{ $project->department?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.start_date') }}</p>
                            <p class="mb-0 font-weight-bold">{{ $project->start_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.planned_end_date') }}</p>
                            <p class="mb-0 font-weight-bold">{{ $project->planned_end_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.actual_end_date') }}</p>
                            <p class="mb-0 font-weight-bold">{{ $project->actual_end_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1">{{ __('site.projects.project_url') }}</p>
                            @if ($project->project_url)
                                <a
                                    href="{{ $project->project_url }}"
                                    class="font-weight-bold text-info text-break"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ $project->project_url }}
                                </a>
                            @else
                                <p class="mb-0 font-weight-bold">-</p>
                            @endif
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-1">
                        <p class="text-muted mb-1">{{ __('site.projects.description') }}</p>
                        <p class="mb-0">{{ $project->description ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div
            @class(['tab-pane fade', 'show active' => $activeTab === \App\Http\Controllers\ProjectController::MEMBERS_TAB])
            id="project-members"
            role="tabpanel"
            aria-labelledby="project-members-tab"
        >
            @include('projects.assignments._toolbar', ['project' => $project])

            @include('projects.assignments._card', ['project' => $project, 'assignments' => $assignments])

            <x-pagination :paginator="$assignments" />
        </div>

        <div
            @class(['tab-pane fade', 'show active' => $activeTab === \App\Http\Controllers\ProjectController::HISTORY_TAB])
            id="project-history"
            role="tabpanel"
            aria-labelledby="project-history-tab"
        >
            <x-assignment-history
                :assignments="$histories"
                lang-prefix="site.projects"
            />
        </div>
    </div>

    <x-remove-assignment-modal
        :owner="$project"
        :assignments="$assignments->getCollection()"
        destroy-route="projects.assignments.destroy"
        lang-prefix="site.projects"
    />

    <x-employee-picker-modal
        id="project-add-assignment-modal"
        :title="__('site.projects.members_modal_title')"
        :url="route('projects.assignments.employee-options', $project)"
        :action="route('projects.assignments.store', $project)"
        :labels="[
            'remove' => __('site.projects.remove_member'),
            'members' => __('site.projects.members'),
        ]"
        :initial-employees="$initialEmployees"
    />
@endsection

@push('css')
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/x-editable/css/bootstrap-editable.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('plugins/x-editable/js/bootstrap-editable.min.js') }}"></script>
    <script src="{{ asset('js/shared/x-editable.js') }}"></script>
@endpush

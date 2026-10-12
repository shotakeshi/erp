@php
    $project = $project ?? null;
    $employees = $employees ?? collect();
    $managers = $managers ?? $employees;
    $teams = $teams ?? collect();
    $departments = $departments ?? collect();
    $roles = $roles ?? collect();
    $memberInputs = old('members', []);
    $projectType = $project?->project_type;
    $projectStatus = $project?->status;
    $projectPriority = $project?->priority;
    $projectTypeValue = $projectType instanceof \App\Enums\ProjectType ? $projectType->value : $projectType;
    $projectStatusValue = $projectStatus instanceof \App\Enums\ProjectStatus ? $projectStatus->value : $projectStatus;
    $projectPriorityValue = $projectPriority instanceof \App\Enums\ProjectPriority ? $projectPriority->value : $projectPriority;
    $formatDate = static fn ($date): ?string => $date
        ? \Illuminate\Support\Carbon::parse($date)->format('d/m/Y')
        : null;
    $projectTypeOptions = collect(\App\Enums\ProjectType::cases())
        ->mapWithKeys(fn (\App\Enums\ProjectType $type): array => [$type->value => __('site.projects.types.' . $type->value)])
        ->all();
    $projectStatusOptions = \App\Enums\ProjectStatus::options();
    $projectPriorityOptions = collect(\App\Enums\ProjectPriority::cases())
        ->mapWithKeys(fn (\App\Enums\ProjectPriority $priority): array => [$priority->value => __('site.projects.priorities.' . $priority->value)])
        ->all();
@endphp

@push('css')
    <link href="{{ asset('css/dropify/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.forms-advanced.js') }}"></script>
@endpush

<form class="form-horizontal form-material mb-0" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="card">
        <div class="card-header">{{ __('site.projects.detail') }}</div>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-4">
                    <x-form.label for="project_avatar">{{ __('site.projects.project_avatar') }}</x-form.label>
                    <input
                        type="file"
                        name="project_avatar"
                        id="project_avatar"
                        accept="image/jpeg,image/png,image/webp"
                        class="dropify"
                        data-default-file="{{ image_url($project?->project_avatar, 'images/widgets/project1.jpg') }}"
                    >
                    <x-form.error name="project_avatar" />

                    @if ($project?->project_avatar)
                        <div class="form-check mt-2">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="remove_project_avatar"
                                id="remove_project_avatar"
                                value="1"
                            >
                            <label class="form-check-label" for="remove_project_avatar">
                                {{ __('site.projects.remove_project_avatar') }}
                            </label>
                        </div>
                    @endif
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-8">
                            <x-form.input
                                name="name"
                                label="{{ __('site.projects.name') }}"
                                :value="$project?->name"
                                placeholder="{{ __('site.projects.name_placeholder') }}"
                                required
                                autofocus
                            />
                        </div>
                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <x-form.input
                                name="code"
                                label="{{ __('site.projects.code') }}"
                                :value="$project?->code"
                                placeholder="{{ __('site.projects.code_placeholder') }}"
                                format="uppercase"
                                required
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-4">
                            <x-form.select
                                name="project_type"
                                label="{{ __('site.projects.project_type') }}"
                                :options="$projectTypeOptions"
                                :selected="$projectTypeValue"
                                required
                            />
                        </div>
                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <x-form.select
                                name="status"
                                label="{{ __('site.projects.status') }}"
                                :options="$projectStatusOptions"
                                :selected="$projectStatusValue"
                                required
                            />
                        </div>
                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <x-form.select
                                name="priority"
                                label="{{ __('site.projects.priority') }}"
                                :options="$projectPriorityOptions"
                                :selected="$projectPriorityValue"
                                required
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <x-form.input
                                name="project_url"
                                label="{{ __('site.projects.project_url') }}"
                                type="url"
                                :value="$project?->project_url"
                                placeholder="{{ __('site.projects.project_url_placeholder') }}"
                            />
                        </div>
                    </div>

                    <div class="mt-3">
                        <x-form.textarea
                            name="description"
                            label="{{ __('site.projects.description') }}"
                            :value="$project?->description"
                            placeholder="{{ __('site.projects.description_placeholder') }}"
                            :rows="4"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{ __('site.projects.planning') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <x-form.select
                        name="manager_id"
                        label="{{ __('site.projects.manager') }}"
                        :options="$managers"
                        option-value="id"
                        option-label="employee_id_full_name"
                        :selected="$project?->manager_id"
                        select2
                    />
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0">
                    <x-form.select
                        name="team_id"
                        label="{{ __('site.projects.team') }}"
                        :options="$teams"
                        option-value="id"
                        option-label="name"
                        :selected="$project?->team_id"
                        select2
                    />
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-6">
                    <x-form.select
                        name="department_id"
                        label="{{ __('site.projects.department') }}"
                        :options="$departments"
                        option-value="id"
                        option-label="name"
                        :selected="$project?->department_id"
                        select2
                    />
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0">
                    <x-form.datepicker
                        name="start_date"
                        label="{{ __('site.projects.start_date') }}"
                        :value="$formatDate($project?->start_date)"
                    />
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-6">
                    <x-form.datepicker
                        name="planned_end_date"
                        label="{{ __('site.projects.planned_end_date') }}"
                        :value="$formatDate($project?->planned_end_date)"
                    />
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0">
                    <x-form.datepicker
                        name="actual_end_date"
                        label="{{ __('site.projects.actual_end_date') }}"
                        :value="$formatDate($project?->actual_end_date)"
                    />
                </div>
            </div>
        </div>
    </div>

    @if ($method === 'POST')
        @include('projects._assignment-fields', [
            'employees' => $employees,
            'memberInputs' => $memberInputs,
            'roles' => $roles,
        ])
    @endif

    <div class="card">
        <div class="card-body">
            <x-form-actions
                :show-reset="$method === 'POST'"
                show-cancel
                :url-cancel="route('projects.index')"
            />
        </div>
    </div>

</form>

@push('scripts')
    <script>
        $(function () {
            $('#project_avatar').dropify({
                imgFileExtensions: ['png', 'jpg', 'jpeg', 'webp'],
            });
        });
    </script>
@endpush

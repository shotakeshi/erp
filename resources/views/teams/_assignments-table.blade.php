@php
    $assignmentConfigs = [
        'member' => [
            'title' => __('site.teams.members'),
            'count' => $team->current_members_count,
            'addModal' => 'add-member-modal',
            'addLabel' => __('site.teams.add_members'),
            'history' => [
                'route' => 'teams.members.history',
                'label' => __('site.teams.member_history'),
                'icon' => 'fas fa-history',
            ],
        ],
        'manager' => [
            'title' => __('site.teams.managers'),
            'count' => $team->current_managers_count,
            'addModal' => 'add-manager-modal',
            'addLabel' => __('site.teams.add_managers'),
            'history' => [
                'route' => 'teams.managers.history',
                'label' => __('site.teams.open_history_page'),
                'icon' => 'fas fa-history',
            ],
        ],
    ];
    $assignmentConfig = $assignmentConfigs[$assignmentType];
    $historyTab = $assignmentConfig['history'];
    $hasHistoryRoute = \Illuminate\Support\Facades\Route::has($historyTab['route']);
@endphp

<div class="card-body">
    <div class="row">
        <div class="col-lg-6 d-flex align-items-center">{{ $assignmentConfig['title'] }}</div>
        <div class="col-lg-6 d-flex justify-content-end">
            @if ($hasHistoryRoute)
                <a
                        href="{{ route($historyTab['route'], $team) }}"
                        class="btn btn-sm btn-outline-info mr-1"
                >
                    <i class="{{ $historyTab['icon'] }} mr-1"></i>
                    {{ $historyTab['label'] }}
                </a>
            @endif
            <button
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    data-toggle="modal"
                    data-target="#{{ $assignmentConfig['addModal'] }}"
            >
                <i class="fas fa-plus mr-1"></i>
                {{ $assignmentConfig['addLabel'] }}
            </button>
        </div>
    </div>
    <div class="table-responsive mt-3">
    <table class="table table-bordered mb-0 table-centered">
        <thead>
            <tr>
                <th>{{ __('site.teams.employee') }}</th>
                <th>{{ __('site.teams.employee_code') }}</th>
                <th>{{ __('site.teams.department') }}</th>
                <th>{{ __('site.teams.position') }}</th>
                <th>{{ __('site.teams.start_date') }}</th>
                <th>{{ __('site.teams.user_lifecycle_status') }}</th>
                <th class="text-center">{{ __('site.teams.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assignments as $assignment)
                @php
                    $employee = $assignment->employee;
                @endphp
                <tr>
                    <td>
                        <div class="media align-items-center">
                            <span class="avatar-box thumb-sm align-self-center mr-2">
                                <span class="avatar-title bg-soft-info rounded-circle">
                                    <i class="fas fa-user"></i>
                                </span>
                            </span>
                            <div class="media-body">
                                @if ($employee->trashed())
                                    <span class="font-weight-bold">{{ $employee->full_name }}</span>
                                @else
                                    <a href="{{ route('employees.show', $employee) }}" class="font-weight-bold text-primary">
                                        {{ $employee->full_name }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $employee->department?->name ?? '-' }}</td>
                    <td>{{ $employee->position?->name ?? '-' }}</td>
                    <td class="text-nowrap">{{ $assignment->start_date->format('d/m/Y') }}</td>
                    <td><x-employee-lifecycle-badge :employee="$employee" /></td>
                    <td class="text-center">
                        @if ($employee->trashed())
                            <span class="text-muted font-12" title="{{ __('site.teams.deleted_employee_action_unavailable') }}">
                                <i class="fas fa-lock mr-1"></i>
                                {{ __('site.teams.action_unavailable') }}
                            </span>
                        @else
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                data-toggle="modal"
                                data-target="#remove-{{ $assignmentType }}-assignment-modal"
                                data-assignment-action="{{ route($destroyRoute, [$team, $employee]) }}"
                                data-employee-id="{{ $employee->id }}"
                                data-employee-name="{{ $employee->full_name }}"
                                data-start-date="{{ $assignment->start_date->toDateString() }}"
                                data-assignment-description="{{ __(
                                    $assignmentType === 'member'
                                        ? 'site.teams.remove_member_confirmation'
                                        : 'site.teams.remove_manager_confirmation',
                                    ['employee' => $employee->full_name],
                                ) }}"
                                title="{{ $assignmentType === 'member' ? __('site.teams.remove_member_title') : __('site.teams.remove_manager_title') }}"
                            >
                                <i class="fas fa-user-minus"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-user-friends d-block font-20 mb-2"></i>
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</div>

@include('teams._remove-assignment-modal', [
    'team' => $team,
    'assignments' => $assignments,
    'assignmentType' => $assignmentType,
    'destroyRoute' => $destroyRoute,
])

<div class="table-responsive">
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
                                aria-label="{{ $assignmentType === 'member' ? __('site.teams.remove_member_title') : __('site.teams.remove_manager_title') }}"
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

@include('teams._remove-assignment-modal', [
    'team' => $team,
    'assignments' => $assignments,
    'assignmentType' => $assignmentType,
    'destroyRoute' => $destroyRoute,
])

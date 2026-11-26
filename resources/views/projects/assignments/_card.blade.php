<div class="row">
    @forelse ($assignments as $assignment)
        @php
            $employee = $assignment->employee;
            $employeeIsDeleted = $employee->trashed();
        @endphp

        <div class="col-sm-6 col-lg-3">
            <div class="card team-card">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge badge-soft-primary">{{ $employee->employee_id }}</span>
                        <div class="dropdown d-inline-block">
                            <a
                                type="button"
                                class="nav-link dropdown-toggle arrow-none p-0"
                                id="project-assignment-actions-{{ $assignment->id }}"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <i class="fas fa-ellipsis-v font-16 text-muted"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right"
                                 aria-labelledby="project-assignment-actions-{{ $assignment->id }}">
                                @if (! $employeeIsDeleted)
                                    <a class="dropdown-item text-gray"
                                       href="{{ route('employees.show', $employee) }}">
                                        {{ __('common.button.view') }}
                                    </a>
                                @endif
                                <a
                                    type="button"
                                    class="dropdown-item text-danger"
                                    data-toggle="modal"
                                    data-target="#remove-assignment-modal"
                                    data-assignment-action="{{ route('projects.assignments.destroy', [$project, $employee]) }}"
                                    data-employee-id="{{ $employee->id }}"
                                    data-start-date="{{ $assignment->start_date->toDateString() }}"
                                    data-assignment-description="{{ __('site.projects.remove_member_confirmation', ['employee' => $employee->full_name]) }}"
                                    title="{{ __('site.projects.remove_member_title') }}"
                                    aria-label="{{ __('site.projects.remove') }}"
                                >
                                    {{ __('site.projects.remove') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <img
                        src="{{ image_url($employee->avatar) }}"
                        alt="{{ $employee->full_name }}"
                        class="rounded-circle thumb-lg d-block mx-auto mb-2"
                    >

                    <h5 class="client-name text-truncate mt-0 mb-3" title="{{ $employee->full_name }}">
                        @if ($employeeIsDeleted)
                            <span>{{ $employee->full_name }}</span>
                        @else
                            <a href="{{ route('employees.show', $employee) }}" class="text-primary">
                                {{ $employee->full_name }}
                            </a>
                        @endif
                    </h5>

                    @if ($employeeIsDeleted)
                        <span class="badge badge-soft-danger mb-2">
                            <i class="ti ti-close mr-1" aria-hidden="true"></i>
                            {{ __('site.projects.employee_deleted') }}
                        </span>
                        <p class="font-12 text-muted text-truncate mb-0" title="{{ $assignment->role }}">{{ $assignment->role }}</p>
                    @else
                        <p class="mt-2 mb-3">
                            <x-form.editable
                                name="role"
                                :pk="$assignment->id"
                                :url="route('projects.assignments.update', [$project, $employee])"
                                :title="__('site.projects.update_role')"
                                :maxlength="50"
                            >{{ $assignment->role }}</x-form.editable>
                        </p>
                    @endif

                    <div class="font-12 text-muted text-nowrap mb-2">
                        <span class="mr-2" title="{{ $employee->email }}">
                            <i class="fas fa-envelope text-info mr-2" aria-hidden="true"></i>{{ $employee->email }}
                        </span>
                        <span class="text-truncate">
                            <i class="fas fa-phone text-info mr-2" aria-hidden="true"></i>{{ format_phone($employee->phone) ?: '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-4">
                <i class="fas fa-user-friends d-block font-20 mb-2" aria-hidden="true"></i>
                {{ __('site.projects.no_current_members') }}
            </div>
        </div>
    @endforelse
</div>

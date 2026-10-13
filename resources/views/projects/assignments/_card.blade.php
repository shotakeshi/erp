<div class="row">
    @forelse ($assignments as $assignment)
        @php
            $employee = $assignment->employee;
            $employeeIsDeleted = $employee->trashed();
            $isRoleFormSubmitted = (string) old('role_employee_id') === (string) $employee->id;
            $roleError = $isRoleFormSubmitted
                ? $errors->getBag(\App\Http\Requests\ProjectAssignmentRequest::UPDATE_ERROR_BAG)->first('role')
                : null;
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
                        <form action="{{ route('projects.assignments.update', [$project, $employee]) }}" method="POST" class="mt-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="role_employee_id" value="{{ $employee->id }}">
                            <label class="sr-only" for="assignment-role-{{ $assignment->id }}">{{ __('site.projects.role') }}</label>
                            <div class="input-group input-group-sm mb-3">
                                <input
                                    id="assignment-role-{{ $assignment->id }}"
                                    name="role"
                                    value="{{ $isRoleFormSubmitted ? old('role') : $assignment->role }}"
                                    placeholder="{{ __('site.projects.role') }}"
                                    @class(['form-control text-center', 'is-invalid' => $roleError])
                                    maxlength="255"
                                    required
                                    @if ($roleError) aria-describedby="assignment-role-error-{{ $assignment->id }}" @endif
                                >
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-soft-primary" title="{{ __('site.projects.update_role') }}" aria-label="{{ __('site.projects.update_role') }}">
                                        <i class="fas fa-check" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            @if ($roleError)
                                <div id="assignment-role-error-{{ $assignment->id }}" class="invalid-feedback d-block text-left">{{ $roleError }}</div>
                            @endif
                        </form>
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

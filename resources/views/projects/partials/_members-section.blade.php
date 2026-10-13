@if ($method === 'POST')
    @php
        $memberErrors = $errors->get('members.*');
    @endphp

    <div class="card" id="project-create-members" data-delete-label="{{ __('common.button.delete') }}">
        <div class="card-header">
            <span>{{ __('site.projects.members') }}</span>
            <button
                type="button"
                class="btn btn-sm btn-outline-primary float-right"
                data-toggle="modal"
                data-target="#project-members-modal"
            >
                <i class="fas fa-plus mr-1" aria-hidden="true"></i>
                {{ __('site.projects.add_member') }}
            </button>
        </div>
        <div class="card-body">
            <x-form.error name="members" />

            @if ($memberErrors !== [])
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 pl-3">
                        @foreach ($memberErrors as $messages)
                            @foreach ($messages as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.projects.employee') }}</th>
                            <th>{{ __('site.projects.member_detail') }}</th>
                            <th>{{ __('site.projects.role') }}</th>
                            <th>{{ __('site.projects.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody data-assignment-rows>
                        <tr data-assignment-empty>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-users d-block font-20 mb-2" aria-hidden="true"></i>
                                {{ __('site.projects.no_members_selected') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-employee-picker-modal
        id="project-members-modal"
        :title="__('site.projects.members_modal_title')"
        :url="route('projects.employee-options')"
        :labels="[
            'remove' => __('site.projects.remove_member'),
            'members' => __('site.projects.members'),
        ]"
        :confirm-label="__('site.projects.confirm')"
    />

    <script type="application/json" data-initial-members>@json($initialMembers)</script>

    @push('scripts')
        <script src="{{ asset('js/assignment-table.js') }}"></script>
        <script>
            $(function () {
                AssignmentTable.init({
                    root: '#project-create-members',
                    modal: '#project-members-modal',
                    roleListId: 'project-role-suggestions',
                    roleRequired: true,
                    initialAssignments: JSON.parse($('[data-initial-members]').text()),
                });
            });
        </script>
    @endpush
@else
    @php
        $oldMembers = collect($memberInputs ?? [])->keyBy('assignment_id');
    @endphp

    <div class="card">
        <div class="card-header">{{ __('site.projects.members') }}</div>
        <div class="card-body">
            <x-form.error name="members" />
            <x-form.error name="members.*" />
            <div class="table-responsive">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.projects.employee') }}</th>
                            <th>{{ __('site.projects.member_detail') }}</th>
                            <th>{{ __('site.projects.role') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignments as $assignment)
                            @php
                                $employee = $assignment->employee;
                                $roleField = 'members.' . $loop->index . '.role';
                            @endphp
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <span class="avatar-box thumb-sm mr-2">
                                            @if ($employee?->avatar)
                                                <img src="{{ image_url($employee->avatar) }}" class="thumb-sm rounded-circle" alt="">
                                            @else
                                                <span class="avatar-title bg-soft-info rounded-circle">
                                                    <i class="fas fa-user" aria-hidden="true"></i>
                                                </span>
                                            @endif
                                        </span>
                                        <span>{{ $employee?->full_name ?? '-' }}</span>
                                        @if ($employee?->trashed())
                                            <span class="badge badge-soft-danger ml-1">
                                                <i class="ti ti-close mr-1" aria-hidden="true"></i>{{ __('site.projects.employee_deleted') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $employee?->position?->name ?? '-' }}</td>
                                <td>
                                    <input type="hidden" name="members[{{ $loop->index }}][assignment_id]" value="{{ $assignment->id }}">
                                    <input
                                        type="text"
                                        name="members[{{ $loop->index }}][role]"
                                        class="form-control @error($roleField) is-invalid @enderror"
                                        value="{{ data_get($oldMembers->get($assignment->id), 'role', $assignment->role) }}"
                                        list="project-role-suggestions"
                                        maxlength="255"
                                        aria-label="{{ __('site.projects.role') }} — {{ $employee?->full_name }}"
                                    >
                                    <x-form.error :name="$roleField" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-users d-block font-20 mb-2" aria-hidden="true"></i>
                                    {{ __('site.projects.no_current_members') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<datalist id="project-role-suggestions">
    @foreach ($roles as $role)
        <option value="{{ $role }}"></option>
    @endforeach
</datalist>

@php
    $isCreate = $method === 'POST';
@endphp

@if ($isCreate)
    <div class="card" id="team-create-members" data-delete-label="{{ __('common.button.delete') }}">
        <div class="card-header">
            <span>{{ __('site.teams.add_member_to_team') }}</span>
            <button
                type="button"
                class="btn btn-sm btn-outline-primary float-right"
                data-toggle="modal"
                data-target="#team-members-modal"
            >
                <i class="fas fa-plus mr-1" aria-hidden="true"></i>
                {{ __('site.teams.add_members') }}
            </button>
        </div>
        <div class="card-body">
            <x-form.error name="members.*.role" />
            <div class="table-responsive">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.teams.employee') }}</th>
                            <th>{{ __('site.teams.member_detail') }}</th>
                            <th>{{ __('site.teams.role') }}</th>
                            <th>{{ __('site.teams.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody data-assignment-rows>
                        <tr data-assignment-empty>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-users d-block font-20 mb-2" aria-hidden="true"></i>
                                {{ __('site.teams.no_members_selected') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-employee-picker-modal
        id="team-members-modal"
        :title="__('site.teams.members_modal_title')"
        :url="route('teams.employee-options')"
        :confirm-label="__('site.teams.confirm')"
    />

    <script type="application/json" data-initial-members>@json($initialMembers)</script>
    @push('scripts')
        <script src="{{ asset('js/assignment-table.js') }}"></script>
        <script>
            $(function () {
                AssignmentTable.init({
                    root: '#team-create-members',
                    modal: '#team-members-modal',
                    roleListId: 'team-role-suggestions',
                    initialAssignments: JSON.parse($('[data-initial-members]').text()),
                });
            });
        </script>
    @endpush
@else
    @php
        $oldMembers = collect($memberInputs)->keyBy('assignment_id');
    @endphp

    <div class="card">
        <div class="card-header">{{ __('site.teams.members') }}</div>
        <div class="card-body">
            <x-form.error name="members" />
            <x-form.error name="members.*" />
            <div class="table-responsive">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.teams.employee') }}</th>
                            <th>{{ __('site.teams.member_detail') }}</th>
                            <th>{{ __('site.teams.role') }}</th>
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
                                                <i class="ti ti-close mr-1" aria-hidden="true"></i>{{ __('site.teams.employee_deleted') }}
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
                                        list="team-role-suggestions"
                                        aria-label="{{ __('site.teams.role') }} — {{ $employee?->full_name }}"
                                    >
                                    <x-form.error :name="$roleField" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-users d-block font-20 mb-2" aria-hidden="true"></i>
                                    {{ __('site.teams.no_current_members') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<datalist id="team-role-suggestions">
    @foreach ($roles as $role)
        <option value="{{ $role }}"></option>
    @endforeach
</datalist>

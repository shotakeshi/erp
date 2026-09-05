@php
    $team = $team ?? null;
    $employees = $employees ?? collect();
    $roles = $roles ?? collect();

     $employeeById = $employees->keyBy('id');

    $employeeOptions = $employees
        ->map(fn ($employee) => [
            'employee_id' => (int) $employee->id,
            'name' => $employee->full_name,
            'detail' => $employee->position?->name ?? '-',
            'avatar_url' => $employee->avatar ? image_url($employee->avatar) : null,
        ])
        ->values()
        ->all();

    $initialMembers = collect($method === 'POST' ? old('members', []) : [])
        ->map(function ($member) use ($employeeById): array {
            $employee = $employeeById->get($member['employee_id']);
            return [
                'employee_id' => $member['employee_id'],
                'name' => $employee?->full_name ?? '',
                'detail' => $employee?->position?->name ?? '-',
                'avatar_url' => $employee?->avatar ? image_url($employee->avatar) : null,
                'role' => $member['role'],
                'is_manager' => (bool) $member['is_manager'] ?? false,
            ];
        })
        ->values()
        ->all();
@endphp

@push('css')
    <link href="{{ asset('css/dropify/dropify.min.css') }}" rel="stylesheet">
@endpush

<form class="form-horizontal form-material mb-0" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    {{ __('site.teams.detail') }}
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <x-form.label for="logo">
                                {{ __('site.teams.logo') }}
                            </x-form.label>
                            <input
                                type="file"
                                name="logo"
                                id="logo"
                                accept="image/jpeg,image/png,image/webp"
                                class="dropify"
                                data-default-file="{{ image_url($team?->logo) }}"
                            />
                            @if ($team?->logo)
                                <div class="form-check mt-2">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        name="remove_logo"
                                        id="remove_logo"
                                        value="1"
                                    >
                                    <label class="form-check-label" for="remove_logo">
                                        {{ __('site.teams.remove_logo') }}
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-8">
                            <x-form.input
                                name="name"
                                label="{{ __('site.teams.name') }}"
                                :value="$team?->name"
                                placeholder="{{ __('site.teams.name_placeholder') }}"
                                required
                                autofocus
                            />
                            <div class="mt-3">
                                <x-form.input
                                    name="code"
                                    label="{{ __('site.teams.code') }}"
                                    :value="$team?->code"
                                    placeholder="{{ __('site.teams.code_placeholder') }}"
                                    format="uppercase"
                                    required
                                />
                            </div>
                            <div class="mt-3">
                                <x-form.textarea
                                    name="description"
                                    label="{{ __('site.teams.description') }}"
                                    :value="$team?->description"
                                    placeholder="{{ __('site.teams.description') }}"
                                    :rows="2"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($method === 'POST')
        <div class="row">
            <div class="col-lg-12">
                <div
                    class="card"
                    id="team-create-form"
                    data-empty-message="{{ __('site.teams.no_members_selected') }}"
                    data-no-employees-message="{{ __('site.teams.no_available_employees') }}"
                    data-remove-label="{{ __('site.teams.remove') }}"
                    data-delete-label="{{ __('common.button.delete') }}"
                    data-add-label="{{ __('common.button.add') }}"
                    data-manager-label="{{ __('site.teams.team_manager') }}"
                >
                    <div class="card-header">
                        <span>{{ __('site.teams.add_member_to_team') }}</span>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger float-right"
                            data-toggle="modal"
                            data-target="#invite-members-modal"
                        >
                            <i class="fas fa-plus"></i>
                            {{ __('site.teams.add_new_member') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <x-form.error name="members.*.role" />
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('site.teams.employee') }}</th>
                                        <th>{{ __('site.teams.member_detail') }}</th>
                                        <th>{{ __('site.teams.role') }}</th>
                                        <th>{{ __('site.teams.team_manager') }}</th>
                                        <th>{{ __('site.teams.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody data-team-members>
                                    <tr data-team-members-empty>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-users d-block font-20 mb-2"></i>
                                            {{ __('site.teams.no_members_selected') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <x-form-actions
                                show-reset
                                show-cancel
                                :url-cancel="route('teams.index')"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <datalist id="team-role-suggestions">
            @foreach ($roles as $role)
                <option value="{{ $role }}"></option>
            @endforeach
        </datalist>

        <div
            class="modal fade"
            id="invite-members-modal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="invite-members-modal-label"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0">
                    <div class="modal-header bg-soft-success">
                        <h5 class="modal-title" id="invite-members-modal-label">
                            {{ __('site.teams.members_modal_title') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.button.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0 text-muted pl-3 pr-2">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input
                                type="search"
                                class="form-control bg-light border-0 pl-0"
                                placeholder="{{ __('site.teams.search_employee_placeholder') }}"
                                aria-label="{{ __('site.teams.search_employee_placeholder') }}"
                                data-member-search
                            >
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <h6 class="mb-0 mr-3">{{ __('site.teams.members') }}:</h6>
                            <div class="d-flex align-items-center flex-wrap" data-selected-members></div>
                        </div>

                        <div class="team-members-scroll pr-1" data-employee-list></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light px-4" data-dismiss="modal">
                            {{ __('common.button.cancel') }}
                        </button>
                        <button type="button" class="btn btn-success px-4" data-member-confirm>
                            {{ __('site.teams.confirm') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script type="application/json" data-team-employees>@json($employeeOptions)</script>
        <script type="application/json" data-team-initial-members>@json($initialMembers)</script>
    @else
        @php
            $oldMembers = collect(old('members', []))->keyBy('assignment_id');
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
                                <th>{{ __('site.teams.team_manager') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <span class="avatar-box thumb-sm mr-2">
                                                @if ($assignment->employee?->avatar)
                                                    <img src="{{ image_url($assignment->employee->avatar) }}" class="thumb-sm rounded-circle" alt="">
                                                @else
                                                    <span class="avatar-title bg-soft-info rounded-circle"><i class="fas fa-user"></i></span>
                                                @endif
                                            </span>
                                            <span>{{ $assignment->employee?->full_name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $assignment->employee?->position?->name ?? '-' }}</td>
                                    <td>
                                        <input type="hidden" name="members[{{ $loop->index }}][assignment_id]" value="{{ $assignment->id }}">
                                        <input
                                            type="text"
                                            name="members[{{ $loop->index }}][role]"
                                            class="form-control @error('members.'.$loop->index.'.role') is-invalid @enderror"
                                            value="{{ data_get($oldMembers->get($assignment->id), 'role', $assignment->role) }}"
                                            list="team-role-suggestions"
                                            aria-label="{{ __('site.teams.role') }} — {{ $assignment->employee?->full_name }}"
                                            required
                                        >
                                        <x-form.error :name="'members.'.$loop->index.'.role'" />
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" disabled @checked($assignment->type === \App\Enums\TeamAssignmentType::MANAGER) aria-label="{{ __('site.teams.team_manager') }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-users d-block font-20 mb-2"></i>
                                        {{ __('site.teams.no_current_members') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="form-group mt-3 mb-0">
                    <x-form-actions
                        :show-reset="false"
                        show-cancel
                        :url-cancel="route('teams.show', $team)"
                    />
                </div>
            </div>
        </div>
        <datalist id="team-role-suggestions">
            @foreach ($roles as $role)
                <option value="{{ $role }}"></option>
            @endforeach
        </datalist>
    @endif
</form>

@push('scripts')
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>
    <script>
        $(function () {
            $('#logo').dropify({
                imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'],
            });
        });
    </script>
    @if ($method === 'POST')
        <script src="{{ asset('js/teams/team-create.js') }}"></script>
    @endif
@endpush

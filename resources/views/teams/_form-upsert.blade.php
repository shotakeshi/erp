@php
    $team = $team ?? null;
    $employees = $employees ?? collect();
    $roles = $roles ?? collect();

     $employeeById = $employees->keyBy('id');

    $initialMembers = collect($method === 'POST' ? old('members', []) : [])
        ->map(function ($member) use ($employeeById): array {
            $employee = $employeeById->get($member['employee_id'] ?? null);
            return [
                'employee_id' => $member['employee_id'] ?? null,
                'name' => $employee?->full_name ?? '',
                'detail' => $employee?->position?->name ?? '-',
                'avatar_url' => $employee?->avatar ? image_url($employee->avatar) : null,
                'role' => $member['role'] ?? '',
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
                    data-delete-label="{{ __('common.button.delete') }}"
                >
                    <div class="card-header">
                        <span>{{ __('site.teams.add_member_to_team') }}</span>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary float-right"
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
                                        <th>{{ __('site.teams.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody data-team-members>
                                    <tr data-team-members-empty>
                                        <td colspan="4" class="text-center text-muted py-4">
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
                        @include('teams._employee-picker', ['employeeOptionsUrl' => route('teams.employee-options')])
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                @php $employee = $assignment->employee @endphp
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <span class="avatar-box thumb-sm mr-2">
                                                @if ($employee->avatar)
                                                    <img src="{{ image_url($employee->avatar) }}" class="thumb-sm rounded-circle" alt="">
                                                @else
                                                    <span class="avatar-title bg-soft-info rounded-circle"><i class="fas fa-user"></i></span>
                                                @endif
                                            </span>
                                            <span>{{ $employee->full_name ?? '-' }}</span>
                                            @if($employee->trashed())
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
                                            class="form-control @error('members.'.$loop->index.'.role') is-invalid @enderror"
                                            value="{{ data_get($oldMembers->get($assignment->id), 'role', $assignment->role) }}"
                                            list="team-role-suggestions"
                                            aria-label="{{ __('site.teams.role') }} — {{ $employee?->full_name }}"
                                        >
                                        <x-form.error :name="'members.'.$loop->index.'.role'" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
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
                        :url-cancel="route('teams.members.index', $team)"
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
        <script>
            $(function () {

                const $form = $('#team-create-form');
                const $modal = $('#invite-members-modal');
                const $membersTable = $form.find('[data-team-members]');
                const $emptyRow = $membersTable.find('[data-team-members-empty]').first().clone();
                const $confirm = $modal.find('[data-member-confirm]');
                let members = JSON.parse($('[data-team-initial-members]').text());
                const picker = TeamEmployeePicker.create($modal.find('[data-employee-picker]'));
                const appendAvatar = TeamEmployeePicker.appendAvatar;
                const getEmployeeId = (employee) => Number(employee.employee_id);
                const labels = { delete: $form.data('delete-label') };

                // Render members và các input sẽ được gửi khi submit form.
                const renderMembers = function () {
                    $membersTable.empty();

                    if (! members.length) {
                        $membersTable.append($emptyRow.clone());
                        return;
                    }

                    members.forEach(function (member, index) {
                        const memberId = getEmployeeId(member);
                        const fieldNames = {
                            employeeId: `members[${index}][employee_id]`,
                            role: `members[${index}][role]`,
                        };

                        const $row = $(
                        `<tr>
                            <td>
                                <div class="media align-items-center">
                                    <span data-member-avatar></span>
                                    <div class="media-body">
                                        <input type="hidden" data-member-id>
                                        <span data-member-name></span>
                                    </div>
                                </div>
                            </td>
                            <td data-member-detail></td>
                            <td>
                                <input
                                    type="text"
                                    class="form-control"
                                    list="team-role-suggestions"
                                    data-member-role
                                >
                            </td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    class="btn btn-circle btn-outline-danger"
                                    data-member-delete
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`,
                        );

                        $row.attr('data-employee-id', memberId);

                        $row.find('[data-member-id]')
                            .attr('name', fieldNames.employeeId)
                            .val(memberId);

                        appendAvatar(
                            $row.find('[data-member-avatar]'),
                            member,
                            'thumb-xs mr-2',
                            'avatar-title bg-soft-primary text-primary rounded-circle',
                        );
                        $row.find('[data-member-name]').text(member.name);
                        $row.find('[data-member-detail]').text(member.detail || '-');

                        $row.find('[data-member-role]')
                            .attr({
                                name: fieldNames.role,
                                'data-employee-id': memberId,
                            })
                            .val(member.role || '');

                        $row.find('[data-member-delete]').attr({
                            title: labels.delete,
                            'aria-label': labels.delete,
                            'data-employee-id': memberId,
                        });

                        $row.appendTo($membersTable);
                    });
                };

                $modal.on('show.bs.modal', function () {
                    picker.open([], members.map(getEmployeeId));
                });
                $modal.on('hidden.bs.modal', picker.close);

                // Xác nhận selection, chuyển thành members để tạo các input submit trong bảng.
                $confirm.on('click', function () {
                    members.push(...picker.selected().map(function (employee) {
                        return {
                            employee_id: getEmployeeId(employee),
                            name: employee.name,
                            detail: employee.detail,
                            avatar_url: employee.avatar_url,
                            role: '',
                        };
                    }));

                    renderMembers();
                    $modal.modal('hide');
                });

                // Giữ state members đồng bộ khi người dùng thay đổi role
                $membersTable.on('input', '[data-member-role]', function () {
                    const id = Number($(this).data('employee-id'));
                    const member = members.find(function (item) {
                        return getEmployeeId(item) === id;
                    });

                    if (member) {
                        member.role = $(this).val();
                    }
                });

                // Xoá member khỏi state rồi render lại bảng để đánh lại index của các field names.
                $membersTable.on('click', '[data-member-delete]', function () {
                    const id = Number($(this).data('employee-id'));

                    members = members.filter(function (member) {
                        return getEmployeeId(member) !== id;
                    });
                    renderMembers();
                });

                // Render state ban đầu, bao gồm dữ liệu old input sau khi backend validation lỗi.
                renderMembers();
            });
        </script>
    @endif
@endpush

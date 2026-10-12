@php
    $memberInputs = collect($memberInputs ?? []);
    $roles = $roles ?? collect();
    $employeeById = $employees->keyBy('id');
    $initialMembers = $memberInputs
        ->map(function (array $member) use ($employeeById): array {
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

<datalist id="project-role-suggestions">
    @foreach ($roles as $role)
        <option value="{{ $role }}"></option>
    @endforeach
</datalist>

<div
    class="modal fade"
    id="project-members-modal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="project-members-modal-label"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-success">
                <h5 class="modal-title" id="project-members-modal-label">
                    {{ __('site.projects.members_modal_title') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.button.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <x-employee-picker
                    :url="route('projects.employee-options')"
                    :labels="[
                        'remove' => __('site.projects.remove_member'),
                        'members' => __('site.projects.members'),
                    ]"
                />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal">
                    {{ __('common.button.cancel') }}
                </button>
                <button type="button" class="btn btn-success px-4" data-assignment-confirm>
                    {{ __('site.projects.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

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

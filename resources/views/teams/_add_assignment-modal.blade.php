@php
    $modalId = 'add-member-modal';
    $oldEmployeeIds = collect(old('employee_ids', []))
        ->map(static fn ($employeeId): int => (int) $employeeId)
        ->values()
        ->all();
    $employeeOptions = $employees
        ->map(static fn ($employee): array => [
            'employee_id' => (int) $employee->id,
            'name' => $employee->full_name,
            'detail' => $employee->position?->name ?? '-',
            'avatar_url' => $employee->avatar ? image_url($employee->avatar) : null,
        ])
        ->values()
        ->all();
    $shouldOpen = $errors->has('employee_ids')
        || $errors->get('employee_ids.*') !== [];
@endphp

<div
    class="modal fade team-add-assignment-modal"
    id="{{ $modalId }}"
    data-auto-open="{{ $shouldOpen ? 'true' : 'false' }}"
    data-add-label="{{ __('common.button.add') }}"
    data-remove-label="{{ __('site.teams.remove') }}"
    data-no-employees-message="{{ __('site.teams.no_available_employees') }}"
    data-request-failed-message="{{ __('common.messages.create_failed') }}"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            <form action="{{ $storeAction }}" method="POST" data-add-assignment-form>
                @csrf

                <div class="modal-header bg-soft-success">
                    <h5 class="modal-title" id="{{ $modalId }}-title">
                        {{ __('site.teams.add_members_to_team', ['team' => $team->name]) }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.button.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-3">
                    <div class="alert alert-danger d-none" role="alert" data-add-assignment-error></div>

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
                            data-assignment-member-search
                        >
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <h6 class="mb-0 mr-3">{{ __('site.teams.members') }}:</h6>
                        <div class="d-flex align-items-center flex-wrap" data-selected-members></div>
                    </div>

                    <div class="team-members-scroll pr-1" data-assignment-employee-list></div>

                    <div data-selected-member-inputs></div>

                    <div class="d-none">
                        <x-form.error name="employee_ids" />
                        <x-form.error name="employee_ids.*" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal">
                        {{ __('common.button.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success px-4" data-add-assignment-confirm>
                        {{ __('site.teams.confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/json" data-assignment-employees>@json($employeeOptions)</script>
<script type="application/json" data-assignment-initial-member-ids>@json($oldEmployeeIds)</script>

@push('css')
    <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/teams/team-assignment.js') }}"></script>
@endpush

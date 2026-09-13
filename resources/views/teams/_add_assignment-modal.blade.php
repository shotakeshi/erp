@php
    $modalId = 'add-member-modal';
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
    data-request-failed-message="{{ __('common.messages.create_failed') }}"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            <form action="{{ $storeAction }}" method="POST" class="d-flex flex-column overflow-hidden" data-add-assignment-form>
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

                    @include('teams._employee-picker', ['employeeOptionsUrl' => route('teams.members.employee-options', $team)])

                    <div data-selected-member-inputs></div>

                    <div data-add-assignment-server-errors>
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

@push('css')
    <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/teams/team-assignment.js') }}"></script>
@endpush

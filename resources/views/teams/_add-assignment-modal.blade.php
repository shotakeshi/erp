@php
    $modalId = 'add-assignment-modal';
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
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $modalId }}-title"
    aria-hidden="true"
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

                    <x-employee-picker :url="route('teams.assignments.employee-options', $team)" />

                    <div data-selected-assignment-inputs></div>

                    <div data-add-assignment-server-errors>
                        <x-form.error name="employee_ids" />
                        <x-form.error name="employee_ids.*" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-gray px-4" data-dismiss="modal">
                        {{ __('common.button.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary px-4" data-add-assignment>
                        <i class="fas fa-save"></i>
                        {{ __('common.button.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/json" data-assignment-employees>@json($employeeOptions)</script>

@push('scripts')
    <script>
        $(function () {
            const initializeAddAssignmentModal = function ($modal) {
                const $form = $modal.find('[data-add-assignment-form]');
                const $selectedAssignmentInputs = $modal.find('[data-selected-assignment-inputs]');
                const $errorMessage = $modal.find('[data-add-assignment-error]');
                const $saveButton = $form.find('[data-add-assignment]');
                const initialEmployees = JSON.parse($('[data-assignment-employees]').text());
                let shouldRestoreInitialSelection = $modal.attr('data-auto-open') === 'true';
                let isSubmitting = false;
                const picker = EmployeePicker.create($modal.find('[data-employee-picker]'), function (employees) {
                    $selectedAssignmentInputs.empty();
                    employees.forEach(function (employee) {
                        $('<input>', { type: 'hidden', name: 'employee_ids[]', value: employee.employee_id })
                            .appendTo($selectedAssignmentInputs);
                    });
                    $errorMessage.addClass('d-none').empty();
                });

                $modal.on('show.bs.modal', function () {
                    if (!shouldRestoreInitialSelection) {
                        $modal.find('[data-add-assignment-server-errors]').empty();
                    }
                    picker.open(shouldRestoreInitialSelection ? initialEmployees : []);
                    shouldRestoreInitialSelection = false;
                });
                $modal.on('hidden.bs.modal', picker.close);

                $form.on('submit', function (event) {
                    event.preventDefault();
                    if (isSubmitting) {
                        return;
                    }
                    isSubmitting = true;
                    $saveButton.prop('disabled', true);
                    $modal.find('[data-add-assignment-server-errors]').empty();
                    $errorMessage.addClass('d-none').empty();

                    $.ajax({
                        url: $form.attr('action'),
                        method: $form.attr('method'),
                        data: $form.serialize(),
                        dataType: 'json',
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    }).done(function (response) {
                        window.location.assign(response.redirect_url || $form.attr('action'));
                    }).fail(function (response) {
                        const firstError = Object.values(response.responseJSON?.errors || {}).flat()[0];
                        $errorMessage.text(firstError || response.responseJSON?.message || $modal.data('request-failed-message'))
                            .removeClass('d-none');
                    }).always(function () {
                        isSubmitting = false;
                        $saveButton.prop('disabled', false);
                    });
                });

                if ($modal.attr('data-auto-open') === 'true') {
                    $modal.modal('show');
                }
            };

            $('.team-add-assignment-modal').each(function () {
                initializeAddAssignmentModal($(this));
            });
        });
    </script>
@endpush

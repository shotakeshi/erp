@props([
    'owner',
    'assignments',
    'destroyRoute',
    'langPrefix',
])

@php
    $modalId = 'remove-assignment-modal';
    $reopenAssignment = $assignments->first(
        static fn ($assignment): bool => (string) $assignment->employee_id === (string) old('remove_employee_id'),
    );
    $shouldOpen = ($errors->has('end_date') || $errors->has('end_reason_note'))
        && $reopenAssignment !== null;
    $reopenEmployee = $reopenAssignment?->employee;
@endphp

<div
    class="modal fade remove-assignment-modal"
    id="{{ $modalId }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $modalId }}-title"
    aria-hidden="true"
    data-auto-open="{{ $shouldOpen ? 'true' : 'false' }}"
    @if ($reopenAssignment && $reopenEmployee)
        data-assignment-action="{{ route($destroyRoute, [$owner, $reopenEmployee]) }}"
        data-employee-id="{{ $reopenEmployee->id }}"
        data-start-date="{{ $reopenAssignment->start_date->toDateString() }}"
        data-assignment-description="{{ __($langPrefix.'.remove_member_confirmation', ['employee' => $reopenEmployee->full_name]) }}"
    @endif
>
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-remove-assignment-form>
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}-title">
                        {{ __($langPrefix.'.remove_member_title') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-3" data-remove-assignment-description>
                        @if ($reopenEmployee)
                            {{ __($langPrefix.'.remove_member_confirmation', ['employee' => $reopenEmployee->full_name]) }}
                        @endif
                    </p>

                    <input type="hidden" name="remove_employee_id" value="{{ old('remove_employee_id') }}" data-remove-employee-id>

                    <div class="form-group mb-0">
                        <x-form.datepicker
                            name="end_date"
                            label="{{ __($langPrefix.'.end_date') }}"
                            :value="old('end_date', now()->format('d/m/Y'))"
                            :min-date="$reopenAssignment?->start_date?->format('d/m/Y')"
                            :max-date="now()->format('d/m/Y')"
                            required
                            data-remove-assignment-end-date
                        />
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <x-form.textarea
                            name="end_reason_note"
                            label="{{ __($langPrefix.'.end_reason_note') }}"
                            placeholder="{{ __($langPrefix.'.end_reason_note_placeholder') }}"
                            :rows="4"
                            maxlength="5000"
                        />
                    </div>

                    <small class="form-text text-muted mt-3">
                        {{ __($langPrefix.'.membership_history_retained') }}
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-gray" data-dismiss="modal">
                        {{ __('common.button.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-outline-danger" data-remove-assignment-submit>
                        <i class="fas fa-user-minus mr-1"></i>
                        {{ __($langPrefix.'.remove') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@once
    @push('css')
        <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    @endpush
@endonce

@push('scripts')
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function () {
            const DISPLAY_DATE_FORMAT = 'DD/MM/YYYY';
            const ASSIGNMENT_DATE_FORMAT = 'YYYY-MM-DD';

            const parseDate = function (value, format) {
                if (!value || typeof moment !== 'function') {
                    return null;
                }

                const parsedDate = moment(value, format, true);

                return parsedDate.isValid() ? parsedDate.startOf('day') : null;
            };

            const updateEndDatePicker = function (input, assignmentStartDate, preserveEndDate) {
                const defaultEndDate = input.attr('data-max-date') || '';

                if (! preserveEndDate) {
                    input.val(defaultEndDate);
                }

                const datePicker = input.data('daterangepicker');

                if (!datePicker) {
                    return;
                }

                const minDate = parseDate(assignmentStartDate, ASSIGNMENT_DATE_FORMAT);
                const maxDate = datePicker.maxDate;

                if (minDate) {
                    // If `start_date` exceeds `maxDate`, set `minDate` to `maxDate`.
                    datePicker.minDate = maxDate && minDate.isAfter(maxDate, 'day')
                        ? maxDate.clone().startOf('day')
                        : minDate;
                }

                const selectedDate = preserveEndDate
                    ? (datePicker.endDate || datePicker.startDate).clone()
                    : parseDate(defaultEndDate, DISPLAY_DATE_FORMAT);

                if (!selectedDate) {
                    return;
                }

                datePicker.setStartDate(selectedDate.clone());
                datePicker.setEndDate(selectedDate.clone());
                datePicker.updateView();
            };

            const initializeRemoveAssignmentModal = function (modal) {
                const form = modal.find('[data-remove-assignment-form]');
                const description = modal.find('[data-remove-assignment-description]');
                const employeeId = modal.find('[data-remove-employee-id]');
                const endDate = modal.find('[data-remove-assignment-end-date]');
                const submitButton = form.find('[data-remove-assignment-submit]');

                const resetSubmitButton = function () {
                    submitButton.prop('disabled', false);
                };

                const populateModal = function (source, preserveEndDate) {
                    form.attr('action', source.data('assignment-action'));
                    description.text(source.data('assignment-description'));
                    employeeId.val(source.data('employee-id'));

                    updateEndDatePicker(
                        endDate,
                        source.data('start-date'),
                        preserveEndDate,
                    );
                };

                modal.on('show.bs.modal', function (event) {
                    resetSubmitButton();

                    const trigger = $(event.relatedTarget);

                    if (trigger.length) {
                        form.find('[name="end_reason_note"]').val('');
                        form.find('.is-invalid').removeClass('is-invalid');
                        form.find('.invalid-feedback').remove();
                        populateModal(trigger, false);
                    }
                });

                modal.on('hidden.bs.modal', resetSubmitButton);

                form.on('submit', function () {
                    submitButton.prop('disabled', true);
                });

                if (modal.attr('data-auto-open') === 'true') {
                    populateModal(modal, true);
                    modal.modal('show');
                }
            };

            $('.remove-assignment-modal').each(function () {
                initializeRemoveAssignmentModal($(this));
            });
        });
    </script>
@endpush

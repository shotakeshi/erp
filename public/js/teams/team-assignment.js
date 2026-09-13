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

        // Khi mở modal từ nút Remove, ngày kết thúc mặc định là ngày tối đa (hôm nay).
        // Khi validation fail, giữ nguyên old('end_date')
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
            // daterangepicker đổi attribute của input
            // Nếu start_date vượt quá maxDate, giới hạn minDate về maxDate
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

    const initializeAddAssignmentModal = function ($modal) {
        const $form = $modal.find('[data-add-assignment-form]');
        const $selectedMemberInputs = $modal.find('[data-selected-member-inputs]');
        const $errorMessage = $modal.find('[data-add-assignment-error]');
        const $confirmButton = $form.find('[data-add-assignment-confirm]');
        const initialEmployees = JSON.parse($('[data-assignment-employees]').text());
        let shouldRestoreInitialSelection = $modal.attr('data-auto-open') === 'true';
        let isSubmitting = false;
        const picker = TeamEmployeePicker.create($modal.find('[data-employee-picker]'), function (employees) {
            $selectedMemberInputs.empty();
            employees.forEach(function (employee) {
                $('<input>', { type: 'hidden', name: 'employee_ids[]', value: employee.employee_id })
                    .appendTo($selectedMemberInputs);
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
            $confirmButton.prop('disabled', true);
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
                $confirmButton.prop('disabled', false);
            });
        });

        if ($modal.attr('data-auto-open') === 'true') {
            $modal.modal('show');
        }
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

    $('.team-add-assignment-modal').each(function () {
        initializeAddAssignmentModal($(this));
    });

    $('.team-remove-assignment-modal').each(function () {
        initializeRemoveAssignmentModal($(this));
    });
});

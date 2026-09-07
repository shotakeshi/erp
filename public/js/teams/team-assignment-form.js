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

    const initializeAddAssignmentModal = function (modal) {
        modal.on('shown.bs.modal', function () {
            const select = modal.find('.team-employee-select');

            if (select.hasClass('select2-hidden-accessible')) {
                return;
            }

            select.select2({
                dropdownParent: modal,
                placeholder: select.data('placeholder'),
                width: '100%',
            });
        });

        if (modal.attr('data-auto-open') === 'true') {
            modal.modal('show');
        }
    };

    const initializeRemoveAssignmentModal = function (modal) {
        const form = modal.find('[data-remove-assignment-form]');
        const description = modal.find('[data-remove-assignment-description]');
        const employeeId = modal.find('[data-remove-employee-id]');
        const endDate = modal.find('[data-remove-assignment-end-date]');
        const endReasonNote = modal.find('[data-remove-assignment-end-reason-note]');
        const submitButton = form.find('[data-remove-assignment-submit]');

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
            const trigger = $(event.relatedTarget);

            if (trigger.length) {
                populateModal(trigger, false);
            }
        });

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

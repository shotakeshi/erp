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
        const $employeeList = $modal.find('[data-assignment-employee-list]');
        const $selectedMembers = $modal.find('[data-selected-members]');
        const $selectedMemberInputs = $modal.find('[data-selected-member-inputs]');
        const $search = $modal.find('[data-assignment-member-search]');
        const $errorMessage = $modal.find('[data-add-assignment-error]');
        const $confirmButton = $form.find('[data-add-assignment-confirm]');
        const employees = JSON.parse($('[data-assignment-employees]').text());
        const initialMemberIds = JSON.parse($('[data-assignment-initial-member-ids]').text());
        const labels = {
            add: $modal.data('add-label'),
            remove: $modal.data('remove-label'),
            noEmployees: $modal.data('no-employees-message'),
            requestFailed: $modal.data('request-failed-message'),
        };
        let selectedMembers = [];
        let shouldRestoreInitialSelection = $modal.attr('data-auto-open') === 'true';
        let isSubmitting = false;

        const getEmployeeId = function (employee) {
            return Number(employee.employee_id);
        };

        const initAvatarByName = function (name) {
            return name
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map(function (part) {
                    return part.charAt(0).toUpperCase();
                })
                .join('');
        };

        const appendAvatar = function ($avatarContainer, employee, avatarClass, fallbackClass) {
            if (employee.avatar_url) {
                $('<img>', {
                    src: employee.avatar_url,
                    alt: employee.name,
                    title: employee.name,
                    class: `rounded-circle ${avatarClass}`,
                }).appendTo($avatarContainer);

                return;
            }

            $('<span>', {
                class: `avatar-box ${avatarClass}`,
                title: employee.name,
            }).append($('<span>', {
                class: fallbackClass,
                text: initAvatarByName(employee.name),
            })).appendTo($avatarContainer);
        };

        const isSelected = function (employeeId) {
            return selectedMembers.some(function (employee) {
                return getEmployeeId(employee) === employeeId;
            });
        };

        const clearError = function () {
            $errorMessage.addClass('d-none').empty();
        };

        const displayError = function (message) {
            $errorMessage.text(message).removeClass('d-none');
        };

        const updateToggleButton = function ($button, employeeId) {
            const employeeIsSelected = isSelected(employeeId);

            $button
                .text(employeeIsSelected ? labels.remove : labels.add)
                .toggleClass('btn-light', ! employeeIsSelected)
                .toggleClass('btn-outline-danger', employeeIsSelected);
        };

        const renderSelectedMembers = function () {
            $selectedMembers.empty();
            $selectedMemberInputs.empty();

            selectedMembers.forEach(function (employee) {
                appendAvatar(
                    $selectedMembers,
                    employee,
                    'thumb-xs mr-1 mb-1',
                    'avatar-title bg-primary rounded-circle border border-white font-12 text-white',
                );

                $('<input>', {
                    type: 'hidden',
                    name: 'employee_ids[]',
                    value: getEmployeeId(employee),
                }).appendTo($selectedMemberInputs);
            });
        };

        const renderEmployeeList = function () {
            const searchValue = String($search.val() || '').trim().toLowerCase();

            $employeeList.empty();

            employees
                .filter(function (employee) {
                    const employeeSearchText = [employee.name, employee.detail]
                        .join(' ')
                        .toLowerCase();

                    return !searchValue || employeeSearchText.includes(searchValue);
                })
                .forEach(function (employee) {
                    const employeeId = getEmployeeId(employee);
                    const $item = $(
                        `<div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="media align-items-center">
                                <span data-assignment-member-avatar></span>
                                <div class="media-body">
                                    <h6 class="m-0" data-assignment-member-name></h6>
                                    <span class="text-muted font-12" data-assignment-member-detail></span>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="btn btn-light btn-sm px-3 mr-3"
                                data-assignment-member-toggle
                            ></button>
                        </div>`,
                    );
                    const $toggleButton = $item.find('[data-assignment-member-toggle]');

                    appendAvatar(
                        $item.find('[data-assignment-member-avatar]'),
                        employee,
                        'thumb-sm mr-3',
                        'avatar-title bg-soft-primary text-primary rounded-circle',
                    );
                    $item.find('[data-assignment-member-name]').text(employee.name);
                    $item.find('[data-assignment-member-detail]').text(employee.detail || '-');
                    $toggleButton.attr('data-employee-id', employeeId);
                    updateToggleButton($toggleButton, employeeId);

                    $item.appendTo($employeeList);
                });

            if (! $employeeList.children().length) {
                $('<p>', {
                    class: 'text-center text-muted mb-0 py-3',
                    text: labels.noEmployees,
                }).appendTo($employeeList);
            }
        };

        const restoreSelectedMembers = function (employeeIds) {
            const selectedEmployeeIds = new Set(employeeIds.map(Number));

            selectedMembers = employees.filter(function (employee) {
                return selectedEmployeeIds.has(getEmployeeId(employee));
            });
        };

        const errorFromResponse = function (response) {
            const errors = response.responseJSON?.errors || {};
            const firstError = Object.values(errors).flat()[0];

            return firstError
                || response.responseJSON?.message
                || labels.requestFailed;
        };

        $modal.on('show.bs.modal', function () {
            if (!shouldRestoreInitialSelection) {
                $modal.find('[data-add-assignment-server-errors]').empty();
            }

            restoreSelectedMembers(
                shouldRestoreInitialSelection
                    ? initialMemberIds
                    : [],
            );
            shouldRestoreInitialSelection = false;
            $search.val('');
            clearError();
            renderSelectedMembers();
            renderEmployeeList();
        });

        $modal.on('shown.bs.modal', function () {
            if ($.fn.slimscroll && ! $employeeList.parent().hasClass('slimScrollDiv')) {
                $employeeList.slimscroll({
                    position: 'right',
                    size: '6px',
                    color: '#a2b1d070',
                    wheelStep: 5,
                    touchScrollStep: 50,
                    alwaysVisible: false,
                });
            }
        });

        $modal.on('hidden.bs.modal', function () {
            selectedMembers = [];
            $search.val('');
            clearError();
        });

        $search.on('input', renderEmployeeList);

        $employeeList.on('click', '[data-assignment-member-toggle]', function () {
            const employeeId = Number($(this).data('employee-id'));
            const employee = employees.find(function (item) {
                return getEmployeeId(item) === employeeId;
            });

            if (!employee) {
                return;
            }

            if (isSelected(employeeId)) {
                selectedMembers = selectedMembers.filter(function (member) {
                    return getEmployeeId(member) !== employeeId;
                });
            } else {
                selectedMembers.push(employee);
            }

            clearError();
            renderSelectedMembers();
            updateToggleButton($(this), employeeId);
        });

        $form.on('submit', function (event) {
            event.preventDefault();

            if (isSubmitting) {
                return;
            }

            isSubmitting = true;
            $confirmButton.prop('disabled', true);
            $modal.find('[data-add-assignment-server-errors]').empty();
            clearError();

            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method'),
                data: $form.serialize(),
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).done(function (response) {
                $modal.one('hidden.bs.modal', function () {
                    window.location.assign(response.redirect_url || $form.attr('action'));
                });
                $modal.modal('hide');
            }).fail(function (response) {
                displayError(errorFromResponse(response));
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

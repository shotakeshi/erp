$(function () {

    const $form = $('#team-create-form');
    const $modal = $('#invite-members-modal');
    const $membersTable = $form.find('[data-team-members]');
    const $emptyRow = $membersTable.find('[data-team-members-empty]').first().clone();
    const $employeeList = $modal.find('[data-employee-list]');
    const $selectedMembers = $modal.find('[data-selected-members]');
    const $search = $modal.find('[data-member-search]');
    const $confirm = $modal.find('[data-member-confirm]');

    const employees = JSON.parse($('[data-team-employees]').text());
    let members = JSON.parse($('[data-team-initial-members]').text());

    let selectedEmployees = [];

    const labels = {
        add: $form.data('add-label'),
        remove: $form.data('remove-label'),
        delete: $form.data('delete-label'),
        noEmployees: $form.data('no-employees-message'),
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

    const renderSelectedEmployeesModal = function () {
        $selectedMembers.empty();

        selectedEmployees.forEach(function (employee) {
            appendAvatar(
                $selectedMembers,
                employee,
                'thumb-xs mr-1 mb-1',
                'avatar-title bg-primary rounded-circle border border-white font-12 text-white',
            );
        });
    };

    const getEmployeeId = function (employee) {
        return Number(employee.employee_id);
    };

    const isSelected = function (employeeId) {
        return selectedEmployees.some(function (employee) {
            return getEmployeeId(employee) === employeeId;
        });
    };

    const updateToggleButton = function ($button, employeeId) {
        const employeeIsSelected = isSelected(employeeId);

        $button
            .text(employeeIsSelected ? labels.remove : labels.add)
            .toggleClass('btn-light', ! employeeIsSelected)
            .toggleClass('btn-outline-danger', employeeIsSelected);
    };

    const renderEmployeeList = function () {
        const searchValue = String($search.val() || '').trim().toLowerCase();
        const memberIds = new Set(members.map(getEmployeeId));

        $employeeList.empty();

        employees
            .filter(function (employee) {
                const employeeSearchText = [employee.name, employee.detail].join(' ').toLowerCase();

                return !memberIds.has(getEmployeeId(employee))
                    && (!searchValue || employeeSearchText.includes(searchValue));
            })
            .forEach(function (employee) {
                const $item = $(
                    `<div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="media align-items-center">
                            <span data-member-avatar></span>
                            <div class="media-body">
                                <h6 class="m-0"></h6>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-light btn-sm px-3 mr-3"
                            data-member-toggle
                        ></button>
                    </div>`,
                );

                appendAvatar(
                    $item.find('[data-member-avatar]'),
                    employee,
                    'thumb-sm mr-3',
                    'avatar-title bg-soft-primary text-primary rounded-circle',
                );
                $item.find('h6').text(employee.name);
                const $toggleButton = $item.find('[data-member-toggle]')
                    .attr('data-employee-id', getEmployeeId(employee));

                updateToggleButton($toggleButton, getEmployeeId(employee));

                $item.appendTo($employeeList);
            });

        // empty employee
        if (! $employeeList.children().length) {
            $('<p>', {
                class: 'text-center text-muted mb-0 py-3',
                text: labels.noEmployees,
            }).appendTo($employeeList);
        }
    };

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
        selectedEmployees = [];
        $search.val('');
        renderSelectedEmployeesModal();
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
        selectedEmployees = [];
        $search.val('');
        renderSelectedEmployeesModal();
    });

    $search.on('input', renderEmployeeList);

    // Chỉ thay đổi selection tạm thời trong modal; item vẫn giữ nguyên trong danh sách.
    $employeeList.on('click', '[data-member-toggle]', function () {
        const employeeId = Number($(this).data('employee-id'));
        const employee = employees.find(function (item) {
            return getEmployeeId(item) === employeeId;
        });

        if (! employee) {
            return;
        }

        if (isSelected(employeeId)) {
            selectedEmployees = selectedEmployees.filter(function (item) {
                return getEmployeeId(item) !== employeeId;
            });
        } else {
            selectedEmployees.push(employee);
        }

        renderSelectedEmployeesModal();
        updateToggleButton($(this), employeeId);
    });

    // Xác nhận selection, chuyển thành members để tạo các input submit trong bảng.
    $confirm.on('click', function () {
        members.push(...selectedEmployees.map(function (employee) {
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

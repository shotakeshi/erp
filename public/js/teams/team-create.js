$(function () {

    const $form = $('#team-create-form');
    const $modal = $('#invite-members-modal');
    const $membersTable = $form.find('[data-team-members]');
    const $emptyRow = $membersTable.find('[data-team-members-empty]').first().clone();
    const $employeeList = $modal.find('[data-employee-list]');
    const $selectedMembers = $modal.find('[data-selected-members]');
    const $search = $modal.find('[data-member-search]');
    const $confirm = $modal.find('[data-member-confirm]');

    const employees = JSON.parse($('[data-team-employees]').text()) || '[]';
    let members = JSON.parse($('[data-team-initial-members]').text()) || '[]';

    let selectedEmployees = [];

    const labels = {
        add: $form.data('add-label'),
        delete: $form.data('delete-label'),
        noEmployees: $form.data('no-employees-message'),
        manager: $form.data('manager-label'),
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

    const renderEmployeeList = function () {
        // Không hiển thị lại employee đã nằm trong team hoặc đã được chọn.
        const searchValue = String($search.val() || '').trim().toLowerCase();
        const excludedIds = new Set([...members, ...selectedEmployees].map(getEmployeeId));

        $employeeList.empty();

        employees
            .filter(function (employee) {
                const employeeSearchText = [employee.name, employee.detail].join(' ').toLowerCase();

                // Chỉ giữ employee chưa được chọn và khớp từ khoá search (nếu có).
                return !excludedIds.has(getEmployeeId(employee))
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
                            data-member-add
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
                $item.find('[data-member-add]')
                    .text(labels.add)
                    .attr('data-employee-id', getEmployeeId(employee));

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
                isManager: `members[${index}][is_manager]`,
            };
            const managerCheckboxId = `member-manager-${memberId}`;

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
                            required
                            data-member-role
                        >
                    </td>
                    <td class="text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" data-manager-value>
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                value="1"
                                data-manager-checkbox
                            >
                            <label class="custom-control-label" data-manager-label></label>
                        </div>
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

            $row.find('[data-manager-value]')
                .attr('name', fieldNames.isManager)
                .val(member.is_manager ? '1' : '0');

            $row.find('[data-manager-checkbox]')
                .attr({
                    id: managerCheckboxId,
                    'data-employee-id': memberId,
                })
                .prop('checked', Boolean(member.is_manager));

            $row.find('[data-manager-label]')
                .attr('for', managerCheckboxId)

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

    // Thêm employee vào selection trong modal, loại item đó khỏi danh sách có thể chọn.
    $employeeList.on('click', '[data-member-add]', function () {
        const employee = employees.find(function (item) {
            return getEmployeeId(item) === Number($(this).data('employee-id'));
        }.bind(this));

        if (! employee) {
            return;
        }

        selectedEmployees.push(employee);
        renderSelectedEmployeesModal();
        renderEmployeeList();
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
                is_manager: false,
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

    // Cập nhật state và hidden input
    $membersTable.on('change', '[data-manager-checkbox]', function () {
        const id = Number($(this).data('employee-id'));
        const member = members.find(function (item) {
            return getEmployeeId(item) === id;
        });

        if (member) {
            member.is_manager = $(this).prop('checked');
            $(this).siblings('[data-manager-value]').val(member.is_manager ? '1' : '0');
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

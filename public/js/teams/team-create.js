$(function () {

    const $form = $('#team-create-form');
    const $modal = $('#invite-members-modal');
    const $membersTable = $form.find('[data-team-members]');
    const $emptyRow = $membersTable.find('[data-team-members-empty]').first().clone();
    const $confirm = $modal.find('[data-member-confirm]');
    let members = JSON.parse($('[data-team-initial-members]').text());
    const picker = TeamEmployeePicker.create($modal.find('[data-employee-picker]'));
    const appendAvatar = TeamEmployeePicker.appendAvatar;
    const getEmployeeId = (employee) => Number(employee.employee_id);
    const labels = { delete: $form.data('delete-label') };

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
        picker.open([], members.map(getEmployeeId));
    });
    $modal.on('hidden.bs.modal', picker.close);

    // Xác nhận selection, chuyển thành members để tạo các input submit trong bảng.
    $confirm.on('click', function () {
        members.push(...picker.selected().map(function (employee) {
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

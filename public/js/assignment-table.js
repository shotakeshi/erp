/**
 * Assignment table on the create form (Team, Project): select employees from the picker modal,
 * enter a role, and submit as members[index][employee_id|role].
 * Uses EmployeePicker (employee-picker.js) when initialized; call init after DOM ready.
 *
 * Markup: [data-assignment-rows] holds the empty row [data-assignment-empty];
 * the modal contains [data-employee-picker] and the [data-assignment-confirm] button.
 */
(function () {
    const init = function (options) {
        const $root = $(options.root);
        const $modal = $(options.modal);
        const $rows = $root.find('[data-assignment-rows]');
        const $emptyRow = $rows.find('[data-assignment-empty]').first().clone();
        const $confirm = $modal.find('[data-assignment-confirm]');
        const picker = EmployeePicker.create($modal.find('[data-employee-picker]'));
        const initialAssignments = options.initialAssignments || [];
        const copyAssignments = () => initialAssignments.map((assignment) => Object.assign({}, assignment));
        const getEmployeeId = (employee) => Number(employee.employee_id);
        const deleteLabel = $root.data('delete-label');
        let assignments = copyAssignments();

        // Render assignments and the inputs that are submitted with the form.
        const renderAssignments = function () {
            $rows.empty();

            if (!assignments.length) {
                $rows.append($emptyRow.clone());
                return;
            }

            assignments.forEach(function (assignment, index) {
                const employeeId = getEmployeeId(assignment);
                const $row = $(
                    `<tr>
                        <td>
                            <div class="media align-items-center">
                                <span data-assignment-avatar></span>
                                <div class="media-body">
                                    <input type="hidden" data-assignment-id>
                                    <span data-assignment-name></span>
                                </div>
                            </div>
                        </td>
                        <td data-assignment-detail></td>
                        <td>
                            <input type="text" class="form-control" data-assignment-role>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-circle btn-outline-danger" data-assignment-delete>
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`,
                );

                $row.attr('data-employee-id', employeeId);
                $row.find('[data-assignment-id]').attr('name', `members[${index}][employee_id]`).val(employeeId);
                EmployeePicker.appendAvatar(
                    $row.find('[data-assignment-avatar]'),
                    assignment,
                    'thumb-xs mr-2',
                    'avatar-title bg-soft-primary text-primary rounded-circle',
                );
                $row.find('[data-assignment-name]').text(assignment.name);
                $row.find('[data-assignment-detail]').text(assignment.detail || '-');
                $row.find('[data-assignment-role]')
                    .attr({
                        name: `members[${index}][role]`,
                        list: options.roleListId,
                        'data-employee-id': employeeId,
                    })
                    .prop('required', Boolean(options.roleRequired))
                    .val(assignment.role || '');
                $row.find('[data-assignment-delete]').attr({
                    title: deleteLabel,
                    'aria-label': deleteLabel,
                    'data-employee-id': employeeId,
                });
                $row.appendTo($rows);
            });
        };

        // Reset returns the state to the initial data (including old input after a validation error).
        $root.closest('form').on('reset', function () {
            window.setTimeout(function () {
                assignments = copyAssignments();
                renderAssignments();
            });
        });

        $modal.on('show.bs.modal', function () {
            picker.open([], assignments.map(getEmployeeId));
        });
        $modal.on('hidden.bs.modal', picker.close);

        // Confirm the selection and convert it to assignments that create the submit inputs in the table.
        $confirm.on('click', function () {
            assignments.push(...picker.selected().map(function (employee) {
                return {
                    employee_id: getEmployeeId(employee),
                    name: employee.name,
                    detail: employee.detail,
                    avatar_url: employee.avatar_url,
                    role: '',
                };
            }));

            renderAssignments();
            $modal.modal('hide');
        });

        // Keep the assignments state in sync when the user changes a role.
        $rows.on('input', '[data-assignment-role]', function () {
            const id = Number($(this).data('employee-id'));
            const assignment = assignments.find((item) => getEmployeeId(item) === id);

            if (assignment) {
                assignment.role = $(this).val();
            }
        });

        // Remove the assignment from the state and re-render the table to re-index the field names.
        $rows.on('click', '[data-assignment-delete]', function () {
            const id = Number($(this).data('employee-id'));

            assignments = assignments.filter((assignment) => getEmployeeId(assignment) !== id);
            renderAssignments();
        });

        renderAssignments();
    };

    window.AssignmentTable = { init };
})();

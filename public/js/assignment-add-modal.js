/**
 * Add-members modal on the show pages (Team, Project): pick employees and submit them as employee_ids[]
 * with a normal form post. Markup: resources/views/components/employee-picker-modal.blade.php.
 * Uses EmployeePicker (employee-picker.js); every [data-assignment-add-modal] is initialized on DOM ready.
 */
(function () {
    const init = function ($modal) {
        const $form = $modal.find('[data-assignment-modal-form]');
        const $inputs = $modal.find('[data-selected-assignment-inputs]');
        const $errors = $modal.find('[data-assignment-modal-errors]');
        const initialEmployees = JSON.parse($(`[data-initial-employees="${$modal.attr('id')}"]`).text());
        let shouldRestoreInitialSelection = $modal.attr('data-auto-open') === 'true';

        const picker = EmployeePicker.create($modal.find('[data-employee-picker]'), function (employees) {
            $inputs.empty();
            employees.forEach(function (employee) {
                $('<input>', { type: 'hidden', name: 'employee_ids[]', value: employee.employee_id }).appendTo($inputs);
            });
        });

        $modal.on('show.bs.modal', function () {
            if (!shouldRestoreInitialSelection) {
                $errors.empty();
            }

            picker.open(shouldRestoreInitialSelection ? initialEmployees : []);
            shouldRestoreInitialSelection = false;
        });
        $modal.on('hidden.bs.modal', picker.close);

        // Prevent a double submit from adding the same employees twice.
        $form.on('submit', function () {
            $form.find('[type="submit"]').prop('disabled', true);
        });

        if ($modal.attr('data-auto-open') === 'true') {
            $modal.modal('show');
        }
    };

    $(function () {
        $('[data-assignment-add-modal]').each(function () {
            init($(this));
        });
    });
})();

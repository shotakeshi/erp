$(function () {
    $('.team-add-assignment-modal').each(function () {
        const modal = $(this);

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
    });

    $('.team-remove-assignment-modal').each(function () {
        const modal = $(this);
        const form = modal.find('[data-remove-assignment-form]');
        const description = modal.find('[data-remove-assignment-description]');
        const employeeId = modal.find('[data-remove-employee-id]');
        const endDate = modal.find('[data-remove-assignment-end-date]');

        const populateModal = function (source, preserveEndDate) {
            form.attr('action', source.data('assignment-action'));
            description.text(source.data('assignment-description'));
            employeeId.val(source.data('employee-id'));
            endDate.attr('min', source.data('start-date'));

            if (! preserveEndDate) {
                endDate.val(endDate.attr('max'));
            }
        };

        modal.on('show.bs.modal', function (event) {
            const trigger = $(event.relatedTarget);

            if (trigger.length) {
                populateModal(trigger, false);
            }
        });

        form.on('submit', function () {
            form.find('[data-remove-assignment-submit]')
                .prop('disabled', true);
        });

        if (modal.attr('data-auto-open') === 'true') {
            populateModal(modal, true);
            modal.modal('show');
        }
    });
});

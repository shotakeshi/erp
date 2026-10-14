/**
 * Inline editing for elements rendered by <x-form.editable>.
 *
 * Each `[data-x-editable]` element carries its own configuration:
 *   data-url, data-pk, data-name, data-method (default PUT), data-maxlength,
 *   data-csrf, data-error-label.
 * The server must answer with JSON; `{ message, value }` on success, where `value` (optional)
 * is the stored value to display. Validation (422) and business (409) errors are shown inline.
 *
 * Requires jQuery, toastr and plugins/x-editable (css + js).
 */
$(function () {
    const $elements = $('[data-x-editable]');

    if (!$elements.length) {
        return;
    }

    // The theme uses Bootstrap 4, so replace the plugin's glyphicon buttons with Font Awesome ones.
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editableform.buttons =
        '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="fas fa-check" aria-hidden="true"></i></button>' +
        '<button type="button" class="btn btn-light btn-sm editable-cancel"><i class="fas fa-times" aria-hidden="true"></i></button>';

    $elements.each(function () {
        const $element = $(this);
        const name = $element.data('name');
        const maxlength = $element.data('maxlength');

        $element.editable({
            tpl: maxlength ? $('<input>', { type: 'text', maxlength }).prop('outerHTML') : undefined,
            ajaxOptions: { dataType: 'json' },
            params: function (params) {
                const data = { _method: $element.data('method') || 'PUT', _token: $element.data('csrf') };
                data[name] = params.value;

                return data;
            },
            success: function (response) {
                toastr.success(response.message);

                if (response.value !== undefined) {
                    return { newValue: response.value };
                }
            },
            error: function (response) {
                const json = response.responseJSON || {};
                const fieldErrors = json.errors && json.errors[name];

                return (fieldErrors && fieldErrors[0]) || json.message || $element.data('error-label');
            },
        });
    });
});

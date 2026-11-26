$(document).on('click', '[data-confirm]', function (e) {
    e.preventDefault();

    const button = $(this);
    const form = button.closest('form');

    Swal.fire({
        title: button.data('confirm-title'),
        text: button.data('confirm-text'),
        icon: 'warning',
        customClass: {
            popup: 'delete-popup'
        },
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: button.data('confirm-button'),
        cancelButtonText: button.data('cancel-button'),
    }).then((result) => {
        if (result.value) {
            form.submit();
        }
    });
});
function confirmDelete(formId) {
    Swal.fire({
        title: window.trans.delete_confirm,
        text: window.trans.delete_warning,
        icon: 'warning',
        customClass: {
            popup: 'delete-popup'
        },
        showCancelButton: true,
        reverseButtons: false,
        confirmButtonColor: '#506ee4',
        cancelButtonColor: '#ef4d56',
        confirmButtonText: window.trans.delete,
        cancelButtonText: window.trans.cancel,
    }).then((result) => {
        if (result.value) {
            document.getElementById(formId).submit();
        }
    });
}
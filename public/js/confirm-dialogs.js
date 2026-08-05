/**
 * PANTAU — dialog konfirmasi & alert konsisten (SweetAlert2).
 * Warna mengikuti template Skote/Bootstrap.
 */
const PANTAU_SWAL = {
    primary: '#556ee6',
    success: '#34c38f',
    danger: '#f46a6a',
    muted: '#74788d',
};

function showAlert(title, text, icon = 'info') {
    return Swal.fire({
        title,
        text,
        icon,
        confirmButtonColor: PANTAU_SWAL.primary,
        confirmButtonText: 'OK',
    });
}

function showValidationErrors(errors) {
    const message = Array.isArray(errors) ? errors.join('\n') : errors;
    return showAlert('Perhatian', message, 'warning');
}

function confirmAction(options) {
    const {
        title,
        text,
        html,
        icon = 'question',
        confirmText = 'Ya',
        cancelText = 'Batal',
        confirmColor = PANTAU_SWAL.primary,
        cancelColor = PANTAU_SWAL.muted,
        reverseButtons = true,
    } = options;

    return Swal.fire({
        title,
        ...(html ? { html } : { text }),
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: confirmColor,
        cancelButtonColor: cancelColor,
        reverseButtons,
    });
}

function confirmImpersonate(event, username) {
    event.preventDefault();

    const targetUrl = event.currentTarget.href;

    confirmAction({
        title: 'Masuk sebagai pengguna lain?',
        html: `Anda akan masuk sebagai <strong>${username}</strong>.<br><small class="text-muted">Anda dapat melihat sistem dari sudut pandang pengguna ini.</small>`,
        icon: 'question',
        confirmText: 'Ya, masuk',
        confirmColor: PANTAU_SWAL.success,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = targetUrl;
        }
    });

    return false;
}

function confirmDelete(entityId, entityName, entityLabel = 'pengguna') {
    confirmAction({
        title: `Hapus ${entityLabel}?`,
        html: `<strong>${entityName}</strong> akan dihapus permanen.`,
        icon: 'warning',
        confirmText: 'Hapus',
        confirmColor: PANTAU_SWAL.danger,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${entityId}`).submit();
        }
    });
}

function confirmApproveRegistration(form, travelName) {
    confirmAction({
        title: `Setujui pendaftaran ${travelName}?`,
        text: 'PIC travel akan bisa login.',
        icon: 'question',
        confirmText: 'Ya, setujui',
        confirmColor: PANTAU_SWAL.success,
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

function confirmPublicFormSubmit(form, options = {}) {
    return confirmAction({
        title: options.title || 'Konfirmasi',
        text: options.text || 'Apakah Anda yakin ingin melanjutkan?',
        icon: options.icon || 'question',
        confirmText: options.confirmText || 'Ya, lanjutkan',
        confirmColor: options.confirmColor || PANTAU_SWAL.primary,
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.submit();
        }
    });
}

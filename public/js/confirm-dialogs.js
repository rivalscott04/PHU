function confirmImpersonate(event, username) {
    event.preventDefault();

    const targetUrl = event.currentTarget.href;

    Swal.fire({
        title: 'Masuk sebagai pengguna lain?',
        html: `Anda akan masuk sebagai <strong>${username}</strong>.<br><small class="text-muted">Anda dapat melihat sistem dari sudut pandang pengguna ini.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#34c38f',
        cancelButtonColor: '#74788d',
        confirmButtonText: 'Ya, masuk',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = targetUrl;
        }
    });

    return false;
}

function confirmDelete(userId, username) {
    Swal.fire({
        title: 'Hapus pengguna?',
        html: `<strong>${username}</strong> akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f46a6a',
        cancelButtonColor: '#74788d',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${userId}`).submit();
        }
    });
}

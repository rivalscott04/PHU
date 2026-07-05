(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-form-id]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const formId = form.dataset.formId;
                const errors = validatePublicPengaduanForm(form, formId);

                if (errors.length > 0) {
                    event.preventDefault();
                    alert(errors.join('\n'));
                    return;
                }

                if (!window.confirm('Apakah Anda yakin ingin mengirim pengaduan ini?')) {
                    event.preventDefault();
                }
            });
        });

        const modalEl = document.getElementById('pengaduanTravelModal');
        if (modalEl && modalEl.dataset.autoOpen === '1') {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }

        const successFlash = document.querySelector('.profile-flash');
        if (successFlash) {
            successFlash.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const section = document.getElementById('pengaduan-travel');
        if (section && section.querySelector('.alert-danger')) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    function validatePublicPengaduanForm(form, formId) {
        const errors = [];
        const nameInput = form.querySelector('#nama_pengadu_' + formId);
        const halInput = form.querySelector('#hal_aduan_' + formId);
        const travelSelect = form.querySelector('#travels_id_' + formId);
        const fileInput = form.querySelector('#berkas_aduan_' + formId);

        if (nameInput && !nameInput.value.trim()) {
            errors.push('Nama pengadu wajib diisi.');
        }

        if (travelSelect && !travelSelect.value) {
            errors.push('Travel wajib dipilih.');
        }

        if (halInput && !halInput.value.trim()) {
            errors.push('Hal yang diadukan wajib diisi.');
        }

        if (fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            const maxSize = 2 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                errors.push('Lampiran harus berformat PDF, JPG, atau PNG.');
            }

            if (file.size > maxSize) {
                errors.push('Ukuran lampiran maksimal 2MB.');
            }
        }

        return errors;
    }
})();

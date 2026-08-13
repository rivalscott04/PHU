@php
    // Default: ringkasan pendaftaran pusat. Formulir cabang mengoper grupnya sendiri.
    // Satu grup = satu langkah isian; langkah Review selalu yang terakhir.
    $reviewGroups = $reviewGroups ?? [
        ['title' => 'Profil Travel', 'fields' => ['Penyelenggara', 'Status', 'Pimpinan']],
        ['title' => 'Izin Operasional', 'fields' => ['Pusat', 'Tanggal', 'license_expiry']],
        ['title' => 'Data Akreditasi', 'fields' => ['nilai_akreditasi', 'tanggal_akreditasi', 'lembaga_akreditasi']],
        ['title' => 'Kontak & Alamat', 'fields' => ['Telepon', 'kab_kota', 'alamat_kantor_lama', 'alamat_kantor_baru']],
        ['title' => 'Upload Dokumen', 'fields' => ['dokumen_sk', 'dokumen_akreditasi']],
        ['title' => 'Akun PIC', 'fields' => ['pic_nama', 'pic_email', 'pic_nomor_hp', 'password']],
    ];
@endphp

<script src="{{ asset('libs/jquery-steps/build/jquery.steps.min.js') }}"></script>
<script>
    (function () {
        const form = document.getElementById('travel-registration-form');
        if (!form) {
            return;
        }

        const MAX_FILE_SIZE = 1572864; // 1.5 MB

        const reviewGroups = @json($reviewGroups);

        // Review adalah langkah setelah semua langkah isian.
        const REVIEW_STEP_INDEX = reviewGroups.length;
        const ACCOUNT_STEP_INDEX = REVIEW_STEP_INDEX - 1;

        function getStepBody(stepIndex) {
            const wizard = document.getElementById('travel-registration-wizard');
            if (!wizard) {
                return null;
            }

            const bodies = wizard.querySelectorAll(':scope > .content > .body');
            if (bodies.length > 0) {
                return bodies[stepIndex] || null;
            }

            const sections = wizard.querySelectorAll(':scope > section');
            return sections[stepIndex] || null;
        }

        function getFieldLabel(fieldId) {
            const label = form.querySelector('label[for="' + fieldId + '"]');
            if (!label) {
                return fieldId;
            }

            // [\s\S] bukan titik, karena label diakhiri newline sehingga .*$ tidak
            // pernah cocok dan tanda bintang wajib ikut terbawa ke ringkasan.
            return label.textContent.replace(/\s*\*[\s\S]*$/, '').replace(/\([^)]*\)/g, '').trim();
        }

        function formatDateValue(value) {
            if (!value) {
                return '-';
            }

            const parts = value.split('-');
            if (parts.length !== 3) {
                return value;
            }

            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        function getSelectText(field) {
            if (!field || field.tagName !== 'SELECT') {
                return '-';
            }

            const selected = field.options[field.selectedIndex];
            if (!selected || !selected.value) {
                return '-';
            }

            return selected.textContent.trim();
        }

        function getFileDisplayValue(field) {
            if (!field) {
                return '-';
            }

            if (field.files && field.files.length > 0) {
                return field.files[0].name;
            }

            return 'Belum diupload';
        }

        function getFieldDisplayValue(fieldId) {
            const field = form.querySelector('#' + fieldId);
            if (!field || field.disabled) {
                return '-';
            }

            if (fieldId === 'password') {
                const value = field.value;
                return value ? '••••••••' : '-';
            }

            if (field.type === 'file') {
                return getFileDisplayValue(field);
            }

            if (field.tagName === 'SELECT') {
                return getSelectText(field);
            }

            if (field.type === 'date') {
                return formatDateValue(field.value.trim());
            }

            const value = field.value.trim();
            return value || '-';
        }

        function renderReview() {
            const container = document.getElementById('travel-registration-review');
            if (!container) {
                return;
            }

            container.innerHTML = reviewGroups.map(function (group) {
                const rows = group.fields.map(function (fieldId) {
                    const field = form.querySelector('#' + fieldId);
                    if (!field || field.disabled) {
                        return '';
                    }

                    return `
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">${getFieldLabel(fieldId)}</small>
                            <span class="fw-semibold">${getFieldDisplayValue(fieldId)}</span>
                        </div>
                    `;
                }).filter(Boolean).join('');

                if (!rows) {
                    return '';
                }

                return `
                    <div class="card border mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">${group.title}</h6>
                            <div class="row">${rows}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function fieldContainer(field) {
            return field.closest('.mb-3, .mb-4') || field.parentElement;
        }

        function resetFieldValidation(field) {
            field.classList.remove('is-invalid', 'is-valid');
            const container = fieldContainer(field);
            if (container) {
                container.classList.remove('validation-error');
                const errorDiv = container.querySelector('.validation-error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }

        function markFieldInvalid(field, message) {
            const fieldContainerEl = fieldContainer(field);
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            if (fieldContainerEl) {
                fieldContainerEl.classList.add('validation-error');
                let errorDiv = fieldContainerEl.querySelector('.validation-error-message, .invalid-feedback.custom-wizard-error');
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.classList.add('d-block');
                } else {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback custom-wizard-error d-block validation-error-message';
                    errorDiv.textContent = message;
                    fieldContainerEl.appendChild(errorDiv);
                }
            }
        }

        function markFieldValid(field) {
            const fieldContainerEl = fieldContainer(field);
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (fieldContainerEl) {
                fieldContainerEl.classList.remove('validation-error');
                const errorDiv = fieldContainerEl.querySelector('.validation-error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }

        function validateField(field) {
            if (!field || field.disabled) {
                return true;
            }

            if (field.type === 'file') {
                if (!field.files || field.files.length === 0) {
                    if (field.required) {
                        markFieldInvalid(field, 'File ini wajib diupload');
                        return false;
                    }
                    resetFieldValidation(field);
                    return true;
                }

                if (field.files[0].size > MAX_FILE_SIZE) {
                    markFieldInvalid(field, 'Ukuran file maksimal 1,5 MB');
                    return false;
                }

                markFieldValid(field);
                return true;
            }

            const value = field.value.trim();

            if (field.required && !value) {
                markFieldInvalid(field, getFieldLabel(field.id || field.name) + ' wajib diisi');
                return false;
            }

            if (field.id === 'pic_nomor_hp' && value) {
                if (!/^08\d{6,14}$/.test(value)) {
                    markFieldInvalid(field, 'Nomor HP harus diawali 08, panjang 8 s.d. 16 digit angka');
                    return false;
                }
            }

            if (field.id === 'pic_email' && value) {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    markFieldInvalid(field, 'Format email tidak valid');
                    return false;
                }
            }

            if (field.id === 'password' && value && value.length < 8) {
                markFieldInvalid(field, 'Password minimal 8 karakter');
                return false;
            }

            if (field.id === 'password_confirmation') {
                const password = form.querySelector('#password');
                if (value !== (password?.value || '')) {
                    markFieldInvalid(field, 'Password tidak sama');
                    return false;
                }
            }

            if (value) {
                markFieldValid(field);
            } else {
                resetFieldValidation(field);
            }

            return true;
        }

        function validateStep(stepIndex) {
            if (stepIndex === REVIEW_STEP_INDEX) {
                return true;
            }

            const section = getStepBody(stepIndex);
            if (!section) {
                return false;
            }

            const fields = section.querySelectorAll('[required]:not([disabled])');
            const invalidFields = [];

            fields.forEach(function (field) {
                if (!validateField(field)) {
                    invalidFields.push(field);
                }
            });

            if (stepIndex === ACCOUNT_STEP_INDEX) {
                const password = form.querySelector('#password');
                const confirm = form.querySelector('#password_confirmation');
                if (password && confirm && password.value !== confirm.value) {
                    markFieldInvalid(confirm, 'Password tidak sama');
                    if (!invalidFields.includes(confirm)) {
                        invalidFields.push(confirm);
                    }
                }
            }

            if (invalidFields.length > 0) {
                invalidFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                invalidFields[0].focus();
                return false;
            }

            return true;
        }

        function validateAllInputSteps() {
            for (let i = 0; i < REVIEW_STEP_INDEX; i++) {
                if (!validateStep(i)) {
                    return i;
                }
            }

            return -1;
        }

        function bindPhoneSanitizer() {
            const phoneField = form.querySelector('#pic_nomor_hp');
            if (!phoneField) {
                return;
            }

            phoneField.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 14);
                if (this.classList.contains('is-valid') || this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        }

        function updateWizardProgress(currentIndex) {
            document.querySelectorAll('#travel-wizard-progress .travel-wizard-step').forEach(function (item, index) {
                item.classList.remove('is-active', 'is-done');
                if (index < currentIndex) {
                    item.classList.add('is-done');
                }
                if (index === currentIndex) {
                    item.classList.add('is-active');
                }
            });

            const caption = document.getElementById('travel-wizard-current-label');
            const labels = window.travelWizardStepLabels || [];
            if (caption && labels[currentIndex]) {
                caption.textContent = 'Langkah ' + (currentIndex + 1) + ' dari ' + labels.length + ': ' + labels[currentIndex];
            }
        }

        function initWizard() {
            const $wizard = $('#travel-registration-wizard');
            if (!$wizard.length || typeof $wizard.steps !== 'function') {
                return;
            }

            $wizard.steps({
                headerTag: 'h3',
                bodyTag: 'section',
                transitionEffect: 'slide',
                enableFinishButton: true,
                enablePagination: true,
                enableCancelButton: false,
                labels: {
                    cancel: 'Batal',
                    current: 'langkah saat ini:',
                    pagination: 'Pagination',
                    finish: 'Kirim Pendaftaran',
                    next: 'Lanjut',
                    previous: 'Kembali',
                    loading: 'Memuat ...'
                },
                onStepChanging: function (event, currentIndex, newIndex) {
                    if (newIndex < currentIndex) {
                        return true;
                    }

                    if (!validateStep(currentIndex)) {
                        form.classList.add('was-validated');
                        return false;
                    }

                    if (newIndex === REVIEW_STEP_INDEX) {
                        const firstInvalid = validateAllInputSteps();
                        if (firstInvalid !== -1) {
                            setTimeout(function () {
                                $wizard.steps('setStep', firstInvalid);
                            }, 0);
                            return false;
                        }
                        renderReview();
                    }

                    return true;
                },
                onFinishing: function () {
                    const firstInvalid = validateAllInputSteps();
                    if (firstInvalid !== -1) {
                        $wizard.steps('setStep', firstInvalid);
                        return false;
                    }

                    renderReview();
                    return true;
                },
                onStepChanged: function (event, currentIndex) {
                    updateWizardProgress(currentIndex);
                },
                onFinished: function () {
                    form.submit();
                }
            });

            updateWizardProgress(0);
        }

        document.addEventListener('DOMContentLoaded', function () {
            bindPhoneSanitizer();
            initWizard();
        });
    })();
</script>

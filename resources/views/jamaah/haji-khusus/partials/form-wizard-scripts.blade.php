@php
    $finishLabel = $finishLabel ?? 'Simpan Data';
    $locationDefaults = $locationDefaults ?? [];
@endphp

@push('js')
    <script src="{{ asset('libs/jquery-steps/build/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('js/wilayah-cascade.js') }}"></script>
    <script>
        (function () {
            const form = document.getElementById('jamaah-haji-khusus-form');
            if (!form) {
                return;
            }

            const locationDefaults = @json($locationDefaults);
            const finishLabel = @json($finishLabel);
            const REVIEW_STEP_INDEX = 6;

            const reviewGroups = [
                {
                    title: 'Data Pribadi',
                    fields: ['nama_lengkap', 'no_ktp', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin'],
                },
                {
                    title: 'Alamat',
                    fields: ['alamat', 'provinsi', 'kota', 'kecamatan', 'kode_pos'],
                },
                {
                    title: 'Kontak & Keluarga',
                    fields: ['no_hp', 'email', 'nama_ayah'],
                },
                {
                    title: 'Data Tambahan',
                    fields: ['pekerjaan', 'pendidikan_terakhir', 'status_pernikahan', 'pergi_haji', 'golongan_darah', 'alergi'],
                },
                {
                    title: 'Paspor & Haji Khusus',
                    fields: ['no_paspor', 'tanggal_berlaku_paspor', 'tempat_terbit_paspor', 'nomor_porsi', 'tahun_pendaftaran', 'catatan_khusus'],
                },
                {
                    title: 'Upload Dokumen',
                    fields: ['dokumen_ktp', 'dokumen_kk', 'dokumen_paspor', 'dokumen_foto', 'surat_keterangan', 'bukti_setor_bank'],
                },
            ];

            function getStepBody(stepIndex) {
                const wizard = document.getElementById('jamaah-haji-khusus-wizard');
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

                return label.textContent.replace(/\s*\*.*$/, '').replace(/\([^)]*\)/g, '').trim();
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

                const container = field.closest('.mb-3');
                if (container && container.querySelector('a[href], button[onclick*="openPdfPreview"]')) {
                    return 'Sudah ada (tidak diubah)';
                }

                return 'Belum diupload';
            }

            function getFieldDisplayValue(fieldId) {
                const field = form.querySelector('#' + fieldId);
                if (!field || field.disabled) {
                    return '-';
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
                const container = document.getElementById('jamaah-haji-khusus-review');
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

            function resetFieldValidation(field) {
                field.classList.remove('is-invalid', 'is-valid');
                const fieldContainer = field.closest('.mb-3');
                if (fieldContainer) {
                    fieldContainer.classList.remove('validation-error');
                    const errorDiv = fieldContainer.querySelector('.validation-error-message');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            }

            function markFieldInvalid(field, message) {
                const fieldContainer = field.closest('.mb-3') || field.parentElement;
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                if (fieldContainer) {
                    fieldContainer.classList.add('validation-error');
                    let errorDiv = fieldContainer.querySelector('.validation-error-message, .invalid-feedback.custom-wizard-error');
                    if (errorDiv) {
                        errorDiv.textContent = message;
                        errorDiv.classList.add('d-block');
                    } else {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback custom-wizard-error d-block validation-error-message';
                        errorDiv.textContent = message;
                        fieldContainer.appendChild(errorDiv);
                    }
                }
            }

            function markFieldValid(field) {
                const fieldContainer = field.closest('.mb-3');
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                if (fieldContainer) {
                    fieldContainer.classList.remove('validation-error');
                    const errorDiv = fieldContainer.querySelector('.validation-error-message');
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
                    if (field.files && field.files.length > 0) {
                        markFieldValid(field);
                        return true;
                    }
                    if (field.required) {
                        markFieldInvalid(field, 'File ini wajib diupload');
                        return false;
                    }
                    resetFieldValidation(field);
                    return true;
                }

                const value = field.value.trim();
                if (!value) {
                    markFieldInvalid(field, getFieldLabel(field.id || field.name) + ' wajib diisi');
                    return false;
                }

                if (field.id === 'no_hp' && !value.startsWith('08')) {
                    markFieldInvalid(field, 'Nomor HP harus diawali dengan 08');
                    return false;
                }

                if (field.id === 'no_ktp') {
                    if (!/^\d+$/.test(value)) {
                        markFieldInvalid(field, 'NIK hanya boleh berisi angka');
                        return false;
                    }
                    if (value.length !== 16) {
                        markFieldInvalid(field, 'NIK harus 16 digit');
                        return false;
                    }
                }

                markFieldValid(field);
                return true;
            }

            function focusFirstInvalidField(invalidFields, section) {
                if (invalidFields.length === 0) {
                    return;
                }

                invalidFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                invalidFields[0].focus();
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

                const phoneField = section.querySelector('#no_hp');
                if (phoneField && !phoneField.disabled && phoneField.value.trim() && !phoneField.value.trim().startsWith('08')) {
                    markFieldInvalid(phoneField, 'Nomor HP harus diawali dengan 08');
                    if (!invalidFields.includes(phoneField)) {
                        invalidFields.push(phoneField);
                    }
                }

                const ktpField = section.querySelector('#no_ktp');
                if (ktpField && !ktpField.disabled && ktpField.value.trim()) {
                    if (!validateField(ktpField)) {
                        if (!invalidFields.includes(ktpField)) {
                            invalidFields.push(ktpField);
                        }
                    }
                }

                if (invalidFields.length > 0) {
                    focusFirstInvalidField(invalidFields, section);
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

            function sanitizeDigitsField(field) {
                const maxLength = parseInt(field.dataset.digitsOnly || field.getAttribute('maxlength') || '32', 10);
                const cleaned = field.value.replace(/\D/g, '').slice(0, maxLength);
                const changed = field.value !== cleaned;
                if (changed) {
                    field.value = cleaned;
                }
                return changed;
            }

            function showDigitsOnlyHint(field) {
                const container = field.closest('.mb-3') || field.parentElement;
                if (!container) {
                    return;
                }

                const label = getFieldLabel(field.id || field.name);
                const message = field.dataset.digitsHint || (label + ' hanya boleh berisi angka');

                let hint = container.querySelector('.digits-only-hint');
                if (!hint) {
                    hint = document.createElement('div');
                    hint.className = 'text-danger small mt-1 digits-only-hint d-none';
                    hint.setAttribute('role', 'status');
                    hint.setAttribute('aria-live', 'polite');
                    container.appendChild(hint);
                }

                hint.textContent = message;
                hint.classList.remove('d-none');

                if (field._digitsHintTimer) {
                    clearTimeout(field._digitsHintTimer);
                }

                field._digitsHintTimer = setTimeout(function () {
                    hint.classList.add('d-none');
                }, 2800);
            }

            function bindDigitsOnlyFields() {
                form.querySelectorAll('[data-digits-only]').forEach(function (field) {
                    sanitizeDigitsField(field);
                });

                form.addEventListener('beforeinput', function (event) {
                    const field = event.target;
                    if (!field.matches || !field.matches('[data-digits-only]')) {
                        return;
                    }

                    if (event.isComposing) {
                        return;
                    }

                    const allowedTypes = ['deleteContentBackward', 'deleteContentForward', 'deleteByCut', 'historyUndo', 'historyRedo'];
                    if (allowedTypes.includes(event.inputType)) {
                        return;
                    }

                    if (event.inputType === 'insertFromPaste') {
                        return;
                    }

                    if (event.data && /\D/.test(event.data)) {
                        event.preventDefault();
                        showDigitsOnlyHint(field);
                    }
                });

                form.addEventListener('input', function (event) {
                    const field = event.target;
                    if (!field.matches || !field.matches('[data-digits-only]')) {
                        return;
                    }

                    const hadNonDigits = sanitizeDigitsField(field);
                    if (hadNonDigits) {
                        showDigitsOnlyHint(field);
                    }

                    if (field.classList.contains('is-valid') || field.classList.contains('is-invalid')) {
                        validateField(field);
                    }
                });

                form.addEventListener('paste', function (event) {
                    const field = event.target;
                    if (!field.matches || !field.matches('[data-digits-only]')) {
                        return;
                    }

                    event.preventDefault();
                    const maxLength = parseInt(field.dataset.digitsOnly || field.getAttribute('maxlength') || '32', 10);
                    const pasted = (event.clipboardData || window.clipboardData).getData('text') || '';
                    const cleaned = pasted.replace(/\D/g, '').slice(0, maxLength);
                    if (pasted && pasted !== cleaned) {
                        showDigitsOnlyHint(field);
                    }
                    field.value = cleaned;
                    validateField(field);
                });

                form.addEventListener('drop', function (event) {
                    const field = event.target;
                    if (!field.matches || !field.matches('[data-digits-only]')) {
                        return;
                    }

                    event.preventDefault();
                    showDigitsOnlyHint(field);
                });
            }

            function bindFormatters() {
                bindDigitsOnlyFields();
            }

            function initLocationSelects() {
                if (typeof window.initWilayahCascade !== 'function') {
                    return;
                }

                window.initWilayahCascade({
                    provinceId: 'provinsi',
                    cityId: 'kota',
                    districtId: 'kecamatan',
                    provinceManualId: 'provinsi_manual',
                    provincePanelId: 'provinsi_override_panel',
                    provinceTriggerId: 'provinsi_override_btn',
                    cityManualId: 'kota_manual',
                    cityPanelId: 'kota_override_panel',
                    cityTriggerId: 'kota_override_btn',
                    districtManualId: 'kecamatan_manual',
                    districtPanelId: 'kecamatan_override_panel',
                    districtTriggerId: 'kecamatan_override_btn',
                    initial: locationDefaults,
                    routes: {
                        provinces: @json(route('api.provinces')),
                        cities: @json(route('api.cities')),
                        districts: @json(route('api.districts')),
                    },
                });
            }

            function updateWizardProgress(currentIndex) {
                document.querySelectorAll('#jamaah-wizard-progress .jamaah-wizard-step').forEach(function (item, index) {
                    item.classList.remove('is-active', 'is-done');
                    if (index < currentIndex) {
                        item.classList.add('is-done');
                    }
                    if (index === currentIndex) {
                        item.classList.add('is-active');
                    }
                });

                const caption = document.getElementById('jamaah-wizard-current-label');
                const labels = window.jamaahWizardStepLabels || [];
                if (caption && labels[currentIndex]) {
                    caption.textContent = 'Langkah ' + (currentIndex + 1) + ' dari ' + labels.length + ': ' + labels[currentIndex];
                }
            }

            function initWizard() {
                const $wizard = $('#jamaah-haji-khusus-wizard');
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
                        finish: finishLabel,
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
                bindFormatters();
                initLocationSelects();
                initWizard();
            });
        })();
    </script>
@endpush

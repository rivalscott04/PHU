@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pengaduan</h5>
                </div>
                <div class="card-body">
                    <!-- Validation Summary - will be shown dynamically -->
                    <div id="validation-summary" class="alert alert-danger d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-error-circle me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>Mohon lengkapi data berikut:</strong>
                                <ul id="validation-errors-list" class="mb-0 mt-2">
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form id="pengaduan-form" method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pengadu" class="form-label">Nama Pengadu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pengadu" name="nama_pengadu"
                                    value="{{ old('nama_pengadu') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="travels_id" class="form-label">Travel <span class="text-danger">*</span></label>
                                <select class="form-control" id="travels_id" name="travels_id" required>
                                    <option value="">-- Pilih Travel --</option>
                                    @foreach ($travels as $travel)
                                        <option value="{{ $travel->id }}" {{ old('travels_id') == $travel->id ? 'selected' : '' }}>
                                            {{ $travel->Penyelenggara }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="hal_aduan" class="form-label">Hal yang Diadukan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="hal_aduan" name="hal_aduan" rows="4" required>{{ old('hal_aduan') }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="berkas_aduan" class="form-label">Berkas Pendukung</label>
                                <input type="file" class="form-control" id="berkas_aduan" name="berkas_aduan" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">File maksimal 2MB. Format yang diperbolehkan: PDF, JPG, PNG, DOC, DOCX</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span class="btn-text">Kirim Pengaduan</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Mengirim...
                                </span>
                            </button>
                            <a href="{{ route('pengaduan') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('pengaduan-form');
        const submitBtn = document.getElementById('submit-btn');
        const validationSummary = document.getElementById('validation-summary');
        const validationErrorsList = document.getElementById('validation-errors-list');
        
        // Required fields
        const requiredFields = [
            { id: 'nama_pengadu', name: 'Nama Pengadu' },
            { id: 'travels_id', name: 'Travel' },
            { id: 'hal_aduan', name: 'Hal yang Diadukan' }
        ];

        // Form submission handler
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset validation states
            resetValidationStates();
            
            // Validate form
            const validationErrors = validateForm();
            
            if (validationErrors.length > 0) {
                showValidationErrors(validationErrors);
                scrollToFirstError();
                return;
            }
            
            // If validation passes, submit the form
            submitForm();
        });

        function validateForm() {
            const errors = [];
            
            // Validate required fields
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                const value = element.value.trim();
                
                if (!value) {
                    errors.push({
                        field: field.id,
                        message: `${field.name} wajib diisi`,
                        element: element
                    });
                }
            });
            
            // Validate file size if uploaded
            const fileInput = document.getElementById('berkas_aduan');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    errors.push({
                        field: 'berkas_aduan',
                        message: 'Ukuran file maksimal 2MB',
                        element: fileInput
                    });
                }
            }
            
            return errors;
        }

        function showValidationErrors(errors) {
            // Show validation summary
            validationErrorsList.innerHTML = '';
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error.message;
                validationErrorsList.appendChild(li);
            });
            validationSummary.classList.remove('d-none');
            
            // Mark fields as invalid
            errors.forEach(error => {
                const element = error.element;
                element.classList.add('is-invalid');
                
                const feedback = element.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = error.message;
                }
            });
        }

        function resetValidationStates() {
            // Hide validation summary
            validationSummary.classList.add('d-none');
            
            // Remove validation classes from all fields
            form.querySelectorAll('.form-control').forEach(field => {
                field.classList.remove('is-invalid', 'is-valid');
            });
            
            // Clear error messages
            form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                feedback.textContent = '';
            });
        }

        function scrollToFirstError() {
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstError.focus();
            }
        }

        function submitForm() {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.querySelector('.btn-text').classList.add('d-none');
            submitBtn.querySelector('.btn-loading').classList.remove('d-none');
            
            // Submit the form
            form.submit();
        }

        // Real-time validation on blur
        form.querySelectorAll('.form-control').forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });

        function validateField(field) {
            const value = field.value.trim();
            const fieldId = field.id;
            
            // Check if it's a required field
            const isRequired = requiredFields.some(f => f.id === fieldId);
            
            if (isRequired && !value) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                
                const feedback = field.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    const fieldName = requiredFields.find(f => f.id === fieldId).name;
                    feedback.textContent = `${fieldName} wajib diisi`;
                }
            } else if (fieldId === 'berkas_aduan' && field.files.length > 0) {
                // Validate file size
                const file = field.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = 'Ukuran file maksimal 2MB';
                    }
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = '';
                    }
                }
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                
                const feedback = field.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = '';
                }
            }
        }
    });
    </script>
@endsection

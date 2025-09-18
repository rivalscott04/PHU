@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Import User Travel via Excel</h5>
                <a href="{{ route('travels.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="row">
                    <!-- Form Upload Excel -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Upload File Excel</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('travels.import') }}" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <!-- Travel Selection -->
                                    <div class="mb-3">
                                        <label for="travel_id" class="form-label">Travel Company <span class="text-danger">*</span></label>
                                        <select class="form-control @error('travel_id') is-invalid @enderror" 
                                                id="travel_id" name="travel_id" required>
                                            <option value="">Pilih Travel Company</option>
                                            @foreach($travelCompanies as $travel)
                                                <option value="{{ $travel->id }}" {{ old('travel_id') == $travel->id ? 'selected' : '' }}>
                                                    {{ $travel->Penyelenggara }} - {{ $travel->kab_kota }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('travel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Semua user yang diimport akan dikaitkan dengan travel company yang dipilih
                                        </small>
                                    </div>

                                    <!-- File Upload -->
                                    <div class="mb-3">
                                        <label for="excel_file" class="form-label">File Excel <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                               id="excel_file" name="excel_file" 
                                               accept=".xlsx,.xls,.csv" required>
                                        @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Format file: Excel (.xlsx, .xls) atau CSV (.csv). Maksimal 10MB.
                                        </small>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-upload"></i> Import User
                                        </button>
                                        
                                        <a href="{{ route('travels.template') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-download"></i> Download Template
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Petunjuk Import</h6>
                            </div>
                            <div class="card-body">
                                <h6>Format Excel yang Diperlukan:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>nama</strong> - Wajib diisi</li>
                                    <li><strong>email</strong> - Wajib diisi</li>
                                    <li><strong>nomor_hp</strong> - Wajib diisi</li>
                                    <li><strong>password</strong> - Wajib diisi</li>
                                </ul>

                                <hr>

                                <h6>Catatan Penting:</h6>
                                <ul class="list-unstyled small">
                                    <li>• Email dan nomor HP harus unik</li>
                                    <li>• Password minimal 5 karakter</li>
                                    <li>• Semua user akan dikaitkan dengan travel yang dipilih</li>
                                    <li>• Kabupaten akan otomatis terisi dari travel company</li>
                                    <li>• Role akan otomatis set ke "user"</li>
                                    <li>• User bisa login dengan email atau nomor HP</li>
                                    <li>• Data profile lengkap dapat diisi nanti melalui halaman profile</li>
                                </ul>

                                <div class="alert alert-info mt-3">
                                    <small>
                                        <i class="bx bx-info-circle"></i>
                                        <strong>Tips:</strong> Download template Excel untuk melihat format yang benar dan contoh data
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Data Table -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Contoh Data Excel</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>nama</th>
                                                <th>email</th>
                                                <th>nomor_hp</th>
                                                <th>password</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>John Doe</td>
                                                <td>john.doe@travel1.com</td>
                                                <td>081234567890</td>
                                                <td>password123</td>
                                            </tr>
                                            <tr>
                                                <td>Jane Smith</td>
                                                <td>jane.smith@travel1.com</td>
                                                <td>081234567891</td>
                                                <td>password123</td>
                                            </tr>
                                            <tr>
                                                <td>Ahmad Wijaya</td>
                                                <td>ahmad.wijaya@travel2.com</td>
                                                <td>081234567892</td>
                                                <td>password123</td>
                                            </tr>
                                            <tr>
                                                <td>Siti Rahayu</td>
                                                <td>siti.rahayu@travel2.com</td>
                                                <td>081234567893</td>
                                                <td>password123</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

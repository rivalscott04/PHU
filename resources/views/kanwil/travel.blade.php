@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($guide = \App\Support\RoleWorkflowGuide::for('travel_master'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Data PPIU Pusat</h4>
                    <p class="text-muted mb-0 small">Kelola PPIU/PIHK dan verifikasi pendaftaran mandiri</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('form.travel') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Tambah
                    </a>
                    <a href="{{ route('travel.export') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-download me-1"></i> Export Excel
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bx bx-upload me-1"></i> Upload Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ ($filter ?? 'all') === 'all' ? 'active' : '' }}"
                               href="{{ route('travel') }}" role="tab">Semua</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ ($filter ?? '') === 'pending' ? 'active' : '' }}"
                               href="{{ route('travel', ['filter' => 'pending']) }}" role="tab">
                                Menunggu Verifikasi
                                @if (($pendingCount ?? 0) > 0)
                                    <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ ($filter ?? '') === 'approved' ? 'active' : '' }}"
                               href="{{ route('travel', ['filter' => 'approved']) }}" role="tab">Disetujui</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ ($filter ?? '') === 'rejected' ? 'active' : '' }}"
                               href="{{ route('travel', ['filter' => 'rejected']) }}" role="tab">Ditolak</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-sm-auto">
                            <label for="travelPerPageFilter" class="form-label mb-1">Tampilkan</label>
                            <select id="travelPerPageFilter" class="form-select form-select-sm">
                                @foreach([10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-auto">
                            <span class="form-text">data per halaman</span>
                        </div>
                        <div class="col-sm-auto ms-sm-auto">
                            <label for="travelSearchInput" class="form-label mb-1">Cari</label>
                            <input type="search" id="travelSearchInput" class="form-control form-control-sm"
                                placeholder="Penyelenggara, pimpinan, kab/kota..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped table-bordered table-hover nowrap w-100 mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th rowspan="2">No.</th>
                                    <th rowspan="2">Penyelenggara</th>
                                    <th colspan="6">Nomor SK</th>
                                    <th rowspan="2">Pimpinan</th>
                                    <th rowspan="2">Alamat Kantor Lama</th>
                                    <th rowspan="2">Alamat Kantor Baru</th>
                                    <th rowspan="2">Telepon</th>
                                    <th rowspan="2">Status</th>
                                    <th rowspan="2">Kab/Kota</th>
                                    <th rowspan="2">Registrasi</th>
                                    <th rowspan="2">Aksi</th>
                                </tr>
                                <tr>
                                    <th>Pusat</th>
                                    <th>Tanggal</th>
                                    <th>Jml Akre</th>
                                    <th>Tanggal Akredi</th>
                                    <th>Lembaga Akred</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody id="travelTableBody">
                                @include('kanwil.partials.travel-table-body', compact('data'))
                            </tbody>
                        </table>

                    <div id="travelPaginationContainer">
                        @include('kanwil.partials.travel-pagination', compact('data'))
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Registration Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Tolak Pendaftaran Travel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Travel: <strong id="rejectTravelName"></strong></p>
                        <div class="mb-3">
                            <label for="registration_notes" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="registration_notes" name="registration_notes" rows="4"
                                placeholder="Contoh: Dokumen SK belum lengkap, silakan daftar ulang." required></textarea>
                            <small class="text-muted">Alasan ini akan tersimpan di sistem.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Status Travel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="statusForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Travel Company</label>
                            <input type="text" class="form-control" id="travelName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="statusSelect" name="Status" required>
                                <option value="">Pilih Status</option>
                                <option value="PPIU">PPIU: Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)</option>
                                <option value="PIHK">PIHK: Penyelenggara Ibadah Haji Khusus (Haji & Umrah)</option>
                            </select>
                        </div>
                        
                        <!-- Status Info Banner -->
                        <div class="alert alert-info" id="statusInfo">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-info-circle font-size-18 me-2"></i>
                                <div>
                                    <h6 class="mb-1">Informasi Status</h6>
                                    <p class="mb-0" id="statusDescription">
                                        Pilih status untuk melihat informasi capabilities
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Capabilities Preview -->
                        <div class="card" id="capabilitiesCard" style="display: none;">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bx bx-list-check me-1"></i>
                                    Capabilities yang Akan Diperoleh
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="capabilitiesList">
                                    <!-- Capabilities will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data Travel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import.data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('travel.template') }}" class="text-sm">
                                <i class="bx bx-download"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function initTravelListing() {
            const searchInput = document.getElementById('travelSearchInput');
            const perPageFilter = document.getElementById('travelPerPageFilter');
            const currentFilter = @json($filter ?? 'all');

            if (!searchInput) {
                return;
            }

            let searchTimeout;

            function updateResultsInfo(data) {
                const info = data.pagination_info;
                const bottom = document.getElementById('travelResultsInfo');
                if (bottom) {
                    bottom.textContent = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
                }
            }

            function fetchListing(params = {}) {
                const queryParams = new URLSearchParams();
                if (currentFilter && currentFilter !== 'all') {
                    queryParams.append('filter', currentFilter);
                }
                if (searchInput.value.trim()) {
                    queryParams.append('search', searchInput.value.trim());
                }
                if (perPageFilter && perPageFilter.value) {
                    queryParams.append('per_page', perPageFilter.value);
                }
                if (params.page) {
                    queryParams.append('page', params.page);
                }

                fetch(`{{ route('travel') }}?${queryParams.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            return;
                        }

                        document.getElementById('travelTableBody').innerHTML = data.tableBody;
                        document.getElementById('travelPaginationContainer').innerHTML = data.pagination;
                        updateResultsInfo(data);
                        bindPaginationLinks();
                    });
            }

            function bindPaginationLinks() {
                document.querySelectorAll('#travelPaginationContainer .pagination a.page-link').forEach(function(link) {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            fetchListing({ page });
                        }
                    });
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(fetchListing, 350);
            });

            if (perPageFilter) {
                perPageFilter.addEventListener('change', fetchListing);
            }

            bindPaginationLinks();
        }

        document.addEventListener('DOMContentLoaded', initTravelListing);
    </script>

    <!-- Status Update JavaScript -->
    <script>
    function editStatus(id, currentStatus, travelName) {
        console.log('editStatus called:', { id, currentStatus, travelName });
        
        // Set form action - using the correct route pattern
        document.getElementById('statusForm').action = `/travel/${id}/status`;
        
        // Set travel name
        document.getElementById('travelName').value = travelName;
        
        // Set current status
        document.getElementById('statusSelect').value = currentStatus;
        
        // Update status info
        updateStatusInfo(currentStatus);
        
        // Show modal
        $('#statusModal').modal('show');
    }

    function updateStatusInfo(status) {
        const statusDescription = document.getElementById('statusDescription');
        const capabilitiesCard = document.getElementById('capabilitiesCard');
        const capabilitiesList = document.getElementById('capabilitiesList');
        
        if (status === 'PIHK') {
            statusDescription.innerHTML = '<strong>PIHK</strong> dapat menangani layanan Haji dan Umrah, termasuk Haji Khusus.';
            capabilitiesList.innerHTML = `
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle text-success me-2"></i>
                        <span>Haji</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle text-success me-2"></i>
                        <span>Umrah</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-star text-warning me-2"></i>
                        <span>Haji Khusus</span>
                    </div>
                </div>
            `;
        } else if (status === 'PPIU') {
            statusDescription.innerHTML = '<strong>PPIU</strong> hanya dapat menangani layanan Umrah.';
            capabilitiesList.innerHTML = `
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle text-secondary me-2"></i>
                        <span class="text-muted">Haji</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle text-success me-2"></i>
                        <span>Umrah</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle text-secondary me-2"></i>
                        <span class="text-muted">Haji Khusus</span>
                    </div>
                </div>
            `;
        } else {
            statusDescription.textContent = 'Pilih status untuk melihat informasi capabilities';
            capabilitiesCard.style.display = 'none';
            return;
        }
        
        capabilitiesCard.style.display = 'block';
    }

    // Handle status select change
    document.getElementById('statusSelect').addEventListener('change', function() {
        updateStatusInfo(this.value);
    });

    // Handle form submission
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submitted');
        
        const formData = new FormData(this);
        const url = this.action;
        
        console.log('URL:', url);
        console.log('FormData:', Object.fromEntries(formData));
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Updating...';
        submitBtn.disabled = true;
        
        // Now do the actual POST request
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                // Store current scroll position
                window.storedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                
                // Close modal first
                $('#statusModal').modal('hide');
                
                // Show success message
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    allowOutsideClick: true,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then((result) => {
                    // Update the status badge directly in the table
                    const travelId = data.travel_id;
                    console.log('Updating status for travel ID:', travelId);
                    
                    // Find the row with the correct travel ID
                    const row = document.querySelector(`tr[data-travel-id="${travelId}"]`);
                    console.log('Found row:', row);
                    
                    if (row) {
                        const statusCell = row.querySelector('.status-badge');
                        console.log('Found status cell:', statusCell);
                        
                        if (statusCell) {
                            const newStatus = data.new_status;
                            console.log('New status:', newStatus);
                            
                            if (newStatus === 'PIHK') {
                                statusCell.innerHTML = `
                                    <span class="badge bg-success">${newStatus}</span>
                                    <small class="text-muted mt-1">Haji & Umrah</small>
                                `;
                            } else {
                                statusCell.innerHTML = `
                                    <span class="badge bg-info">${newStatus}</span>
                                    <small class="text-muted mt-1">Umrah Only</small>
                                `;
                            }
                            console.log('Status updated in table');
                        }
                    }
                    
                    // Force reload with cache busting as backup
                    setTimeout(() => {
                        console.log('Reloading page...');
                        // Store scroll position before reload
                        sessionStorage.setItem('scrollPosition', window.pageYOffset || document.documentElement.scrollTop);
                        window.location.reload(true);
                    }, 1000);
                    
                    // Also try to reload immediately if direct update failed
                    if (!row || !statusCell) {
                        console.log('Direct update failed, reloading immediately...');
                        setTimeout(() => {
                            window.location.reload(true);
                        }, 500);
                    }
                }).catch(() => {
                    // Fallback if SweetAlert is dismissed
                    console.log('SweetAlert dismissed, reloading...');
                    window.location.reload(true);
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Gagal mengupdate status travel',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengupdate status: ' + error.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Reset form when modal is hidden
    $('#statusModal').on('hidden.bs.modal', function () {
        // Reset form
        document.getElementById('statusForm').reset();
        document.getElementById('capabilitiesCard').style.display = 'none';
        document.getElementById('statusDescription').textContent = 'Pilih status untuk melihat informasi capabilities';
        
        // Restore scroll position if it was stored
        if (window.storedScrollPosition) {
            window.scrollTo(0, window.storedScrollPosition);
            delete window.storedScrollPosition;
        }
    });

    function openRejectModal(travelId, travelName) {
        document.getElementById('rejectTravelName').textContent = travelName;
        document.getElementById('rejectForm').action = `/travel/${travelId}/reject-registration`;
        document.getElementById('registration_notes').value = '';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    // confirmApproveRegistration() provided globally by js/confirm-dialogs.js

    // Debug: Log when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Travel page loaded');
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Restore scroll position if it was stored
        const savedScrollPosition = sessionStorage.getItem('scrollPosition');
        if (savedScrollPosition) {
            setTimeout(() => {
                window.scrollTo(0, parseInt(savedScrollPosition));
                sessionStorage.removeItem('scrollPosition');
            }, 100);
        }
    });
    </script>
@endpush

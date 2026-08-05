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
                            <label for="dataTable_length" class="form-label mb-1">Tampilkan</label>
                            <select id="dataTable_length" class="form-select form-select-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="col-sm-auto">
                            <span class="form-text">data per halaman</span>
                        </div>
                        <div class="col-sm-auto ms-sm-auto">
                            <label for="dataTable_search" class="form-label mb-1">Cari</label>
                            <input type="search" id="dataTable_search" class="form-control form-control-sm">
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
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center align-middle" data-travel-id="{{ $item->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $item->Penyelenggara }}</td>
                                        <td>{{ $item->Pusat }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->Tanggal)) }}</td>
                                        <td>{{ $item->nilai_akreditasi }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal_akreditasi)) }}</td>
                                        <td>{{ $item->lembaga_akreditasi }}</td>
                                        <td>-</td>
                                        <td>{{ $item->Pimpinan }}</td>
                                        <td class="text-start">{{ $item->alamat_kantor_lama }}</td>
                                        <td class="text-start">{{ $item->alamat_kantor_baru }}</td>
                                        <td>{{ $item->Telepon }}</td>
                                        <td>
                                            <div class="d-flex flex-column align-items-center status-badge">
                                                <span class="badge {{ $item->Status === 'PIHK' ? 'bg-success' : 'bg-info' }}">
                                                    {{ $item->Status }}
                                                </span>
                                                <small class="text-muted mt-1">
                                                    @if($item->Status === 'PIHK')
                                                        Haji & Umrah
                                                    @else
                                                        Umrah Only
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                        <td>{{ $item->kab_kota }}</td>
                                        <td>
                                            @php
                                                $regStatus = $item->registration_status ?? \App\Enums\TravelRegistrationStatus::Approved;
                                            @endphp
                                            <span class="badge {{ $regStatus->badgeClass() }}">
                                                {{ $regStatus->label() }}
                                            </span>
                                            @if ($regStatus === \App\Enums\TravelRegistrationStatus::Pending && $item->user)
                                                <div class="mt-1">
                                                    <small class="text-muted d-block">{{ $item->user->nama }}</small>
                                                    <small class="text-muted d-block">{{ $item->user->email }}</small>
                                                </div>
                                                @if ($item->dokumen_sk || $item->dokumen_akreditasi)
                                                    <div class="mt-2 d-flex flex-column gap-1">
                                                        @if ($item->dokumen_sk)
                                                            @include('partials.document-preview-button', [
                                                                'url' => route('travel.registration.document', ['id' => $item->id, 'type' => 'sk']),
                                                                'path' => $item->dokumen_sk,
                                                                'label' => 'SK / Izin',
                                                            ])
                                                        @endif
                                                        @if ($item->dokumen_akreditasi)
                                                            @include('partials.document-preview-button', [
                                                                'url' => route('travel.registration.document', ['id' => $item->id, 'type' => 'akreditasi']),
                                                                'path' => $item->dokumen_akreditasi,
                                                                'label' => 'Akreditasi',
                                                            ])
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                            @if ($regStatus === \App\Enums\TravelRegistrationStatus::Rejected && $item->registration_notes)
                                                <small class="text-danger d-block mt-1">{{ Str::limit($item->registration_notes, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                @if (auth()->user()->role === 'admin' && ($item->registration_status ?? null) === \App\Enums\TravelRegistrationStatus::Pending)
                                                    <form method="POST" action="{{ route('travel.registration.approve', $item->id) }}" class="d-inline" id="approve-form-{{ $item->id }}">
                                                        @csrf
                                                        <button type="button" class="btn btn-success btn-sm" title="Setujui"
                                                                onclick='confirmApproveRegistration(document.getElementById("approve-form-{{ $item->id }}"), @json($item->Penyelenggara))'>
                                                            <i class="bx bx-check me-1"></i> Setujui
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                            onclick='openRejectModal({{ $item->id }}, @json($item->Penyelenggara))'
                                                            title="Tolak">
                                                        <i class="bx bx-x me-1"></i> Tolak
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        onclick='editStatus({{ $item->id }}, @json($item->Status), @json($item->Penyelenggara))'
                                                        title="Update Status">
                                                    <i class="bx bx-edit me-1"></i>
                                                    Status
                                                </button>
                                                <a href="{{ route('travel.edit', $item->id) }}" class="btn btn-sm btn-warning"
                                                    title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <div id="dataTable_info" class="text-muted small"></div>
                        <div id="dataTable_paginate"></div>
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
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable with custom DOM and scrolling
            var table = $('#dataTable').DataTable({
                // Change responsive to false and use scrollX instead
                responsive: false,
                scrollX: true, // Enable horizontal scrolling
                scrollCollapse: true,
                dom: 't', // Only show table
                language: {
                    paginate: {
                        previous: "<i class='fa fa-angle-left'></i>",
                        next: "<i class='fa fa-angle-right'></i>"
                    },
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 hingga 0 dari 0 data",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    infoFiltered: "(disaring dari _MAX_ total data)"
                },
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }], // Disable sorting on action column
                "drawCallback": function(settings) {
                    // Update info text
                    var info = this.api().page.info();
                    $('#dataTable_info').html('Menampilkan ' + (info.start + 1) + ' hingga ' + info
                        .end + ' dari ' + info.recordsTotal + ' data');

                    // Build custom pagination
                    var paginationHtml = '';
                    var pages = this.api().page.info().pages;
                    var currentPage = this.api().page.info().page;

                    paginationHtml += '<ul class="pagination pagination-sm mb-0">';

                    // Previous button
                    paginationHtml += '<li class="page-item' + (currentPage === 0 ? ' disabled' : '') +
                        '">';
                    paginationHtml +=
                        '<a class="page-link" href="#" data-page="prev"><i class="fas fa-chevron-left"></i></a></li>';

                    // Page numbers
                    var startPage = Math.max(0, currentPage - 2);
                    var endPage = Math.min(pages - 1, currentPage + 2);

                    for (var i = startPage; i <= endPage; i++) {
                        paginationHtml += '<li class="page-item' + (i === currentPage ? ' active' :
                            '') + '">';
                        paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + (i +
                            1) + '</a></li>';
                    }

                    // Next button
                    paginationHtml += '<li class="page-item' + (currentPage === pages - 1 ?
                        ' disabled' : '') + '">';
                    paginationHtml +=
                        '<a class="page-link" href="#" data-page="next"><i class="fas fa-chevron-right"></i></a></li>';

                    paginationHtml += '</ul>';

                    $('#dataTable_paginate').html(paginationHtml);

                    // Add event listeners to pagination
                    $('#dataTable_paginate .page-link').on('click', function(e) {
                        e.preventDefault();
                        var page = $(this).data('page');

                        if (page === 'prev') {
                            table.page('previous').draw('page');
                        } else if (page === 'next') {
                            table.page('next').draw('page');
                        } else {
                            table.page(page).draw('page');
                        }
                    });
                }
            });

            // Make sure the table redraws properly when window resizes
            $(window).on('resize', function() {
                table.columns.adjust().draw();
            });

            // Custom length change
            $('#dataTable_length').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Custom search
            $('#dataTable_search').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
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

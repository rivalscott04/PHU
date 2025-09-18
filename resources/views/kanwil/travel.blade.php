@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Travel</h6>
                    <div>
                        <a href="{{ route('form.travel') }}" class="btn btn-primary me-2">Tambah</a>
                        <a href="{{ route('travel.export') }}" class="btn btn-info me-2">
                            <i class="bx bx-download me-1"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <!-- DataTables length and search controls -->
                        <div class="d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center">
                                <label class="me-2 text-sm">Tampilkan</label>
                                <select id="dataTable_length" class="form-select form-select-sm me-2" style="width: 70px">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm">data per halaman</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="me-2 text-sm">Cari:</label>
                                <input type="search" id="dataTable_search" class="form-control form-control-sm"
                                    style="width: 200px">
                            </div>
                        </div>

                        <table id="dataTable" class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 5%">
                                        <div class="vertical-text">No.</div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Penyelenggara
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        colspan="6">
                                        Nomor SK
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Pimpinan
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Alamat Kantor Lama
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Alamat Kantor Baru
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Telepon
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 5%">
                                        Status
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Kab/Kota
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Aksi
                                    </th>
                                </tr>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Pusat
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jml Akre
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal Akredi
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Lembaga Akred
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        -
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center" data-travel-id="{{ $item->id }}">
                                        <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Penyelenggara }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Pusat }}</td>
                                        <td class="text-sm font-weight-bold">
                                            {{ date('d/m/Y', strtotime($item->Tanggal)) }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->nilai_akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">
                                            {{ date('d/m/Y', strtotime($item->tanggal_akreditasi)) }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->lembaga_akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">-</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Pimpinan }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_lama }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_baru }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Telepon }}</td>
                                        <td class="text-sm font-weight-bold text-center">
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
                                        <td class="text-sm font-weight-bold">{{ $item->kab_kota }}</td>
                                        <td class="text-sm font-weight-bold">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        onclick="editStatus({{ $item->id }}, '{{ $item->Status }}', '{{ $item->Penyelenggara }}')"
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

                        <!-- DataTables pagination -->
                        <div class="d-flex justify-content-between align-items-center px-4 py-3">
                            <div id="dataTable_info" class="text-sm text-secondary"></div>
                            <div id="dataTable_paginate" class="pagination"></div>
                        </div>
                    </div>
                </div>
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
                                <option value="PPIU">PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)</option>
                                <option value="PIHK">PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)</option>
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

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 d-flex justify-content-between align-items-center">
                    <h6>Data Jamaah Umrah</h6>
                    <div>
                        <a href="{{ route('jamaah.umrah.create') }}" class="btn btn-primary btn-md me-2">
                            <i class="bx bx-plus me-1"></i> Tambah
                        </a>
                        <button type="button" class="btn btn-success btn-md me-2" data-bs-toggle="modal"
                            data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                        <button type="button" class="btn btn-info btn-md" data-bs-toggle="modal"
                            data-bs-target="#exportModal">
                            <i class="bx bx-export me-1"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(auth()->user()->role === 'admin' && $groupedJamaah)
                        <!-- Global Search and Controls -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bx bx-search text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control border-start-0" 
                                               id="globalSearch"
                                               placeholder="Cari PPIU atau nama jamaah..."
                                               onkeyup="globalSearch()">
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="expandAll()">
                                            <i class="bx bx-expand-alt me-1"></i>Expand All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="collapseAll()">
                                            <i class="bx bx-collapse-alt me-1"></i>Collapse All
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        Total: <strong>{{ $groupedJamaah->count() }}</strong> PPIU, 
                                        <strong>{{ $groupedJamaah->sum(function($group) { return $group->count(); }) }}</strong> Jamaah
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        Showing: <span id="visibleCount">{{ $groupedJamaah->count() }}</span> PPIU
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Admin View: Modern Accordion Design -->
                        <div class="accordion" id="travelAccordion">
                            @foreach($groupedJamaah as $travelId => $jamaahGroup)
                                @php
                                    $travel = $jamaahGroup->first()->travel;
                                    $totalJamaah = $jamaahGroup->count();
                                    $accordionId = 'travel_' . $travelId;
                                @endphp
                                
                                <div class="accordion-item border-0 mb-3 shadow-sm travel-item" data-travel-name="{{ strtolower($travel->Penyelenggara ?? '') }}" data-kabupaten="{{ strtolower($travel->kab_kota ?? '') }}">
                                    <div class="accordion-header" id="heading_{{ $accordionId }}">
                                        <button class="accordion-button collapsed" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse_{{ $accordionId }}" 
                                                aria-expanded="false" 
                                                aria-controls="collapse_{{ $accordionId }}">
                                            <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="bx bx-building-house me-3 fs-4"></i>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-truncate" style="max-width: 300px;" title="{{ $travel->Penyelenggara ?? 'PPIU Tidak Diketahui' }}">
                                                            {{ $travel->Penyelenggara ?? 'PPIU Tidak Diketahui' }}
                                                        </h6>
                                                        <small class="opacity-75">{{ $travel->kab_kota ?? 'Kabupaten Tidak Diketahui' }}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark me-2">
                                                        <i class="bx bx-group me-1"></i>{{ $totalJamaah }} Jamaah
                                                    </span>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bx bx-calendar me-1"></i>{{ $travel->Status ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                    
                                    <div id="collapse_{{ $accordionId }}" 
                                         class="accordion-collapse collapse" 
                                         aria-labelledby="heading_{{ $accordionId }}" 
                                         data-bs-parent="#travelAccordion">
                                        <div class="accordion-body p-0">
                                            <!-- Search and Filter Bar -->
                                            <div class="bg-light p-3 border-bottom">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white border-end-0">
                                                                <i class="bx bx-search text-muted"></i>
                                                            </span>
                                                            <input type="text" 
                                                                   class="form-control border-start-0" 
                                                                   id="search_{{ $accordionId }}"
                                                                   placeholder="Cari jamaah di {{ $travel->Penyelenggara }}..."
                                                                   onkeyup="filterJamaah('{{ $accordionId }}')">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <button class="btn btn-sm btn-outline-primary me-2" onclick="exportJamaah('{{ $travelId }}')">
                                                            <i class="bx bx-export me-1"></i>Export
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="printJamaah('{{ $accordionId }}')">
                                                            <i class="bx bx-printer me-1"></i>Print
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Jamaah Table -->
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0" id="table_{{ $accordionId }}">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">No</th>
                                                            <th>Nama Jamaah</th>
                                                            <th>Alamat</th>
                                                            <th>No HP</th>
                                                            <th style="width: 200px;">NIK</th>
                                                            <th class="text-center" style="width: 120px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($jamaahGroup as $key => $item)
                                                            <tr class="jamaah-row" data-jamaah-name="{{ strtolower($item->nama) }}">
                                                                <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                                                <td>
                                                                    <h6 class="mb-0 fw-bold">{{ $item->nama }}</h6>
                                                                </td>
                                                                <td>
                                                                    <span class="text-sm text-truncate d-inline-block" style="max-width: 200px;" title="{{ $item->alamat }}">
                                                                        {{ $item->alamat }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-light text-dark">
                                                                        <i class="bx bx-phone me-1"></i>{{ $item->nomor_hp }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <span id="nik_{{ $item->id }}" 
                                                                              data-nik="{{ $item->nik }}" 
                                                                              class="text-monospace">{{ str_repeat('*', strlen($item->nik)) }}</span>
                                                                        <button class="btn btn-link btn-sm p-0 ms-2" 
                                                                                onclick="toggleNik('{{ $item->id }}')"
                                                                                title="Tampilkan/Sembunyikan NIK">
                                                                            <i id="icon_{{ $item->id }}" class="bx bxs-show text-primary"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="btn-group" role="group">
                                                                        <a href="{{ route('jamaah.detail', $item->id) }}" 
                                                                           class="btn btn-sm btn-outline-info" 
                                                                           title="Detail">
                                                                            <i class="bx bx-info-circle"></i>
                                                                        </a>
                                                                        <a href="{{ route('jamaah.edit', $item->id) }}" 
                                                                           class="btn btn-sm btn-outline-warning" 
                                                                           title="Edit">
                                                                            <i class="bx bx-edit"></i>
                                                                        </a>
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger" 
                                                                                onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama }}')"
                                                                                title="Hapus">
                                                                            <i class="bx bx-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                    <form id="delete-form-{{ $item->id }}"
                                                                          action="{{ route('jamaah.destroy', $item->id) }}" 
                                                                          method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <!-- Summary Footer -->
                                            <div class="bg-light p-3 border-top">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <small class="text-muted">
                                                            Menampilkan <strong>{{ $totalJamaah }}</strong> jamaah dari {{ $travel->Penyelenggara }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <small class="text-muted">
                                                            Terakhir diperbarui: {{ now()->format('d M Y H:i') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        

                    @else
                        <!-- Regular View: Normal Table -->
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Alamat</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No HP</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 200px; min-width: 200px;">NIK</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jamaah as $key => $item)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $key + 1 }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->nama }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->alamat }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->nomor_hp }}</td>
                                            <td class="text-sm font-weight-bold" style="width: 200px; min-width: 200px;">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span id="nik_{{ $item->id }}"
                                                        data-nik="{{ $item->nik }}">{{ str_repeat('*', strlen($item->nik)) }}</span>
                                                    <button class="btn btn-link p-0 ms-2"
                                                        onclick="toggleNik('{{ $item->id }}')">
                                                        <i id="icon_{{ $item->id }}" class="bx bxs-show"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('jamaah.detail', $item->id) }}">
                                                    <i class="bx bx-info-circle me-2"></i>
                                                </a>
                                                <a href="{{ route('jamaah.edit', $item->id) }}">
                                                    <i class="bx bx-edit text-success me-2"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama }}')">
                                                    <i class="bx bx-trash text-danger"></i>
                                                </a>
                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('jamaah.destroy', $item->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data Jamaah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jamaah.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        </div>
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Format yang didukung:</strong> .xlsx, .xls<br>
                            <strong>Download template:</strong> 
                            <a href="{{ route('jamaah.template') }}" class="alert-link">Template Excel</a>
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

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Data Jamaah Umrah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Global Export -->
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bx bx-globe me-2"></i>Export Global</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small">Export semua data jamaah dari semua PPIU dalam satu file dengan separator per PPIU.</p>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary" onclick="exportGlobal('excel')">
                                            <i class="bx bx-file me-2"></i>Export Excel Global
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="exportGlobal('pdf')">
                                            <i class="bx bx-file-pdf me-2"></i>Export PDF Global
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Travel Specific Export -->
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="bx bx-building me-2"></i>Export Per PPIU</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small">Export data jamaah dari PPIU tertentu saja.</p>
                                    <div class="mb-3">
                                        <label for="travelSelect" class="form-label">Pilih PPIU:</label>
                                        <select class="form-select" id="travelSelect">
                                            <option value="">Pilih PPIU...</option>
                                            @if(auth()->user()->role === 'admin' && $groupedJamaah)
                                                @foreach($groupedJamaah as $travelId => $jamaahGroup)
                                                    @php
                                                        $travel = $jamaahGroup->first()->travel;
                                                        $totalJamaah = $jamaahGroup->count();
                                                    @endphp
                                                    <option value="{{ $travelId }}" data-travel-name="{{ $travel->Penyelenggara ?? 'PPIU Tidak Diketahui' }}">
                                                        {{ $travel->Penyelenggara ?? 'PPIU Tidak Diketahui' }} ({{ $totalJamaah }} Jamaah)
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-success" onclick="exportByTravel('excel')" disabled id="exportTravelExcel">
                                            <i class="bx bx-file me-2"></i>Export Excel PPIU
                                        </button>
                                        <button type="button" class="btn btn-outline-info" onclick="exportByTravel('pdf')" disabled id="exportTravelPdf">
                                            <i class="bx bx-file-pdf me-2"></i>Export PDF PPIU
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Summary -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="bx bx-info-circle me-2"></i>Informasi Export</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Export Global:</strong> Semua data jamaah dengan separator per PPIU</li>
                                    <li><strong>Export Per PPIU:</strong> Data jamaah dari PPIU tertentu saja</li>
                                    <li><strong>Format Excel:</strong> File .xlsx dengan multiple sheets</li>
                                    <li><strong>Format PDF:</strong> File .pdf dengan header resmi Kemenag</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Accordion Styles - Following Skote Theme */
    .accordion-button {
        border: none;
        box-shadow: none;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .accordion-button:not(.collapsed) {
        background-color: #556ee6;
        color: white !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .accordion-button:not(.collapsed) h6,
    .accordion-button:not(.collapsed) small,
    .accordion-button:not(.collapsed) .badge {
        color: white !important;
    }
    
    .accordion-button:not(.collapsed) .badge.bg-light {
        background-color: rgba(255,255,255,0.2) !important;
        color: white !important;
    }
    
    /* Accordion arrow color when active */
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
        filter: brightness(0) invert(1) !important;
    }
    
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(85, 110, 230, 0.25);
    }
    
    .accordion-item {
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .accordion-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .avatar {
        width: 32px;
        height: 32px;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .btn-group .btn {
        border-radius: 4px;
        margin: 0 1px;
    }
    
    /* Summary Cards - Following Skote Theme */
    .card.bg-gradient-primary {
        background: linear-gradient(45deg, #556ee6, #6c757d) !important;
    }
    
    .card.bg-gradient-success {
        background: linear-gradient(45deg, #34c38f, #6c757d) !important;
    }
    
    .card.bg-gradient-info {
        background: linear-gradient(45deg, #50a5f1, #6c757d) !important;
    }
    
    .card.bg-gradient-warning {
        background: linear-gradient(45deg, #f1b44c, #6c757d) !important;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .accordion-button .d-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .accordion-button .d-flex .d-flex {
            margin-top: 10px;
            width: 100%;
            justify-content: space-between;
        }
    }
    </style>

    <script>
    // Toggle NIK visibility
    function toggleNik(id) {
        const nikSpan = document.getElementById('nik_' + id);
        const icon = document.getElementById('icon_' + id);
        const nik = nikSpan.getAttribute('data-nik');
        
        if (nikSpan.textContent.includes('*')) {
            nikSpan.textContent = nik;
            icon.className = 'bx bxs-hide text-primary';
        } else {
            nikSpan.textContent = '*'.repeat(nik.length);
            icon.className = 'bx bxs-show text-primary';
        }
    }
    
    // Filter jamaah within accordion
    function filterJamaah(accordionId) {
        const searchTerm = document.getElementById('search_' + accordionId).value.toLowerCase();
        const table = document.getElementById('table_' + accordionId);
        const rows = table.getElementsByClassName('jamaah-row');
        
        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    // Global search functionality
    function globalSearch() {
        const globalSearchInput = document.getElementById('globalSearch');
        const searchTerm = globalSearchInput.value.toLowerCase();
        const travelItems = document.querySelectorAll('.travel-item');
        const visibleCount = document.getElementById('visibleCount');
        let currentVisibleCount = 0;

        travelItems.forEach(item => {
            const travelName = item.getAttribute('data-travel-name');
            const kabupaten = item.getAttribute('data-kabupaten');
            const travelContent = item.querySelector('.accordion-body').innerHTML.toLowerCase();

            if (travelName.includes(searchTerm) || kabupaten.includes(searchTerm) || travelContent.includes(searchTerm)) {
                item.style.display = '';
                currentVisibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        visibleCount.textContent = currentVisibleCount;
    }

    // Expand all accordions
    function expandAll() {
        const accordions = document.querySelectorAll('.accordion-item');
        accordions.forEach(item => {
            const button = item.querySelector('.accordion-button');
            const collapse = item.querySelector('.accordion-collapse');
            if (button && collapse) {
                button.classList.remove('collapsed');
                collapse.classList.add('show');
            }
        });
    }

    // Collapse all accordions
    function collapseAll() {
        const accordions = document.querySelectorAll('.accordion-item');
        accordions.forEach(item => {
            const button = item.querySelector('.accordion-button');
            const collapse = item.querySelector('.accordion-collapse');
            if (button && collapse) {
                button.classList.add('collapsed');
                collapse.classList.remove('show');
            }
        });
    }
    
    // Export jamaah data (legacy function for individual travel export)
    function exportJamaah(travelId) {
        // Redirect to export route for specific travel
        window.open(`/jamaah/umrah/export?travel_id=${travelId}`, '_blank');
    }
    
    // Global export function
    function exportGlobal(format) {
        const url = `/jamaah/umrah/export?format=${format}&type=global`;
        window.open(url, '_blank');
    }
    
    // Export by travel function
    function exportByTravel(format) {
        const travelId = document.getElementById('travelSelect').value;
        if (!travelId) {
            alert('Silakan pilih PPIU terlebih dahulu!');
            return;
        }
        const url = `/jamaah/umrah/export?format=${format}&type=travel&travel_id=${travelId}`;
        window.open(url, '_blank');
    }
    
    // Enable/disable travel export buttons based on selection
    document.addEventListener('DOMContentLoaded', function() {
        const travelSelect = document.getElementById('travelSelect');
        const exportTravelExcel = document.getElementById('exportTravelExcel');
        const exportTravelPdf = document.getElementById('exportTravelPdf');
        
        if (travelSelect) {
            travelSelect.addEventListener('change', function() {
                const isSelected = this.value !== '';
                exportTravelExcel.disabled = !isSelected;
                exportTravelPdf.disabled = !isSelected;
            });
        }
    });
    
    // Print jamaah data
    function printJamaah(accordionId) {
        const printContent = document.getElementById('collapse_' + accordionId).innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Data Jamaah - Print</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>
                    <div class="container mt-4">
                        ${printContent}
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
    
    // Confirm delete
    function confirmDelete(id, nama) {
        if (confirm('Apakah Anda yakin ingin menghapus jamaah "' + nama + '"?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
    
    // Auto-expand first accordion on page load
    document.addEventListener('DOMContentLoaded', function() {
        const firstAccordion = document.querySelector('.accordion-item');
        if (firstAccordion) {
            const button = firstAccordion.querySelector('.accordion-button');
            const collapse = firstAccordion.querySelector('.accordion-collapse');
            if (button && collapse) {
                button.classList.remove('collapsed');
                collapse.classList.add('show');
            }
        }
    });
    </script>
@endsection

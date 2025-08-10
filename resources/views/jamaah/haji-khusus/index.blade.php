@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Jamaah Haji Khusus</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('jamaah.haji-khusus.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>
                            Tambah Jamaah
                        </a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bx bx-download me-1"></i>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                @if(auth()->user()->role === 'admin' && $groupedJamaahHajiKhusus)
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
                                           placeholder="Cari PIHK atau nama jamaah..."
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
                                    Total: <strong>{{ $groupedJamaahHajiKhusus->count() }}</strong> PIHK, 
                                    <strong>{{ $groupedJamaahHajiKhusus->sum(function($group) { return $group->count(); }) }}</strong> Jamaah
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    Showing: <span id="visibleCount">{{ $groupedJamaahHajiKhusus->count() }}</span> PIHK
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Admin View: Modern Accordion Design -->
                    <div class="accordion" id="travelAccordion">
                    @foreach($groupedJamaahHajiKhusus as $travelId => $jamaahGroup)
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
                                                    <h6 class="mb-0 fw-bold text-truncate" style="max-width: 300px;" title="{{ $travel->Penyelenggara ?? 'PIHK Tidak Diketahui' }}">
                                                        {{ $travel->Penyelenggara ?? 'PIHK Tidak Diketahui' }}
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
                                                        <th>Nama Lengkap</th>
                                                        <th>No. KTP</th>
                                                        <th>Usia</th>
                                                        <th>No. Paspor</th>
                                                        <th>No. SPPH</th>
                                                        <th>Status</th>
                                                        <th>Bukti Setor</th>
                                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($jamaahGroup as $key => $item)
                                                        <tr class="jamaah-row" data-jamaah-name="{{ strtolower($item->nama_lengkap) }}">
                                                            <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                                            <td>
                                                                <h6 class="mb-0 fw-bold">{{ $item->nama_lengkap }}</h6>
                                                                <small class="text-muted">{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                                            </td>
                                                            <td>
                                                                <code>{{ $item->no_ktp }}</code>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $usia = \Carbon\Carbon::parse($item->tanggal_lahir)->age;
                                                                @endphp
                                                                <span class="badge bg-primary">{{ $usia }} tahun</span>
                                                            </td>
                                                            <td>
                                                                <code>{{ $item->no_paspor ?? '-' }}</code>
                                                            </td>
                                                            <td>
                                                                <code>{{ $item->nomor_porsi ?? '-' }}</code>
                                                            </td>
                                                            <td>
                                                                @if($item->status_pendaftaran === 'pending')
                                                                    <span class="badge bg-warning">Menunggu</span>
                                                                @elseif($item->status_pendaftaran === 'approved')
                                                                    <span class="badge bg-success">Disetujui</span>
                                                                @elseif($item->status_pendaftaran === 'rejected')
                                                                    <span class="badge bg-danger">Ditolak</span>
                                                                @elseif($item->status_pendaftaran === 'completed')
                                                                    <span class="badge bg-info">Selesai</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ $item->status_pendaftaran }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($item->status_verifikasi_bukti === 'verified')
                                                                    <span class="badge bg-success">Terverifikasi</span>
                                                                @elseif($item->status_verifikasi_bukti === 'rejected')
                                                                    <span class="badge bg-danger">Ditolak</span>
                                                                @else
                                                                    <span class="badge bg-warning">Menunggu</span>
                                                                @endif
                                                                <br>
                                                                <button class="btn btn-sm btn-primary mt-1" onclick="verifikasiBuktiSetor('{{ $item->id }}')">
                                                                    Verifikasi
                                                                </button>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('jamaah.haji-khusus.show', $item->id) }}" 
                                                                       class="btn btn-sm btn-outline-info" 
                                                                       title="Detail">
                                                                        <i class="bx bx-info-circle"></i>
                                                                    </a>
                                                                    <a href="{{ route('jamaah.haji-khusus.edit', $item->id) }}" 
                                                                       class="btn btn-sm btn-outline-warning" 
                                                                       title="Edit">
                                                                        <i class="bx bx-edit"></i>
                                                                    </a>
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger" 
                                                                            onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama_lengkap }}')"
                                                                            title="Hapus">
                                                                        <i class="bx bx-trash"></i>
                                                                    </button>
                                                                </div>
                                                                <form id="delete-form-{{ $item->id }}"
                                                                      action="{{ route('jamaah.haji-khusus.destroy', $item->id) }}" 
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
                                    </div>
                                </div>
                            </div>
                    @endforeach
                    </div>
                @else
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('jamaah.haji-khusus.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama, KTP, paspor..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('jamaah.haji-khusus.index') }}" class="btn btn-secondary">
                                <i class="bx bx-refresh me-1"></i>
                                Reset Filter
                            </a>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. KTP</th>
                                    <th>Usia</th>
                                    <th>No. Paspor</th>
                                    <th>No. SPPH</th>
                                    <th>Status</th>
                                    <th>Bukti Setor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jamaahHajiKhusus as $index => $jamaah)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $jamaah->nama_lengkap }}</h6>
                                            <small class="text-muted">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $jamaah->no_ktp }}</code>
                                    </td>
                                    <td>
                                        @php
                                            $usia = \Carbon\Carbon::parse($jamaah->tanggal_lahir)->age;
                                        @endphp
                                        <span class="badge bg-primary">{{ $usia }} tahun</span>
                                    </td>
                                    <td>
                                        <code>{{ $jamaah->no_paspor ?? '-' }}</code>
                                    </td>
                                    <td>
                                        <code>{{ $jamaah->nomor_porsi ?? '-' }}</code>
                                    </td>
                                    <td>
                                        @if($jamaah->status_pendaftaran === 'pending')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($jamaah->status_pendaftaran === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($jamaah->status_pendaftaran === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @elseif($jamaah->status_pendaftaran === 'completed')
                                            <span class="badge bg-info">Selesai</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $jamaah->status_pendaftaran }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($jamaah->status_verifikasi_bukti === 'verified')
                                            <span class="badge bg-success">Terverifikasi</span>
                                        @elseif($jamaah->status_verifikasi_bukti === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning">Menunggu</span>
                                        @endif
                                        <br>
                                        <button class="btn btn-sm btn-primary mt-1" onclick="verifikasiBuktiSetor('{{ $jamaah->id }}')">
                                            Verifikasi
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('jamaah.haji-khusus.show', $jamaah->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Detail">
                                                <i class="bx bx-info-circle"></i>
                                            </a>
                                            <a href="{{ route('jamaah.haji-khusus.edit', $jamaah->id) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete('{{ $jamaah->id }}', '{{ $jamaah->nama_lengkap }}')"
                                                    title="Hapus">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $jamaah->id }}"
                                              action="{{ route('jamaah.haji-khusus.destroy', $jamaah->id) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data jamaah haji khusus</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center mt-3">
                            {{ $jamaahHajiKhusus->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Jamaah Haji Khusus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('jamaah.haji-khusus.export') }}" class="btn btn-success">
                        <i class="bx bx-file me-2"></i>Export Excel (Global)
                    </a>
                    <a href="{{ route('jamaah.haji-khusus.export-pdf') }}" class="btn btn-danger">
                        <i class="bx bx-file-pdf me-2"></i>Export PDF (Global)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function confirmDelete(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus jamaah "${name}"?`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}

function verifikasiBuktiSetor(id) {
    const status = prompt('Masukkan status verifikasi (verified/rejected):');
    if (status && (status === 'verified' || status === 'rejected')) {
        const catatan = prompt('Masukkan catatan verifikasi (opsional):');
        
        fetch(`/jamaah/haji-khusus/${id}/verify-bukti-setor`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                catatan: catatan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status verifikasi berhasil diperbarui');
                location.reload();
            } else {
                alert('Gagal memperbarui status verifikasi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui status verifikasi');
        });
    }
}

// Global search functionality
function globalSearch() {
    const searchTerm = document.getElementById('globalSearch').value.toLowerCase();
    const travelItems = document.querySelectorAll('.travel-item');
    let visibleCount = 0;

    travelItems.forEach(item => {
        const travelName = item.getAttribute('data-travel-name');
        const kabupaten = item.getAttribute('data-kabupaten');
        
        if (travelName.includes(searchTerm) || kabupaten.includes(searchTerm)) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('visibleCount').textContent = visibleCount;
}

// Filter jamaah within accordion
function filterJamaah(accordionId) {
    const searchTerm = document.getElementById(`search_${accordionId}`).value.toLowerCase();
    const table = document.getElementById(`table_${accordionId}`);
    const rows = table.querySelectorAll('.jamaah-row');

    rows.forEach(row => {
        const jamaahName = row.getAttribute('data-jamaah-name');
        if (jamaahName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Expand all accordions
function expandAll() {
    const accordionButtons = document.querySelectorAll('#travelAccordion .accordion-button.collapsed');
    accordionButtons.forEach(button => {
        button.click();
    });
}

// Collapse all accordions
function collapseAll() {
    const accordionButtons = document.querySelectorAll('#travelAccordion .accordion-button:not(.collapsed)');
    accordionButtons.forEach(button => {
        button.click();
    });
}

// Debug function to check accordion state
function debugAccordion() {
    const accordionButtons = document.querySelectorAll('#travelAccordion .accordion-button');
    accordionButtons.forEach((button, index) => {
        console.log(`Accordion ${index + 1}:`, {
            collapsed: button.classList.contains('collapsed'),
            classes: button.className,
            background: window.getComputedStyle(button).backgroundColor
        });
    });
}



// Export jamaah for specific travel
function exportJamaah(travelId) {
    if (confirm('Export data jamaah untuk travel ini?')) {
        window.open(`/jamaah/haji-khusus/export?travel_id=${travelId}`, '_blank');
    }
}

// Print jamaah for specific accordion
function printJamaah(accordionId) {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById(`table_${accordionId}`).cloneNode(true);
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Jamaah Haji Khusus</title>
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h2>Data Jamaah Haji Khusus</h2>
                ${table.outerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}
</script>

<style>
    .accordion-button {
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
    
    .accordion-button:not(.collapsed) .badge.bg-warning {
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
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .btn-group .btn {
        border-radius: 4px;
        margin: 0 1px;
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
@endsection 
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Pengaduan</h4>
                    <p class="text-muted mb-0 small">Kelola dan pantau pengaduan masyarakat di wilayah Anda</p>
                </div>
                <a href="{{ route('pengaduan.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Pengaduan
                </a>
            </div>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('pengaduan'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pengaduan</p>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Belum Diproses</p>
                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Sedang Diproses</p>
                    <h4 class="mb-0">{{ $stats['in_progress'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Selesai</p>
                    <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Data Pengaduan</h5>
                    <small class="text-muted" id="pengaduanResultsInfoTop">Total halaman: <strong>{{ $pengaduan->total() }}</strong></small>
                </div>
                <div class="p-3 border-bottom bg-light">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="pengaduanSearchInput"
                                    placeholder="Cari travel, hal aduan, pengadu..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="pengaduanStatusFilter" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                                <option value="in_progress" @selected(request('status') === 'in_progress')>Sedang Diproses</option>
                                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="pengaduanPerPageFilter" class="form-select form-select-sm">
                                @foreach([10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }} / halaman</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Travel yang diadukan</th>
                                <th>Hal Aduan</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengaduanTableBody">
                            @include('pengaduan.partials.table-body', compact('pengaduan'))
                        </tbody>
                    </table>
                </div>
                <div id="pengaduanPaginationContainer">
                    @include('pengaduan.partials.pagination', compact('pengaduan'))
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('status-dropdown')) {
        const id = e.target.dataset.id;
        const newStatus = e.target.value;
        const currentStatus = e.target.dataset.currentStatus;

        if (newStatus !== currentStatus) {
            confirmAction({
                title: 'Yakin ingin mengubah status pengaduan ini?',
                icon: 'question',
                confirmText: 'Ya, ubah',
            }).then((result) => {
                if (!result.isConfirmed) {
                    e.target.value = currentStatus;
                    return;
                }

                fetch(`/pengaduan/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        admin_notes: ''
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        e.target.dataset.currentStatus = newStatus;
                        location.reload();
                    } else {
                        Swal.fire({ title: 'Gagal', text: 'Error: ' + data.message, icon: 'error', confirmButtonColor: '#556ee6' });
                        e.target.value = currentStatus;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan saat update status', icon: 'error', confirmButtonColor: '#556ee6' });
                    e.target.value = currentStatus;
                });
            });
        }
    }
});

function initPengaduanListing() {
    const searchInput = document.getElementById('pengaduanSearchInput');
    const statusFilter = document.getElementById('pengaduanStatusFilter');
    const perPageFilter = document.getElementById('pengaduanPerPageFilter');

    if (!searchInput) {
        return;
    }

    let searchTimeout;

    function updateResultsInfo(data) {
        const info = data.pagination_info;
        const top = document.getElementById('pengaduanResultsInfoTop');
        const bottom = document.getElementById('pengaduanResultsInfo');

        if (top) {
            top.innerHTML = `Total halaman: <strong>${info.total}</strong>`;
        }
        if (bottom) {
            bottom.textContent = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
        }
    }

    function fetchListing(params = {}) {
        const queryParams = new URLSearchParams();
        if (searchInput.value.trim()) {
            queryParams.append('search', searchInput.value.trim());
        }
        if (statusFilter && statusFilter.value) {
            queryParams.append('status', statusFilter.value);
        }
        if (perPageFilter && perPageFilter.value) {
            queryParams.append('per_page', perPageFilter.value);
        }
        if (params.page) {
            queryParams.append('page', params.page);
        }

        fetch(`{{ route('pengaduan') }}?${queryParams.toString()}`, {
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

                document.getElementById('pengaduanTableBody').innerHTML = data.tableBody;
                document.getElementById('pengaduanPaginationContainer').innerHTML = data.pagination;
                updateResultsInfo(data);
                bindPaginationLinks();
            });
    }

    function bindPaginationLinks() {
        document.querySelectorAll('#pengaduanPaginationContainer .pagination a.page-link').forEach(function(link) {
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

    if (statusFilter) {
        statusFilter.addEventListener('change', fetchListing);
    }

    if (perPageFilter) {
        perPageFilter.addEventListener('change', fetchListing);
    }

    bindPaginationLinks();
}

document.addEventListener('DOMContentLoaded', initPengaduanListing);
</script>
@endpush

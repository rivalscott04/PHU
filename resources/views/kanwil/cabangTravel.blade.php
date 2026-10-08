@extends('layouts.app')

@section('content')
    @if($guide = \App\Support\RoleWorkflowGuide::for('cabang_travel'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Data PPIU Cabang</h4>
                    <p class="text-muted mb-0 small">Kelola data cabang travel di wilayah Anda</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @include('partials.excel-scope-menu', [
                        'exportRoute' => 'cabang.travel.export',
                        'buttonClass' => 'btn-outline-info',
                        'menuId' => 'cabangTravelExportMenu',
                    ])
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bx bx-upload me-1"></i> Import Data
                    </button>
                    <a href="{{ route('form.cabang_travel') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Cabang Travel</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 flex-wrap gap-1">
                        @foreach ([
                            '' => 'Semua',
                            'pending' => 'Menunggu Verifikasi',
                            'menunggu_kanwil' => 'Menunggu Kanwil',
                            'perlu_perbaikan' => 'Perlu Perbaikan',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ] as $value => $label)
                            @php($jumlah = $antrian[$value] ?? 0)
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 {{ request('filter', '') === $value ? 'active' : '' }}"
                                    href="{{ route('cabang.travel', array_filter(['filter' => $value, 'search' => request('search')])) }}">
                                    {{ $label }}
                                    @if ($jumlah > 0)
                                        <span class="badge bg-danger ms-1">{{ $jumlah }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-sm-auto">
                            <label for="cabangTravelPerPageFilter" class="form-label mb-1">Tampilkan</label>
                            <select id="cabangTravelPerPageFilter" class="form-select form-select-sm">
                                @foreach([10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-auto">
                            <span class="form-text">data per halaman</span>
                        </div>
                        <div class="col-sm-auto ms-sm-auto">
                            <label for="cabangTravelSearchInput" class="form-label mb-1">Cari</label>
                            <input type="search" id="cabangTravelSearchInput" class="form-control form-control-sm"
                                placeholder="Travel, pimpinan, kabupaten..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="cabangTravelTable" class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Travel</th>
                                    <th>Kabupaten</th>
                                    <th>Status</th>
                                    <th>No. SK Pusat</th>
                                    <th>No SK / BA</th>
                                    <th>Tanggal</th>
                                    <th>Kepala Cabang</th>
                                    <th>Telepon</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cabangTravelTableBody">
                                @include('kanwil.partials.cabang-travel-table-body', compact('data'))
                            </tbody>
                        </table>

                        <div id="cabangTravelPaginationContainer">
                            @include('kanwil.partials.cabang-travel-pagination', compact('data'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Data -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Import Data Cabang Travel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import.cabang_travel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls"
                                required>
                            <div class="form-text">Format file yang didukung: .xlsx, .xls</div>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('template/cabang-travel.xlsx') }}" class="text-sm text-decoration-none">
                                <i class="bx bx-download"></i> Download Template Excel
                            </a>
                        </div>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <small>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Download template Excel terlebih dahulu</li>
                                    <li>Isi data sesuai format yang tersedia</li>
                                    <li>Pastikan semua kolom wajib terisi</li>
                                    <li>Upload file yang sudah diisi</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bx bx-upload me-1"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function initCabangTravelListing() {
            const searchInput = document.getElementById('cabangTravelSearchInput');
            const perPageFilter = document.getElementById('cabangTravelPerPageFilter');

            if (!searchInput) {
                return;
            }

            let searchTimeout;
            let currentPage = new URLSearchParams(window.location.search).get('page') || '1';
            const exportRoot = document.getElementById('cabangTravelExportMenu');
            const exportBase = @json(route('cabang.travel.export'));

            function syncExportLinks() {
                if (!exportRoot) {
                    return;
                }

                const params = new URLSearchParams(window.location.search);
                const keyword = searchInput.value.trim();

                if (keyword) {
                    params.set('search', keyword);
                } else {
                    params.delete('search');
                }

                if (perPageFilter && perPageFilter.value) {
                    params.set('per_page', perPageFilter.value);
                }

                exportRoot.querySelectorAll('[data-excel-scope]').forEach(function (link) {
                    const next = new URLSearchParams(params);
                    next.set('scope', link.dataset.excelScope);

                    if (link.dataset.excelScope === 'page') {
                        next.set('page', currentPage);
                    } else {
                        next.delete('page');
                    }

                    link.href = `${exportBase}?${next.toString()}`;
                });
            }

            function updateResultsInfo(data) {
                const info = data.pagination_info;
                const bottom = document.getElementById('cabangTravelResultsInfo');
                if (bottom) {
                    bottom.textContent = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
                }
            }

            function fetchListing(params = {}) {
                const queryParams = new URLSearchParams(window.location.search);
                queryParams.delete('page');
                queryParams.delete('search');
                queryParams.delete('per_page');
                if (searchInput.value.trim()) {
                    queryParams.append('search', searchInput.value.trim());
                }
                if (perPageFilter && perPageFilter.value) {
                    queryParams.append('per_page', perPageFilter.value);
                }
                if (params.page) {
                    queryParams.append('page', params.page);
                }

                fetch(`{{ route('cabang.travel') }}?${queryParams.toString()}`, {
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

                        document.getElementById('cabangTravelTableBody').innerHTML = data.tableBody;
                        document.getElementById('cabangTravelPaginationContainer').innerHTML = data.pagination;
                        if (data.pagination_info && data.pagination_info.current_page) {
                            currentPage = String(data.pagination_info.current_page);
                        }
                        updateResultsInfo(data);
                        bindPaginationLinks();
                        syncExportLinks();
                    });
            }

            function bindPaginationLinks() {
                document.querySelectorAll('#cabangTravelPaginationContainer .pagination a.page-link').forEach(function(link) {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            currentPage = page;
                            syncExportLinks();
                            fetchListing({ page });
                        }
                    });
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                currentPage = '1';
                syncExportLinks();
                searchTimeout = setTimeout(fetchListing, 350);
            });

            if (perPageFilter) {
                perPageFilter.addEventListener('change', function () {
                    currentPage = '1';
                    syncExportLinks();
                    fetchListing();
                });
            }

            bindPaginationLinks();
            syncExportLinks();
        }

        document.addEventListener('DOMContentLoaded', initCabangTravelListing);
    </script>
@endpush

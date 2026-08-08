@props([
    'jamaahHajiKhusus',
    'showTravelColumn' => false,
])

<div class="p-3 border-bottom bg-light">
    <div class="row align-items-center g-2">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text"
                    class="form-control border-start-0"
                    id="hajiKhususSearchInput"
                    placeholder="Cari nama, NIK, paspor, PIHK..."
                    value="{{ request('search') }}"
                    autocomplete="off">
                <div class="haji-khusus-search-loading px-2 align-self-center" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select form-select-sm" id="hajiKhususStatusFilter">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-sm" id="hajiKhususPerPageFilter">
                @foreach([10, 15, 25, 50] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }} / halaman</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 text-md-end">
            <small class="text-muted" id="hajiKhususResultsInfoTop">
                Total: <strong>{{ $jamaahHajiKhusus->total() }}</strong> jamaah
            </small>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>No</th>
                @if($showTravelColumn)
                    <th>PIHK / Kab/Kota</th>
                @endif
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Usia</th>
                <th>No. Paspor</th>
                <th>No. SPPH</th>
                <th>Status</th>
                <th>Bukti Setor</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="hajiKhususTableBody">
            @include('jamaah.haji-khusus.partials.table-body', compact('jamaahHajiKhusus', 'showTravelColumn'))
        </tbody>
    </table>
</div>

<div id="hajiKhususPaginationContainer">
    @include('jamaah.haji-khusus.partials.pagination', compact('jamaahHajiKhusus'))
</div>

@once
    @push('js')
        <script>
            function initHajiKhususListing(config) {
                const searchInput = document.getElementById('hajiKhususSearchInput');
                const statusFilter = document.getElementById('hajiKhususStatusFilter');
                const perPageFilter = document.getElementById('hajiKhususPerPageFilter');
                const loadingEl = document.querySelector('.haji-khusus-search-loading');

                if (!searchInput) {
                    return;
                }

                let searchTimeout;

                function updateResultsInfo(data) {
                    const info = data.pagination_info;
                    const top = document.getElementById('hajiKhususResultsInfoTop');
                    const bottom = document.getElementById('hajiKhususResultsInfo');

                    if (top) {
                        top.innerHTML = `Total: <strong>${info.total}</strong> jamaah`;
                    }
                    if (bottom) {
                        bottom.textContent = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
                    }
                }

                function fetchListing(params = {}) {
                    if (loadingEl) {
                        loadingEl.style.display = 'block';
                    }

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

                    fetch(`${config.listingUrl}?${queryParams.toString()}`, {
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

                            document.getElementById('hajiKhususTableBody').innerHTML = data.tableBody;
                            document.getElementById('hajiKhususPaginationContainer').innerHTML = data.pagination;
                            updateResultsInfo(data);
                            bindPaginationLinks();
                        })
                        .catch(error => console.error('Haji khusus listing fetch error:', error))
                        .finally(() => {
                            if (loadingEl) {
                                loadingEl.style.display = 'none';
                            }
                        });
                }

                function bindPaginationLinks() {
                    document.querySelectorAll('#hajiKhususPaginationContainer .pagination a.page-link').forEach(function(link) {
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
                    searchTimeout = setTimeout(function() {
                        fetchListing();
                    }, 350);
                });

                if (statusFilter) {
                    statusFilter.addEventListener('change', function() {
                        fetchListing();
                    });
                }

                if (perPageFilter) {
                    perPageFilter.addEventListener('change', function() {
                        fetchListing();
                    });
                }

                bindPaginationLinks();
            }
        </script>
    @endpush
@endonce

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initHajiKhususListing({
                listingUrl: @json(route('jamaah.haji-khusus.index')),
            });
        });
    </script>
@endpush

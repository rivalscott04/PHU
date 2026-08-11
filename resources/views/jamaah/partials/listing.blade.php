@props([
    'listingRoute',
    'jamaah',
    'showTravelColumn' => false,
    'travelOptions' => null,
])

@php
    $hasTravelFilter = ($showTravelColumn ?? false) && $travelOptions && $travelOptions->isNotEmpty();
@endphp

@include('jamaah.partials.listing-assets')

<div class="p-3 border-bottom bg-light">
    <div class="row align-items-center g-2">
        <div class="{{ $hasTravelFilter ? 'col-md-4' : 'col-md-6' }}">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text"
                    class="form-control border-start-0"
                    id="jamaahSearchInput"
                    placeholder="Cari nama, NIK, PPIU, kab/kota..."
                    value="{{ request('search') }}"
                    autocomplete="off">
                <div class="search-loading px-2 align-self-center" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
        </div>
        @if($hasTravelFilter)
            <div class="col-md-3">
                <select class="form-select" id="jamaahTravelFilter" data-placeholder="Semua PPIU">
                    <option value="">Semua PPIU</option>
                    @foreach($travelOptions as $travelOption)
                        <option value="{{ $travelOption->id }}" @selected((int) request('travel_id') === $travelOption->id)>
                            {{ $travelOption->Penyelenggara }} ({{ $travelOption->jamaah_count }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-2">
            <select class="form-select form-select-sm" id="jamaahPerPageFilter">
                @foreach([10, 15, 25, 50] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }} / halaman</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 text-md-end">
            <small class="text-muted" id="jamaahResultsInfoTop">
                Total: <strong>{{ $jamaah->total() }}</strong> jamaah
            </small>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-items-center mb-0">
        <thead class="table-light">
            <tr class="text-center">
                <th>No</th>
                <th>Nama</th>
                @if($showTravelColumn)
                    <th>PPIU / Kab/Kota</th>
                @endif
                <th>Alamat</th>
                <th>No HP</th>
                <th style="width: 200px; min-width: 200px;">NIK</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="jamaahTableBody">
            @include('jamaah.partials.table-body', compact('jamaah', 'showTravelColumn'))
        </tbody>
    </table>
</div>

<div id="jamaahPaginationContainer">
    @include('jamaah.partials.pagination', compact('jamaah'))
</div>

@once
    @push('js')
        <script>
            function toggleJamaahNik(id) {
                const nikSpan = document.getElementById('nik_' + id);
                const icon = document.getElementById('icon_' + id);
                if (!nikSpan || !icon) {
                    return;
                }

                const nik = nikSpan.getAttribute('data-nik');
                if (nikSpan.textContent.includes('*')) {
                    nikSpan.textContent = nik;
                    icon.className = 'bx bxs-hide text-success';
                } else {
                    nikSpan.textContent = '*'.repeat(nik.length);
                    icon.className = 'bx bxs-show text-success';
                }
            }

            function initJamaahListing(config) {
                const searchInput = document.getElementById('jamaahSearchInput');
                const perPageFilter = document.getElementById('jamaahPerPageFilter');
                const travelFilter = document.getElementById('jamaahTravelFilter');
                const loadingEl = document.querySelector('.search-loading');

                if (!searchInput) {
                    return;
                }

                let searchTimeout;

                function activeFilters() {
                    const params = new URLSearchParams();
                    if (searchInput.value.trim()) {
                        params.append('search', searchInput.value.trim());
                    }
                    if (travelFilter && travelFilter.value) {
                        params.append('travel_id', travelFilter.value);
                    }

                    return params;
                }

                // Tautan unduh selalu membawa filter yang sedang aktif, supaya
                // isi file sama dengan yang terlihat di layar.
                function syncExportLinks() {
                    const filters = activeFilters().toString();

                    document.querySelectorAll('[data-jamaah-export-url]').forEach(function (link) {
                        const base = link.getAttribute('data-jamaah-export-url');
                        link.href = filters ? `${base}&${filters}` : base;
                    });
                }

                function updateResultsInfo(data) {
                    const info = data.pagination_info;
                    const text = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
                    const top = document.getElementById('jamaahResultsInfoTop');
                    const bottom = document.getElementById('jamaahResultsInfo');

                    if (top) {
                        top.innerHTML = `Total: <strong>${info.total}</strong> jamaah`;
                    }
                    if (bottom) {
                        bottom.textContent = text;
                    }
                }

                function fetchJamaahListing(params = {}) {
                    if (loadingEl) {
                        loadingEl.style.display = 'block';
                    }

                    const queryParams = activeFilters();
                    if (perPageFilter && perPageFilter.value) {
                        queryParams.append('per_page', perPageFilter.value);
                    }
                    if (params.page) {
                        queryParams.append('page', params.page);
                    }

                    syncExportLinks();

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

                            document.getElementById('jamaahTableBody').innerHTML = data.tableBody;
                            document.getElementById('jamaahPaginationContainer').innerHTML = data.pagination;
                            updateResultsInfo(data);
                            bindJamaahPaginationLinks();
                        })
                        .catch(error => console.error('Jamaah listing fetch error:', error))
                        .finally(() => {
                            if (loadingEl) {
                                loadingEl.style.display = 'none';
                            }
                        });
                }

                function bindJamaahPaginationLinks() {
                    document.querySelectorAll('#jamaahPaginationContainer .pagination a.page-link').forEach(function(link) {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            const url = new URL(this.href);
                            const page = url.searchParams.get('page');
                            if (page) {
                                fetchJamaahListing({ page });
                            }
                        });
                    });
                }

                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        fetchJamaahListing();
                    }, 350);
                });

                if (perPageFilter) {
                    perPageFilter.addEventListener('change', function() {
                        fetchJamaahListing();
                    });
                }

                if (travelFilter) {
                    if (window.jQuery && jQuery.fn.select2) {
                        jQuery(travelFilter)
                            .select2({
                                width: '100%',
                                placeholder: travelFilter.dataset.placeholder,
                                allowClear: true,
                            })
                            .on('change', function () {
                                fetchJamaahListing();
                            });
                    } else {
                        travelFilter.addEventListener('change', function () {
                            fetchJamaahListing();
                        });
                    }
                }

                syncExportLinks();
                bindJamaahPaginationLinks();
            }
        </script>
    @endpush
@endonce

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initJamaahListing({
                listingUrl: @json(route($listingRoute)),
            });
        });
    </script>
@endpush

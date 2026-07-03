@extends('layouts.app')

@php
    use App\Enums\UserRole;

    $tabs = [
        UserRole::Pimpinan->value => [
            'label' => 'Pimpinan Kanwil',
            'description' => 'Dashboard seluruh NTB',
        ],
        UserRole::Kabupaten->value => [
            'label' => 'Admin Kabupaten',
            'description' => 'Data per kabupaten',
        ],
        UserRole::Pengawas->value => [
            'label' => 'Pengawas',
            'description' => 'Pengawasan per wilayah',
        ],
        UserRole::User->value => [
            'label' => 'User Travel (PPIU)',
            'description' => 'Akun travel',
        ],
    ];

    $isTravelTab = $activeTab === UserRole::User->value;
    $isPimpinanTab = $activeTab === UserRole::Pimpinan->value;
@endphp

@section('content')
    @if($guide = \App\Support\RoleWorkflowGuide::for('users'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Kelola Pengguna</h5>
                        <p class="text-muted mb-0 small">{{ $tabs[$activeTab]['description'] }}</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($isTravelTab)
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    Import Excel
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('travels.import.form') }}">Import User Pusat</a></li>
                                    <li><a class="dropdown-item" href="{{ route('cabang.import.form') }}">Import User Cabang</a></li>
                                </ul>
                            </div>
                        @endif
                        <a href="{{ route('users.create', ['role' => $activeTab]) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus"></i> Tambah {{ $tabs[$activeTab]['label'] }}
                        </a>
                    </div>
                </div>

                <div class="card-body pb-0">
                    <ul class="nav nav-tabs nav-tabs-custom" id="userRoleTabs" role="tablist">
                        @foreach($tabs as $tabKey => $tabMeta)
                            <li class="nav-item" role="presentation">
                                <button type="button"
                                    class="nav-link {{ $activeTab === $tabKey ? 'active' : '' }}"
                                    data-tab="{{ $tabKey }}"
                                    role="tab">
                                    {{ $tabMeta['label'] }}
                                    <span class="badge bg-secondary ms-1">{{ $tabCounts[$tabKey] ?? 0 }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body border-bottom">
                    <div class="row g-3 mb-3">
                        <div class="col-lg-{{ $isTravelTab ? '6' : '8' }} col-md-8">
                            <label class="form-label">Pencarian</label>
                            <input type="text" id="searchInput" class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="{{ $isTravelTab ? 'Nama, email, HP, travel, kabupaten...' : 'Nama, email, HP, kabupaten...' }}">
                            <div class="search-loading mt-1" style="display:none;">
                                <small class="text-muted"><i class="bx bx-loader-alt bx-spin me-1"></i> Mencari...</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 d-flex align-items-end">
                            <button type="button" id="resetBtn" class="btn btn-secondary w-100">
                                <i class="bx bx-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        @unless($isPimpinanTab)
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select id="kabupatenFilter" class="form-select">
                                <option value="">Semua wilayah</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten }}" @selected(request('kabupaten') === $kabupaten)>{{ $kabupaten }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endunless

                        <div class="col-lg-3 col-md-6 travel-only-filter" style="{{ $isTravelTab ? '' : 'display:none;' }}">
                            <label class="form-label">Travel Company</label>
                            <select id="travelCompanyFilter" class="form-select">
                                <option value="">Semua travel</option>
                                @foreach($travelCompanies as $company)
                                    <option value="{{ $company }}" @selected(request('travel_company') === $company)>{{ $company }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Per Halaman</label>
                            <select id="perPageFilter" class="form-select">
                                @foreach([10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div id="resultsInfo" class="text-muted small">
                            <i class="bx bx-info-circle me-1"></i>
                            Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }}
                            dari {{ $users->total() }} data
                        </div>
                        <div id="activeFilters" class="text-muted small" style="display:none;">
                            <i class="bx bx-filter me-1"></i> Filter aktif: <span id="filterBadges"></span>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-0 position-relative">
                    <div id="loadingOverlay" class="text-center py-5" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2 mb-0">Memuat data...</p>
                    </div>

                    <div id="tableContainer">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th style="width:5%">
                                            <a href="#" data-sort="created_at" class="sort-link text-secondary text-decoration-none">No.</a>
                                        </th>
                                        <th style="width:18%">
                                            <a href="#" data-sort="nama" class="sort-link text-secondary text-decoration-none">Nama</a>
                                        </th>
                                        <th style="width:20%">
                                            <a href="#" data-sort="email" class="sort-link text-secondary text-decoration-none">Email</a>
                                        </th>
                                        <th style="width:12%">
                                            <a href="#" data-sort="nomor_hp" class="sort-link text-secondary text-decoration-none">Nomor HP</a>
                                        </th>
                                        @if($isTravelTab)
                                            <th style="width:18%">Travel Company</th>
                                            <th style="width:12%">
                                                <a href="#" data-sort="kabupaten" class="sort-link text-secondary text-decoration-none">Kabupaten</a>
                                            </th>
                                        @else
                                            <th style="width:15%">
                                                @if($isPimpinanTab)
                                                    Cakupan Akses
                                                @else
                                                    <a href="#" data-sort="kabupaten" class="sort-link text-secondary text-decoration-none">Wilayah Kerja</a>
                                                @endif
                                            </th>
                                        @endif
                                        <th style="width:10%" class="text-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @include('admin.users.partials.table-body', compact('users', 'activeTab'))
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationContainer">
                            @include('admin.users.partials.pagination', compact('users'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let activeTab = @json($activeTab);
    let currentSort = { field: 'created_at', order: 'desc' };

    const searchInput = document.getElementById('searchInput');
    const kabupatenFilter = document.getElementById('kabupatenFilter');
    const travelCompanyFilter = document.getElementById('travelCompanyFilter');
    const perPageFilter = document.getElementById('perPageFilter');
    const resetBtn = document.getElementById('resetBtn');
    const tableContainer = document.getElementById('tableContainer');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const resultsInfo = document.getElementById('resultsInfo');
    const activeFilters = document.getElementById('activeFilters');
    const filterBadges = document.getElementById('filterBadges');
    const travelOnlyFilter = document.querySelector('.travel-only-filter');

    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function showLoading() {
        loadingOverlay.style.display = 'block';
        tableContainer.style.opacity = '0.45';
    }

    function hideLoading() {
        loadingOverlay.style.display = 'none';
        tableContainer.style.opacity = '1';
    }

    function updateTravelFilterVisibility() {
        if (!travelOnlyFilter) return;
        travelOnlyFilter.style.display = activeTab === 'user' ? '' : 'none';
        if (activeTab !== 'user') {
            travelCompanyFilter.value = '';
        }
    }

    function updateResultsInfo(info) {
        resultsInfo.innerHTML = `
            <i class="bx bx-info-circle me-1"></i>
            Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data
        `;
    }

    function updateActiveFilters(filters) {
        const badges = [];
        if (filters.search) badges.push(`<span class="badge bg-primary me-1">"${filters.search}"</span>`);
        if (filters.kabupaten) badges.push(`<span class="badge bg-success me-1">${filters.kabupaten}</span>`);
        if (filters.travel_company) badges.push(`<span class="badge bg-info me-1">${filters.travel_company}</span>`);

        if (badges.length) {
            filterBadges.innerHTML = badges.join('');
            activeFilters.style.display = 'block';
        } else {
            activeFilters.style.display = 'none';
        }
    }

    function fetchData(params = {}) {
        showLoading();

        const queryParams = new URLSearchParams();
        queryParams.append('tab', activeTab);
        if (searchInput.value.trim()) queryParams.append('search', searchInput.value.trim());
        if (kabupatenFilter?.value) queryParams.append('kabupaten', kabupatenFilter.value);
        if (activeTab === 'user' && travelCompanyFilter.value) {
            queryParams.append('travel_company', travelCompanyFilter.value);
        }
        if (perPageFilter.value) queryParams.append('per_page', perPageFilter.value);
        queryParams.append('sort_by', currentSort.field);
        queryParams.append('sort_order', currentSort.order);
        if (params.page) queryParams.append('page', params.page);

        const url = `${window.location.pathname}?${queryParams.toString()}`;
        window.history.replaceState({}, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;
                document.getElementById('tableBody').innerHTML = data.tableBody;
                document.getElementById('paginationContainer').innerHTML = data.pagination;
                updateResultsInfo(data.pagination_info);
                updateActiveFilters(data.filters);
            })
            .catch(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Gagal memuat data pengguna.', 'error');
                }
            })
            .finally(hideLoading);
    }

    document.querySelectorAll('#userRoleTabs .nav-link').forEach(tabButton => {
        tabButton.addEventListener('click', function () {
            if (this.classList.contains('active')) return;
            const tab = this.dataset.tab;
            window.location.href = `${window.location.pathname}?tab=${tab}`;
        });
    });

    document.querySelectorAll('.sort-link').forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            const field = this.dataset.sort;
            if (currentSort.field === field) {
                currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = { field, order: 'asc' };
            }
            fetchData();
        });
    });

    document.addEventListener('click', function (event) {
        const pageLink = event.target.closest('#paginationContainer .page-link');
        if (!pageLink || !pageLink.href) return;
        event.preventDefault();
        const page = new URL(pageLink.href).searchParams.get('page');
        fetchData({ page });
    });

    const debouncedSearch = debounce(() => fetchData(), 400);

    searchInput.addEventListener('input', function () {
        document.querySelector('.search-loading').style.display = 'block';
        debouncedSearch();
        setTimeout(() => {
            document.querySelector('.search-loading').style.display = 'none';
        }, 400);
    });

    if (kabupatenFilter) {
        kabupatenFilter.addEventListener('change', () => fetchData());
    }
    travelCompanyFilter.addEventListener('change', () => fetchData());
    perPageFilter.addEventListener('change', () => fetchData());

    resetBtn.addEventListener('click', function () {
        searchInput.value = '';
        if (kabupatenFilter) kabupatenFilter.value = '';
        travelCompanyFilter.value = '';
        perPageFilter.value = '15';
        currentSort = { field: 'created_at', order: 'desc' };
        fetchData();
    });
});

</script>
@endpush

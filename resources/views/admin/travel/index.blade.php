@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data User Travel</h6>
                    <div class="d-flex gap-2">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-upload me-1"></i>
                                Import Excel
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('travels.import.form') }}">
                                    <i class="bx bx-building me-2"></i>Import User Pusat
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('cabang.import.form') }}">
                                    <i class="bx bx-building-house me-2"></i>Import User Cabang
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('travels.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>
                            Tambah User Travel
                        </a>
                    </div>
                </div>
                
                <!-- Search and Filter Form -->
                <div class="card-body">
                    <div class="mb-4">
                        <!-- First Row: Search -->
                        <div class="row g-3 mb-3">
                            <div class="col-lg-8 col-md-8">
                                <div class="form-group">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" id="searchInput" class="form-control" name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Cari nama, email, HP, atau travel company...">
                                    <div class="search-loading" style="display: none;">
                                        <small class="text-muted">
                                            <i class="bx bx-loader-alt bx-spin me-1"></i>
                                            Mencari...
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="button" id="resetBtn" class="btn btn-secondary">
                                        <i class="bx bx-refresh me-1"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Second Row: Filters -->
                        <div class="row g-3 mb-3">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Travel Company</label>
                                    <select id="travelCompanyFilter" class="form-control" name="travel_company">
                                        <option value="">Semua Travel Company</option>
                                        @foreach($travelCompanies as $company)
                                            <option value="{{ $company }}" 
                                                {{ request('travel_company') == $company ? 'selected' : '' }}>
                                                {{ $company }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            @if(auth()->user()->role === 'admin')
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kabupaten</label>
                                    <select id="kabupatenFilter" class="form-control" name="kabupaten">
                                        <option value="">Semua Kabupaten</option>
                                        @foreach($kabupatens as $kabupaten)
                                            <option value="{{ $kabupaten }}" 
                                                {{ request('kabupaten') == $kabupaten ? 'selected' : '' }}>
                                                {{ $kabupaten }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Status Travel</label>
                                    <select id="travelStatusFilter" class="form-control" name="travel_status">
                                        <option value="">Semua Status</option>
                                        @foreach($travelStatuses as $status)
                                            <option value="{{ $status }}" 
                                                {{ request('travel_status') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Per Halaman</label>
                                    <select id="perPageFilter" class="form-control" name="per_page">
                                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                                        <option value="15" {{ request('per_page') == '15' || !request('per_page') ? 'selected' : '' }}>15</option>
                                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Third Row: Results Info -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div id="resultsInfo" class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Menampilkan {{ $travelUsers->firstItem() ?? 0 }} - {{ $travelUsers->lastItem() ?? 0 }} 
                                        dari {{ $travelUsers->total() }} data
                                    </div>
                                    <div id="activeFilters" class="text-muted" style="display: none;">
                                        <i class="bx bx-filter me-1"></i>
                                        Filter aktif: 
                                        <span id="filterBadges"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 pt-0 pb-2">
                    <div id="tableContainer">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 5%">
                                            <a href="#" data-sort="created_at" class="sort-link text-secondary text-decoration-none">
                                                No. 
                                                <i class="bx bx-down-arrow sort-icon" data-sort="created_at"></i>
                                            </a>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 20%">
                                            <a href="#" data-sort="nama" class="sort-link text-secondary text-decoration-none">
                                                Nama
                                                <i class="bx bx-up-arrow sort-icon" data-sort="nama" style="display: none;"></i>
                                            </a>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 20%">
                                            <a href="#" data-sort="email" class="sort-link text-secondary text-decoration-none">
                                                Email
                                                <i class="bx bx-up-arrow sort-icon" data-sort="email" style="display: none;"></i>
                                            </a>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 15%">
                                            <a href="#" data-sort="nomor_hp" class="sort-link text-secondary text-decoration-none">
                                                Nomor HP
                                                <i class="bx bx-up-arrow sort-icon" data-sort="nomor_hp" style="display: none;"></i>
                                            </a>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 15%">Travel Company</th>
                                        @if(auth()->user()->role === 'admin')
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 10%">Kabupaten</th>
                                        @endif
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @forelse ($travelUsers as $user)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $travelUsers->firstItem() + $loop->index }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->nama }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->email }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->nomor_hp }}</td>
                                            <td class="text-sm font-weight-bold">
                                                <span class="badge {{ $user->getTravelCompanyBadgeClass() }}">
                                                    {{ $user->getTravelCompanyName() }}
                                                </span>
                                            </td>
                                            @if(auth()->user()->role === 'admin')
                                            <td class="text-sm font-weight-bold">
                                                <span class="badge bg-success">{{ $user->getKabupaten() }}</span>
                                            </td>
                                            @endif
                                            <td class="text-sm font-weight-bold">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('impersonate.take', $user->id) }}" 
                                                       class="btn btn-success btn-sm waves-effect waves-light"
                                                       onclick="return confirmImpersonate(event, '{{ $user->nama }}')"
                                                       title="Impersonate User">
                                                        <i class="bx bx-user-check me-1"></i>
                                                        Impersonate
                                                    </a>
                                                    <a href="{{ route('users.edit', $user->id) }}" 
                                                       class="btn btn-warning btn-sm waves-effect waves-light"
                                                       title="Edit User">
                                                        <i class="bx bx-edit me-1"></i>
                                                        Edit
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light"
                                                        onclick="confirmDelete({{ $user->id }}, '{{ $user->nama }}')" 
                                                        title="Delete User">
                                                        <i class="bx bx-trash me-1"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->role === 'admin' ? '7' : '6' }}" class="text-center">
                                                <div class="empty-state text-muted">
                                                    <i class="bx bx-search-alt-2"></i>
                                                    <p class="mt-2 mb-0">Tidak ada data ditemukan</p>
                                                    <small>Silakan coba dengan kriteria pencarian yang berbeda</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="paginationContainer">
                            @if($travelUsers->hasPages())
                            <div class="d-flex justify-content-between align-items-center px-3 py-3">
                                <div class="text-muted">
                                    Menampilkan {{ $travelUsers->firstItem() ?? 0 }} - {{ $travelUsers->lastItem() ?? 0 }} 
                                    dari {{ $travelUsers->total() }} data
                                </div>
                                <div>
                                    {{ $travelUsers->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" style="display: none;">
                        <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    /* Custom Pagination Styling to match system theme */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        color: #556ee6;
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        color: #ffffff;
        background-color: #556ee6;
        border-color: #556ee6;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #556ee6;
        border-color: #556ee6;
        color: #ffffff;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #ffffff;
        border-color: #dee2e6;
    }
    
    /* Search and Filter Form Styling */
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        border-color: #556ee6;
        box-shadow: 0 0 0 0.2rem rgba(85, 110, 230, 0.25);
    }
    
    /* Table Header Links Styling */
    .table th a {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .table th a:hover {
        color: #556ee6;
    }
    
    /* Badge Styling */
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    /* Results Info Styling */
    .text-muted {
        font-size: 0.875rem;
    }
    
    /* Empty State Styling */
    .empty-state {
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    /* Loading Overlay */
    #loadingOverlay {
        position: relative;
        background-color: rgba(255, 255, 255, 0.9);
        z-index: 1000;
    }
    
    /* Search Loading */
    .search-loading {
        margin-top: 0.5rem;
    }
    
    /* Smooth transitions */
    .table-container {
        transition: opacity 0.3s ease;
    }
    
    .table-container.loading {
        opacity: 0.6;
    }
</style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Real-time search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            let currentSort = {
                field: '{{ request("sort_by", "created_at") }}',
                order: '{{ request("sort_order", "desc") }}'
            };

            // Get filter elements
            const searchInput = document.getElementById('searchInput');
            const travelCompanyFilter = document.getElementById('travelCompanyFilter');
            const kabupatenFilter = document.getElementById('kabupatenFilter');
            const travelStatusFilter = document.getElementById('travelStatusFilter');
            const perPageFilter = document.getElementById('perPageFilter');
            const resetBtn = document.getElementById('resetBtn');
            const tableContainer = document.getElementById('tableContainer');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const resultsInfo = document.getElementById('resultsInfo');
            const activeFilters = document.getElementById('activeFilters');
            const filterBadges = document.getElementById('filterBadges');

            // Debounced search function
            function debounceSearch(func, delay) {
                let timeoutId;
                return function (...args) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => func.apply(this, args), delay);
                };
            }

            // Show loading state
            function showLoading() {
                tableContainer.style.display = 'none';
                loadingOverlay.style.display = 'block';
            }

            // Hide loading state
            function hideLoading() {
                loadingOverlay.style.display = 'none';
                tableContainer.style.display = 'block';
            }

            // Update results info
            function updateResultsInfo(data) {
                const info = data.pagination_info;
                resultsInfo.innerHTML = `
                    <i class="bx bx-info-circle me-1"></i>
                    Menampilkan ${info.from || 0} - ${info.to || 0} 
                    dari ${info.total} data
                `;
            }

            // Update active filters
            function updateActiveFilters(params) {
                const badges = [];
                
                if (params.search) {
                    badges.push(`<span class="badge bg-primary me-1">Pencarian: "${params.search}"</span>`);
                }
                if (params.travel_company) {
                    badges.push(`<span class="badge bg-info me-1">Travel: ${params.travel_company}</span>`);
                }
                if (params.kabupaten) {
                    badges.push(`<span class="badge bg-success me-1">Kabupaten: ${params.kabupaten}</span>`);
                }
                if (params.travel_status) {
                    badges.push(`<span class="badge bg-warning me-1">Status: ${params.travel_status}</span>`);
                }

                if (badges.length > 0) {
                    filterBadges.innerHTML = badges.join('');
                    activeFilters.style.display = 'block';
                } else {
                    activeFilters.style.display = 'none';
                }
            }

            // Fetch data via AJAX
            function fetchData(params = {}) {
                showLoading();
                
                // Build query string
                const queryParams = new URLSearchParams();
                
                // Add current filter values
                if (searchInput.value.trim()) queryParams.append('search', searchInput.value.trim());
                if (travelCompanyFilter.value) queryParams.append('travel_company', travelCompanyFilter.value);
                if (kabupatenFilter && kabupatenFilter.value) queryParams.append('kabupaten', kabupatenFilter.value);
                if (travelStatusFilter.value) queryParams.append('travel_status', travelStatusFilter.value);
                if (perPageFilter.value) queryParams.append('per_page', perPageFilter.value);
                
                // Add sorting
                queryParams.append('sort_by', currentSort.field);
                queryParams.append('sort_order', currentSort.order);
                
                // Add page if specified
                if (params.page) queryParams.append('page', params.page);

                fetch(`{{ route('travels.index') }}?${queryParams.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update table content
                        document.getElementById('tableBody').innerHTML = data.tableBody;
                        document.getElementById('paginationContainer').innerHTML = data.pagination;
                        updateResultsInfo(data);
                        updateActiveFilters(data.filters);
                        
                        // Update sort indicators
                        updateSortIndicators();
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Terjadi kesalahan saat memuat data",
                        icon: "error",
                        confirmButtonColor: "#f46a6a"
                    });
                })
                .finally(() => {
                    hideLoading();
                });
            }

            // Update sort indicators
            function updateSortIndicators() {
                document.querySelectorAll('.sort-icon').forEach(icon => {
                    const field = icon.dataset.sort;
                    icon.style.display = 'none';
                    
                    if (field === currentSort.field) {
                        icon.style.display = 'inline';
                        icon.className = `bx bx-${currentSort.order === 'asc' ? 'up' : 'down'}-arrow sort-icon`;
                    }
                });
            }

            // Handle sorting
            document.querySelectorAll('.sort-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const field = this.dataset.sort;
                    
                    if (currentSort.field === field) {
                        currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.field = field;
                        currentSort.order = 'asc';
                    }
                    
                    fetchData();
                });
            });

            // Handle pagination
            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination .page-link')) {
                    e.preventDefault();
                    const href = e.target.closest('.page-link').href;
                    const url = new URL(href);
                    const page = url.searchParams.get('page');
                    fetchData({ page: page });
                }
            });

            // Debounced search
            const debouncedSearch = debounceSearch(function() {
                fetchData();
            }, 500);

            // Event listeners
            searchInput.addEventListener('input', function() {
                const loadingDiv = document.querySelector('.search-loading');
                loadingDiv.style.display = 'block';
                debouncedSearch();
                setTimeout(() => {
                    loadingDiv.style.display = 'none';
                }, 500);
            });

            travelCompanyFilter.addEventListener('change', fetchData);
            if (kabupatenFilter) kabupatenFilter.addEventListener('change', fetchData);
            travelStatusFilter.addEventListener('change', fetchData);
            perPageFilter.addEventListener('change', fetchData);

            // Reset button
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                travelCompanyFilter.value = '';
                if (kabupatenFilter) kabupatenFilter.value = '';
                travelStatusFilter.value = '';
                perPageFilter.value = '15';
                currentSort = { field: 'created_at', order: 'desc' };
                fetchData();
            });

            // Initialize sort indicators
            updateSortIndicators();
        });

        // Existing functions for modals and confirmations
        function confirmImpersonate(event, username) {
            event.preventDefault();
            
            Swal.fire({
                title: "Impersonate User?",
                text: `Anda akan masuk sebagai ${username}. Anda dapat melihat sistem dari perspektif user ini.`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Ya, impersonate!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = event.target.href;
                }
            });
            
            return false;
        }

        function confirmDelete(userId, username) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: `User ${username} akan dihapus secara permanen!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f46a6a",
                cancelButtonColor: "#34c38f",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonColor: "#34c38f"
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                title: "Peringatan!",
                text: "{{ session('warning') }}",
                icon: "warning",
                confirmButtonColor: "#f7b731"
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonColor: "#f46a6a"
            });
        @endif

        @if(session('import_errors') && count(session('import_errors')) > 0)
            // Show detailed errors in a collapsible section
            setTimeout(function() {
                const errorDetails = `
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#errorDetails" aria-expanded="false">
                            <i class="bx bx-chevron-down me-1"></i> Lihat Detail Error
                        </button>
                        <div class="collapse mt-2" id="errorDetails">
                            <div class="card card-body bg-light" style="max-height: 300px; overflow-y: auto;">
                                @foreach(session('import_errors') as $error)
                                    <small class="text-danger">• {{ $error }}</small><br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                `;
                
                // Add error details to the page
                const alertContainer = document.querySelector('.alert, .swal2-container');
                if (alertContainer) {
                    alertContainer.insertAdjacentHTML('afterend', errorDetails);
                }
            }, 1000);
        @endif
    </script>
@endpush

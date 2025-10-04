@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data User Kabupaten</h6>
                    <a href="{{ route('kabupaten.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>
                        Tambah User Kabupaten
                    </a>
                </div>
                
                <!-- Search and Filter Form -->
                <div class="card-body">
                    <div class="mb-4">
                        <!-- Search Only -->
                        <div class="row g-3 mb-3">
                            <div class="col-lg-8 col-md-8">
                                <div class="form-group">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" id="searchInput" class="form-control" name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Cari nama, email, atau nomor HP...">
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
                        
                        <!-- Second Row: Results Info -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div id="resultsInfo" class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Total {{ $kabupatenUsers->count() }} user kabupaten
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
                                            style="width: 15%">
                                            <a href="#" data-sort="kabupaten" class="sort-link text-secondary text-decoration-none">
                                                Kabupaten/Kota
                                                <i class="bx bx-up-arrow sort-icon" data-sort="kabupaten" style="display: none;"></i>
                                            </a>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            style="width: 25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @forelse ($kabupatenUsers as $user)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->nama }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->email }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->nomor_hp }}</td>
                                            <td class="text-sm font-weight-bold">
                                                <span class="badge bg-success">{{ $user->kabupaten }}</span>
                                            </td>
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
                                            <td colspan="6" class="text-center">
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
                        
                        <!-- Results Info Only -->
                        <div id="paginationContainer" class="px-3 py-3">
                            <div class="text-muted text-center">
                                Total {{ $kabupatenUsers->count() }} user kabupaten
                            </div>
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
            let currentSort = {
                field: '{{ request("sort_by", "created_at") }}',
                order: '{{ request("sort_order", "desc") }}'
            };

            // Get filter elements
            const searchInput = document.getElementById('searchInput');
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
                    Total ${info.total} user kabupaten
                `;
            }

            // Update active filters
            function updateActiveFilters(params) {
                const badges = [];
                
                if (params.search) {
                    badges.push(`<span class="badge bg-primary me-1">Pencarian: "${params.search}"</span>`);
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
                
                // Add sorting
                queryParams.append('sort_by', currentSort.field);
                queryParams.append('sort_order', currentSort.order);
                
                // No pagination needed

                fetch(`{{ route('kabupaten.index') }}?${queryParams.toString()}`, {
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

            // No pagination needed for kabupaten users

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

            // Reset button
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
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

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonColor: "#f46a6a"
            });
        @endif
    </script>
@endpush

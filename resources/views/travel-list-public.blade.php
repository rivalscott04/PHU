<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar Travel Berizin | {{ config('app.name') }}</title>
    <meta name="description" content="Daftar lengkap perusahaan travel berizin di wilayah NTB" />
    <meta name="keywords" content="travel berizin, PPIU, PIHK, NTB, haji, umrah" />

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Roboto:wght@400;500;600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS File -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/public-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/travel-list-public.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/interactive-cursor.css') }}" rel="stylesheet" />
    @include('partials.public-trust-styles')
</head>

<body>
    <div class="travel-list-page">
    <div class="container">
        <header class="travel-page-head" data-aos="fade-up">
            <a href="{{ url('/') }}" class="back-btn back-btn--inline">
                <i class="fas fa-arrow-left me-1"></i>Beranda
            </a>
            <div class="travel-page-head__content">
                <p class="travel-page-head__eyebrow">Direktori Publik</p>
                <h1>Daftar Travel Berizin</h1>
                <p class="travel-page-head__subtitle">Perusahaan travel terdaftar di Kanwil Kementerian Haji dan Umroh NTB</p>
            </div>
        </header>

        <div class="trust-intro trust-intro--collapsible" data-aos="fade-up" data-aos-delay="50">
            <button class="trust-intro-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trustIntroBody" aria-expanded="false" aria-controls="trustIntroBody">
                <span><i class="fas fa-handshake me-2"></i>Apa itu Indeks Kepercayaan?</span>
                <i class="fas fa-chevron-down trust-intro-toggle__chevron"></i>
            </button>
            <div class="collapse" id="trustIntroBody">
                <div class="trust-intro__inner">
                    <p class="trust-intro__lead mb-2">
                        Kanwil Kemenag NTB menampilkan <strong>indeks kepercayaan</strong> berdasarkan data
                        pengawasan di sistem PANTAU — bukan sertifikat resmi, melainkan alat bantu
                        membandingkan travel sebelum Anda memilih. Semakin banyak bintang, semakin baik catatannya.
                    </p>
                    <ul class="trust-intro__steps">
                        <li>
                            <i class="fas fa-circle-check"></i>
                            <span>Lihat <strong>badge bintang</strong> di setiap kartu travel di bawah.</span>
                        </li>
                        <li>
                            <i class="fas fa-circle-check"></i>
                            <span>Klik <strong>Lihat Detail Kepercayaan</strong> untuk penjelasan lengkap per travel.</span>
                        </li>
                        <li>
                            <i class="fas fa-circle-check"></i>
                            <span>Gunakan urutan <strong>Rating Terbaik Dulu</strong> untuk melihat yang paling dipercaya.</span>
                        </li>
                    </ul>
                    <div class="trust-legend" aria-label="Panduan bintang kepercayaan">
                        <span class="trust-legend__item"><span class="trust-legend__stars">★★★★★</span> Sangat Dipercaya</span>
                        <span class="trust-legend__item"><span class="trust-legend__stars">★★★★☆</span> Dipercaya</span>
                        <span class="trust-legend__item"><span class="trust-legend__stars">★★★☆☆</span> Perlu Dicek</span>
                        <span class="trust-legend__item"><span class="trust-legend__stars">★★☆☆☆</span> Kurang Dipercaya</span>
                    </div>
                    <p class="trust-intro__note text-muted small mb-0 mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Indeks ini <strong>bukan jaminan</strong> kualitas layanan. Tetap periksa izin travel,
                        kontrak, dan reputasinya sendiri sebelum memutuskan.
                    </p>
                </div>
            </div>
        </div>

        <div class="travel-stats-bar" data-aos="fade-up" data-aos-delay="75">
            <span class="travel-stats-bar__item"><strong>{{ $stats['total'] }}</strong> Total</span>
            <span class="travel-stats-bar__sep">·</span>
            <span class="travel-stats-bar__item"><strong>{{ $stats['ppiu'] }}</strong> PPIU</span>
            <span class="travel-stats-bar__sep">·</span>
            <span class="travel-stats-bar__item"><strong>{{ $stats['pihk'] }}</strong> PIHK</span>
            <span class="travel-stats-bar__sep">·</span>
            <span class="travel-stats-bar__item"><strong>{{ $stats['kabupaten'] }}</strong> Kabupaten</span>
            @if(($stats['with_trust_data'] ?? 0) > 0)
            <span class="travel-stats-bar__sep">·</span>
            <span class="travel-stats-bar__item"><strong>{{ $stats['with_trust_data'] }}</strong> Sudah Dinilai</span>
            @endif
        </div>

            <!-- Filter Section -->
            <div class="filter-section" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-2">
                    <div class="col-md-3 col-6">
                        <label for="statusFilter" class="form-label">Jenis Layanan</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="PPIU">PPIU (Umrah)</option>
                            <option value="PIHK">PIHK (Haji & Umrah)</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <label for="jenisFilter" class="form-label">Kantor</label>
                        <select class="form-select" id="jenisFilter">
                            <option value="">Semua Jenis</option>
                            <option value="pusat">Pusat</option>
                            <option value="cabang">Cabang</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <label for="kabupatenFilter" class="form-label">Wilayah</label>
                        <select class="form-select" id="kabupatenFilter">
                            <option value="">Semua Kabupaten</option>
                            @foreach($allKabupatens as $kabupaten)
                            <option value="{{ $kabupaten }}">{{ $kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <label for="searchInput" class="form-label">Cari Travel</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Nama travel...">
                    </div>
                </div>

                <div class="filter-toolbar">
                    <div class="filter-toolbar__sort">
                        <label for="sortSelect" class="filter-toolbar__label mb-0">Urutkan</label>
                        <select class="form-select" id="sortSelect">
                            <option value="trust-desc">Rating Terbaik Dulu</option>
                            <option value="name-asc">Nama A-Z</option>
                            <option value="name-desc">Nama Z-A</option>
                            <option value="kabupaten-asc">Kabupaten A-Z</option>
                            <option value="kabupaten-desc">Kabupaten Z-A</option>
                            <option value="tanggal-desc">Tanggal SK Terbaru</option>
                            <option value="tanggal-asc">Tanggal SK Terlama</option>
                        </select>
                    </div>
                    <div class="filter-toolbar__actions">
                        <span class="filter-toolbar__label">Tampilan</span>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="viewToggle" id="gridView" value="grid" checked>
                            <label class="btn btn-outline-secondary" for="gridView" title="Grid">
                                <i class="fas fa-th"></i>
                            </label>
                            <input type="radio" class="btn-check" name="viewToggle" id="listView" value="list">
                            <label class="btn btn-outline-secondary" for="listView" title="List">
                                <i class="fas fa-list"></i>
                            </label>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="exportBtn">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                            <i class="fas fa-times me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data travel...</p>
            </div>

            <!-- Travel Cards -->
            <div class="row" id="travelCards">
                @foreach($data as $travel)
                @php
                    $isTravelPusat = isset($travel->Status);
                    $isTravelCabang = isset($travel->pimpinan_cabang);
                    $trust = $travel->trust ?? ['has_data' => false, 'score' => -1];
                    $profileUrl = $isTravelPusat
                        ? route('travel.public.show', $travel->public_uuid)
                        : ($travel->parent_public_uuid ? route('travel.public.show', $travel->parent_public_uuid) : null);
                @endphp
                <div class="col-lg-6 col-xl-4 mb-4 travel-item" 
                     data-status="{{ $isTravelPusat ? $travel->Status : '' }}" 
                     data-jenis="{{ $isTravelPusat ? 'pusat' : 'cabang' }}"
                     data-kabupaten="{{ $isTravelPusat ? $travel->kab_kota : $travel->kabupaten }}"
                     data-name="{{ strtolower($travel->Penyelenggara) }}"
                     data-trust="{{ $trust['has_data'] ? $trust['score'] : -1 }}"
                     data-tanggal="{{ $isTravelPusat ? ($travel->Tanggal ? $travel->Tanggal->format('Y-m-d') : '') : ($travel->tanggal ? $travel->tanggal->format('Y-m-d') : '') }}">
                    <div class="card travel-card h-100">
                        <div class="travel-header">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <h5 class="card-title mb-0">{{ $travel->Penyelenggara }}</h5>
                                @if($isTravelPusat)
                                <span class="status-badge {{ $travel->Status === 'PPIU' ? 'status-ppiu' : 'status-pihk' }}">
                                    {{ $travel->Status }}
                                </span>
                                @else
                                <span class="status-badge status-cabang">
                                    CABANG
                                </span>
                                @endif
                            </div>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $isTravelPusat ? $travel->kab_kota : $travel->kabupaten }}
                            </p>
                            <div class="trust-card-row">
                                <div class="trust-card-label">Tingkat Kepercayaan</div>
                                @include('partials.public-trust-badge', ['trust' => $trust, 'compact' => true])
                            </div>
                            @if($isTravelCabang && $travel->pusat)
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-link me-1"></i>Cabang dari <strong>{{ $travel->pusat }}</strong>
                                </p>
                            @endif
                        </div>
                        <div class="travel-body">
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <span><strong>Telepon:</strong> {{ $travel->telepon ?? $travel->Telepon ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span><strong>Pimpinan:</strong> {{ $isTravelPusat ? $travel->Pimpinan : $travel->pimpinan_cabang }}</span>
                            </div>
                            @if($isTravelPusat && $travel->nilai_akreditasi)
                            <div class="info-item">
                                <i class="fas fa-certificate"></i>
                                <span><strong>Akreditasi:</strong> {{ $travel->nilai_akreditasi }}</span>
                            </div>
                            @endif
                            @if($isTravelCabang && $travel->SK_BA)
                            <div class="info-item">
                                <i class="fas fa-file-alt"></i>
                                <span><strong>SK/BA:</strong> {{ $travel->SK_BA }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Tanggal SK:</strong> {{ $isTravelPusat ? ($travel->Tanggal ? $travel->Tanggal->format('d/m/Y') : '-') : ($travel->tanggal ? $travel->tanggal->format('d/m/Y') : '-') }}</span>
                            </div>
                        </div>
                        @if($profileUrl)
                            <div class="travel-card__footer">
                                <a href="{{ $profileUrl }}" class="btn btn-trust-profile">
                                    <i class="fas fa-shield-halved me-2"></i>Lihat Detail Kepercayaan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div class="no-results" id="noResults" style="display: none;">
                <i class="fas fa-search"></i>
                <h4>Tidak ada hasil ditemukan</h4>
                <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
            </div>

            @if(empty($data))
            <div class="no-results">
                <i class="fas fa-building"></i>
                <h4>Belum ada data travel</h4>
                <p>Data travel berizin akan ditampilkan di sini.</p>
            </div>
            @endif

            <!-- Pagination (client-side, no full page reload) -->
            <div id="paginationContainer" class="pagination-wrapper" style="display: none;"></div>
    </div>
    </div>

    @include('partials.kanwil-contact', ['variant' => 'footer-compact'])

    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Enhanced Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const PER_PAGE = 6;
            const statusFilter = document.getElementById('statusFilter');
            const jenisFilter = document.getElementById('jenisFilter');
            const kabupatenFilter = document.getElementById('kabupatenFilter');
            const searchInput = document.getElementById('searchInput');
            const sortSelect = document.getElementById('sortSelect');
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const exportBtn = document.getElementById('exportBtn');
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            const travelItems = document.querySelectorAll('.travel-item');
            const noResults = document.getElementById('noResults');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const travelCards = document.getElementById('travelCards');
            const paginationContainer = document.getElementById('paginationContainer');

            let filterTimeout;
            let currentPage = Math.max(1, parseInt(new URLSearchParams(window.location.search).get('page') || '1', 10));

            function showLoading() {
                loadingSpinner.style.display = 'block';
                travelCards.style.opacity = '0.5';
            }

            function hideLoading() {
                loadingSpinner.style.display = 'none';
                travelCards.style.opacity = '1';
            }

            function itemMatchesFilters(item) {
                const statusValue = statusFilter.value;
                const jenisValue = jenisFilter.value;
                const kabupatenValue = kabupatenFilter.value;
                const searchValue = searchInput.value.toLowerCase().trim();

                const status = item.dataset.status;
                const jenis = item.dataset.jenis;
                const kabupaten = item.dataset.kabupaten;
                const name = item.dataset.name;

                const statusMatch = !statusValue || status === statusValue;
                const jenisMatch = !jenisValue || jenis === jenisValue;
                const kabupatenMatch = !kabupatenValue || kabupaten === kabupatenValue;
                const searchMatch = !searchValue || name.includes(searchValue);

                return statusMatch && jenisMatch && kabupatenMatch && searchMatch;
            }

            function getFilteredItems() {
                return Array.from(travelItems).filter(itemMatchesFilters);
            }

            function syncPageUrl(page) {
                const url = new URL(window.location.href);
                if (page <= 1) {
                    url.searchParams.delete('page');
                } else {
                    url.searchParams.set('page', String(page));
                }
                window.history.replaceState({ page }, '', url);
            }

            function renderPagination(totalItems, totalPages) {
                if (totalItems === 0 || totalPages <= 1) {
                    paginationContainer.style.display = 'none';
                    paginationContainer.innerHTML = '';
                    return;
                }

                const firstItem = (currentPage - 1) * PER_PAGE + 1;
                const lastItem = Math.min(currentPage * PER_PAGE, totalItems);

                let paginationHtml = '<ul class="pagination mb-0">';

                paginationHtml += currentPage > 1
                    ? `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>`
                    : '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';

                for (let page = 1; page <= totalPages; page++) {
                    if (page === 1 || page === totalPages || Math.abs(page - currentPage) <= 1) {
                        paginationHtml += page === currentPage
                            ? `<li class="page-item active"><span class="page-link">${page}</span></li>`
                            : `<li class="page-item"><a class="page-link" href="#" data-page="${page}">${page}</a></li>`;
                    } else if (page === currentPage - 2 || page === currentPage + 2) {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                paginationHtml += currentPage < totalPages
                    ? `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>`
                    : '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';

                paginationHtml += '</ul>';

                paginationContainer.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center px-3 py-3 bg-white rounded">
                        <div class="text-muted">
                            Menampilkan ${firstItem} sampai ${lastItem} dari ${totalItems} data
                        </div>
                        <div>${paginationHtml}</div>
                    </div>
                `;
                paginationContainer.style.display = 'block';
            }

            function applyPagination(options = {}) {
                const { scroll = false, resetPage = false } = options;
                const filteredItems = getFilteredItems();
                const totalItems = filteredItems.length;
                const totalPages = Math.max(1, Math.ceil(totalItems / PER_PAGE));

                if (resetPage) {
                    currentPage = 1;
                } else if (currentPage > totalPages) {
                    currentPage = totalPages;
                }

                travelItems.forEach(item => {
                    item.style.display = 'none';
                    item.style.animation = '';
                });

                if (totalItems === 0) {
                    noResults.style.display = 'block';
                    noResults.style.animation = 'fadeIn 0.3s ease';
                    renderPagination(0, 0);
                    syncPageUrl(1);
                    return;
                }

                noResults.style.display = 'none';

                const startIndex = (currentPage - 1) * PER_PAGE;
                filteredItems.slice(startIndex, startIndex + PER_PAGE).forEach((item, index) => {
                    item.style.display = 'block';
                    item.style.animation = 'slideInUp 0.4s ease forwards';
                    item.style.animationDelay = `${index * 0.05}s`;
                });

                renderPagination(totalItems, totalPages);
                syncPageUrl(currentPage);

                if (scroll) {
                    travelCards.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function goToPage(page, scroll = true) {
                currentPage = page;
                applyPagination({ scroll });
            }

            function filterTravel() {
                showLoading();

                if (filterTimeout) {
                    clearTimeout(filterTimeout);
                }

                filterTimeout = setTimeout(() => {
                    applyPagination({ resetPage: true });
                    hideLoading();
                }, 300);
            }

            // Sorting functionality
            function sortTravelItems() {
                const sortValue = sortSelect.value;
                const [field, direction] = sortValue.split('-');
                
                const itemsArray = Array.from(travelItems);
                
                itemsArray.sort((a, b) => {
                    let aValue, bValue;
                    
                    switch(field) {
                        case 'trust':
                            aValue = parseInt(a.dataset.trust, 10);
                            bValue = parseInt(b.dataset.trust, 10);
                            if (direction === 'desc') {
                                return bValue - aValue;
                            }
                            return aValue - bValue;
                        case 'name':
                            aValue = a.dataset.name;
                            bValue = b.dataset.name;
                            break;
                        case 'kabupaten':
                            aValue = a.dataset.kabupaten;
                            bValue = b.dataset.kabupaten;
                            break;
                        case 'tanggal':
                            aValue = a.dataset.tanggal || '9999-12-31';
                            bValue = b.dataset.tanggal || '9999-12-31';
                            break;
                        default:
                            return 0;
                    }
                    
                    if (direction === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                });
                
                // Reorder DOM elements
                const container = travelCards;
                itemsArray.forEach(item => {
                    container.appendChild(item);
                });

                applyPagination();
            }
            
            // View toggle functionality
            function toggleView() {
                if (gridView.checked) {
                    travelCards.classList.remove('list-view');
                } else {
                    travelCards.classList.add('list-view');
                }
            }
            
            // Export functionality
            function exportData(format) {
                const visibleItems = getFilteredItems();
                
                let data = visibleItems.map(item => {
                    const card = item.querySelector('.travel-card');
                    const title = card.querySelector('.card-title').textContent;
                    const status = item.dataset.status || 'CABANG';
                    const jenis = item.dataset.jenis;
                    const kabupaten = item.dataset.kabupaten;
                    const pimpinan = card.querySelector('.info-item span')?.textContent.replace('Pimpinan: ', '') || '';
                    const telepon = Array.from(card.querySelectorAll('.info-item span')).find(span => 
                        span.textContent.includes('Telepon:')
                    )?.textContent.replace('Telepon: ', '') || '';
                    
                    return {
                        nama: title,
                        status: status,
                        jenis: jenis,
                        kabupaten: kabupaten,
                        pimpinan: pimpinan,
                        telepon: telepon
                    };
                });
                
                if (format === 'csv') {
                    exportToCSV(data);
                } else if (format === 'json') {
                    exportToJSON(data);
                }
            }
            
            function exportToCSV(data) {
                const headers = ['Nama', 'Status', 'Jenis', 'Kabupaten', 'Pimpinan', 'Telepon'];
                const csvContent = [
                    headers.join(','),
                    ...data.map(row => [
                        `"${row.nama}"`,
                        `"${row.status}"`,
                        `"${row.jenis}"`,
                        `"${row.kabupaten}"`,
                        `"${row.pimpinan}"`,
                        `"${row.telepon}"`
                    ].join(','))
                ].join('\n');
                
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'travel-list.csv';
                link.click();
            }
            
            function exportToJSON(data) {
                const jsonContent = JSON.stringify(data, null, 2);
                const blob = new Blob([jsonContent], { type: 'application/json;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'travel-list.json';
                link.click();
            }

            // Urutkan default: kepercayaan tertinggi
            sortTravelItems();
            applyPagination();

            paginationContainer.addEventListener('click', function(e) {
                const link = e.target.closest('[data-page]');
                if (!link) {
                    return;
                }

                e.preventDefault();
                goToPage(parseInt(link.dataset.page, 10));
            });

            window.addEventListener('popstate', function() {
                currentPage = Math.max(1, parseInt(new URLSearchParams(window.location.search).get('page') || '1', 10));
                applyPagination();
            });

            // Add event listeners with debouncing
            statusFilter.addEventListener('change', filterTravel);
            jenisFilter.addEventListener('change', filterTravel);
            kabupatenFilter.addEventListener('change', filterTravel);
            searchInput.addEventListener('input', filterTravel);
            sortSelect.addEventListener('change', sortTravelItems);
            gridView.addEventListener('change', toggleView);
            listView.addEventListener('change', toggleView);
            
            // Export dropdown
            exportBtn.addEventListener('click', function() {
                const dropdown = document.createElement('div');
                dropdown.className = 'dropdown-menu show';
                dropdown.style.position = 'absolute';
                dropdown.style.top = '100%';
                dropdown.style.right = '0';
                dropdown.innerHTML = `
                    <a class="dropdown-item" href="#" data-format="csv">
                        <i class="fas fa-file-csv me-2"></i>Export CSV
                    </a>
                    <a class="dropdown-item" href="#" data-format="json">
                        <i class="fas fa-file-code me-2"></i>Export JSON
                    </a>
                `;
                
                exportBtn.style.position = 'relative';
                exportBtn.appendChild(dropdown);
                
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    const format = e.target.closest('[data-format]')?.dataset.format;
                    if (format) {
                        exportData(format);
                    }
                    dropdown.remove();
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function closeDropdown(e) {
                    if (!exportBtn.contains(e.target)) {
                        dropdown.remove();
                        document.removeEventListener('click', closeDropdown);
                    }
                });
            });

            // Add clear filters functionality
            const clearFilters = () => {
                statusFilter.value = '';
                jenisFilter.value = '';
                kabupatenFilter.value = '';
                searchInput.value = '';
                sortSelect.value = 'trust-desc';
                sortTravelItems();
                gridView.checked = true;
                toggleView();
                filterTravel();
            };

            clearFiltersBtn.addEventListener('click', clearFilters);
        });
    </script>
</body>

</html>

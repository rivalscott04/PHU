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
    <link href="{{ asset('css/interactive-cursor.css') }}" rel="stylesheet" />

    <style>
        .travel-card {
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid var(--phu-border, #CCCCCC);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--phu-card-shadow);
            background: var(--phu-surface, #FFFFFF);
            position: relative;
        }

        .travel-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .travel-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--phu-hover-shadow);
        }

        .travel-card:hover::before {
            opacity: 1;
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 0.4rem 0.8rem;
            border-radius: 25px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-ppiu {
            background: #e2a712;
            color: white;
        }

        .status-pihk {
            background: #555555;
            color: white;
        }

        .status-cabang {
            background: #888888;
            color: white;
        }

        .travel-header {
            padding: 1.8rem;
            position: relative;
        }

        .travel-body {
            padding: 1.8rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
        }

        .info-item i {
            width: 24px;
            color: var(--phu-accent, #e2a712);
            margin-right: 1rem;
            margin-top: 2px;
            font-size: 0.9rem;
        }

        .info-item span {
            color: var(--phu-text, #333333);
            line-height: 1.6;
            font-weight: 500;
        }

        .info-item strong {
            color: var(--phu-text, #333333);
            font-weight: 700;
        }

        .back-btn {
            position: fixed;
            top: 25px;
            left: 25px;
            z-index: 1000;
            border-radius: 50px;
            padding: 0.8em 1.5em;
            font-size: clamp(14px, 2vw, 16px);
            box-shadow: var(--phu-card-shadow);
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
        }

        .back-btn:hover {
            transform: translateX(-5px);
            box-shadow: 0 8px 20px rgba(226, 167, 18, 0.2);
        }

        .filter-section {
            border-radius: 20px;
            padding: min(4vw, 2.5rem);
            margin-bottom: 2rem;
        }

        .filter-section .form-label {
            font-weight: 600;
            color: var(--phu-text, #333333);
            margin-bottom: 0.8rem;
        }

        .filter-section .form-select,
        .filter-section .form-control {
            border: 2px solid var(--phu-border, #CCCCCC);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            font-size: clamp(14px, 2vw, 16px);
            background: var(--phu-surface, #FFFFFF);
        }

        .filter-section .form-select:focus,
        .filter-section .form-control:focus {
            border-color: var(--phu-accent, #e2a712);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 68, 0.2);
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: clamp(22px, 3vw, 28px);
            font-weight: 800;
            color: var(--phu-text, #333333);
            margin-bottom: 1rem;
            line-height: 1.2;
            word-wrap: break-word;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title p {
            font-size: clamp(14px, 2vw, 16px);
            color: var(--phu-text-muted, #5a5a5a);
            font-weight: 500;
            max-width: 600px;
            margin: 0 auto;
        }

        .stats-summary {
            border-radius: 20px;
            padding: min(4vw, 2.5rem);
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stats-summary h3 {
            font-size: clamp(18px, 2.5vw, 22px);
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            color: var(--phu-text, #333333);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 15px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .stat-item:hover {
            background: #ffffff;
            border-color: var(--secondary-color);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 800;
            display: block;
            margin-bottom: 0.5rem;
            color: var(--phu-accent, #e2a712);
        }

        .stat-label {
            font-size: clamp(12px, 2vw, 14px);
            color: var(--phu-text-muted, #5a5a5a);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Pagination handled by public-theme.css */

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            color: var(--secondary-color);
        }

        .no-results {
            text-align: center;
            padding: min(5vw, 4rem) min(4vw, 2rem);
        }

        .no-results i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .no-results h4 {
            color: var(--phu-text, #333333);
            margin-bottom: 1rem;
        }

        .no-results p {
            color: var(--phu-text-muted, #5a5a5a);
        }

        /* List View Styles */
        .list-view .travel-item {
            width: 100% !important;
            max-width: none !important;
        }
        
        .list-view .travel-card {
            display: flex;
            flex-direction: row;
            height: auto;
        }
        
        .list-view .travel-header {
            flex: 0 0 300px;
            border-right: 1px solid #e9ecef;
            border-bottom: none;
        }
        
        .list-view .travel-body {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .list-view .info-item {
            flex: 0 0 50%;
            margin-bottom: 0.5rem;
        }
        
        /* View Toggle Styles */
        .btn-check:checked + .btn-outline-secondary {
            background-color: var(--phu-accent, #e2a712);
            border-color: var(--phu-accent, #e2a712);
            color: white;
        }
        
        .btn-outline-secondary {
            border-radius: 50px;
            padding: 0.5rem 1rem;
            border: 2px solid var(--phu-border, #CCCCCC);
            font-weight: 600;
            color: var(--phu-text-muted, #5a5a5a);
            transition: all 0.3s ease;
            background: var(--phu-surface, #FFFFFF);
        }

        .btn-outline-secondary:hover {
            background-color: var(--phu-gold, #c8940e);
            border-color: var(--phu-gold, #c8940e);
            color: var(--phu-text, #333333);
        }
        
        .btn-outline-primary {
            border-radius: 50px;
            padding: 0.8em 1.5em;
            border: 2px solid var(--phu-cta, #e2a712);
            color: var(--phu-cta, #e2a712);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: clamp(14px, 2vw, 16px);
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background-color: var(--phu-cta, #e2a712);
            border-color: var(--phu-cta, #e2a712);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 68, 136, 0.25);
            transform: translateY(-2px);
        }
        
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: var(--phu-card-shadow);
            border: 1px solid var(--phu-border, #CCCCCC);
            background: var(--phu-surface, #FFFFFF);
        }
        
        .dropdown-item:hover {
            background-color: var(--phu-accent, #e2a712);
            color: white;
        }

        .pagination-wrapper {
            margin-top: 2rem;
        }

        .pagination-wrapper .bg-white {
            background: var(--phu-surface, #FFFFFF) !important;
            border: 1px solid var(--phu-border, #CCCCCC);
        }

        @media (max-width: 768px) {
            .section-title h2 {
                font-size: 1.8rem;
                line-height: 1.3;
                padding: 0 1rem;
            }
            
            .section-title p {
                font-size: 1rem;
                padding: 0 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-section {
                padding: 1.5rem;
            }
            
            .back-btn {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 1rem;
                display: inline-block;
            }
            
            .pagination-wrapper .d-flex {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .list-view .travel-card {
                flex-direction: column;
            }
            
            .list-view .travel-header {
                flex: none;
                border-right: none;
                border-bottom: 1px solid #e9ecef;
            }
            
            .list-view .info-item {
                flex: 0 0 100%;
            }
        }
    </style>
    @include('partials.public-trust-styles')
</head>

<body>
    <!-- Back Button -->
    <a href="{{ url('/') }}" class="back-btn">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
    </a>

    <!-- Header Section -->
    <section class="section" style="padding-top: 100px;">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Daftar Travel Berizin</h2>
                <p>Perusahaan Travel Terdaftar di Kanwil Kementerian Haji dan Umroh NTB</p>
            </div>

            <div class="trust-intro" data-aos="fade-up" data-aos-delay="50">
                <div class="d-flex align-items-start gap-3 flex-wrap flex-md-nowrap">
                    <div class="trust-intro__icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h3>Apa itu Indeks Kepercayaan?</h3>
                        <p class="trust-intro__lead">
                            Penilaian sederhana dari Kanwil untuk membantu Anda memilih travel yang lebih aman.
                            Semakin banyak bintang, semakin baik catatannya.
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
                    </div>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="stats-summary" data-aos="fade-up" data-aos-delay="100">
                <h3><i class="fas fa-chart-bar me-2"></i>Ringkasan Data Travel</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['total'] }}</span>
                        <span class="stat-label">Total Travel</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['ppiu'] }}</span>
                        <span class="stat-label">PPIU</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['pihk'] }}</span>
                        <span class="stat-label">PIHK</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['kabupaten'] }}</span>
                        <span class="stat-label">Kabupaten</span>
                    </div>
                    @if(($stats['with_trust_data'] ?? 0) > 0)
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['with_trust_data'] }}</span>
                        <span class="stat-label">Sudah Dinilai</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section" data-aos="fade-up" data-aos-delay="200">
                <div class="row">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">
                            <i class="fas fa-filter me-2"></i>Jenis Layanan:
                        </label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="PPIU">PPIU (Umrah)</option>
                            <option value="PIHK">PIHK (Haji & Umrah)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="jenisFilter" class="form-label">
                            <i class="fas fa-building me-2"></i>Kantor:
                        </label>
                        <select class="form-select" id="jenisFilter">
                            <option value="">Semua Jenis</option>
                            <option value="pusat">Pusat</option>
                            <option value="cabang">Cabang</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kabupatenFilter" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Wilayah:
                        </label>
                        <select class="form-select" id="kabupatenFilter">
                            <option value="">Semua Kabupaten</option>
                            @foreach($allKabupatens as $kabupaten)
                            <option value="{{ $kabupaten }}">{{ $kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">
                            <i class="fas fa-search me-2"></i>Cari Travel:
                        </label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Ketik nama travel...">
                    </div>
                </div>
                
                <!-- Sorting and View Controls -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <label for="sortSelect" class="form-label mb-0">
                                <i class="fas fa-sort me-2"></i>Urutkan:
                            </label>
                            <select class="form-select" id="sortSelect" style="width: auto;">
                                <option value="trust-desc">Rating Terbaik Dulu</option>
                                <option value="name-asc">Nama A-Z</option>
                                <option value="name-desc">Nama Z-A</option>
                                <option value="kabupaten-asc">Kabupaten A-Z</option>
                                <option value="kabupaten-desc">Kabupaten Z-A</option>
                                <option value="tanggal-desc">Tanggal SK Terbaru</option>
                                <option value="tanggal-asc">Tanggal SK Terlama</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <span class="text-muted me-2">Tampilan:</span>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="viewToggle" id="gridView" value="grid" checked>
                                <label class="btn btn-outline-secondary" for="gridView">
                                    <i class="fas fa-th"></i>
                                </label>
                                
                                <input type="radio" class="btn-check" name="viewToggle" id="listView" value="list">
                                <label class="btn btn-outline-secondary" for="listView">
                                    <i class="fas fa-list"></i>
                                </label>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="exportBtn">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
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
                        ? route('travel.public.show', $travel->id)
                        : ($travel->parent_travel_id ? route('travel.public.show', $travel->parent_travel_id) : null);
                @endphp
                <div class="col-lg-6 col-xl-4 mb-4 travel-item" 
                     data-status="{{ $isTravelPusat ? $travel->Status : '' }}" 
                     data-jenis="{{ $isTravelPusat ? 'pusat' : 'cabang' }}"
                     data-kabupaten="{{ $isTravelPusat ? $travel->kab_kota : $travel->kabupaten }}"
                     data-name="{{ strtolower($travel->Penyelenggara) }}"
                     data-trust="{{ $trust['has_data'] ? $trust['score'] : -1 }}"
                     data-tanggal="{{ $isTravelPusat ? ($travel->Tanggal ? $travel->Tanggal->format('Y-m-d') : '') : ($travel->tanggal ? $travel->tanggal->format('Y-m-d') : '') }}">
                    <div class="card travel-card h-100" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
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
                                @include('partials.public-trust-badge', ['trust' => $trust, 'compact' => false])
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

            <!-- Pagination -->
            @if($pagination->hasPages())
            <div class="pagination-wrapper">
                <div class="d-flex justify-content-between align-items-center px-3 py-3 bg-white rounded">
                    <div class="text-muted">
                        Menampilkan {{ $pagination->firstItem() ?? 0 }} sampai {{ $pagination->lastItem() ?? 0 }} 
                        dari {{ $pagination->total() }} data
                    </div>
                    <div>
                        {{ $pagination->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>

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
            const statusFilter = document.getElementById('statusFilter');
            const jenisFilter = document.getElementById('jenisFilter');
            const kabupatenFilter = document.getElementById('kabupatenFilter');
            const searchInput = document.getElementById('searchInput');
            const sortSelect = document.getElementById('sortSelect');
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const exportBtn = document.getElementById('exportBtn');
            const travelItems = document.querySelectorAll('.travel-item');
            const noResults = document.getElementById('noResults');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const travelCards = document.getElementById('travelCards');

            let filterTimeout;

            function showLoading() {
                loadingSpinner.style.display = 'block';
                travelCards.style.opacity = '0.5';
            }

            function hideLoading() {
                loadingSpinner.style.display = 'none';
                travelCards.style.opacity = '1';
            }

            function filterTravel() {
                showLoading();
                
                // Clear existing timeout
                if (filterTimeout) {
                    clearTimeout(filterTimeout);
                }

                // Add delay for better UX
                filterTimeout = setTimeout(() => {
                    const statusValue = statusFilter.value;
                    const jenisValue = jenisFilter.value;
                    const kabupatenValue = kabupatenFilter.value;
                    const searchValue = searchInput.value.toLowerCase().trim();

                    let visibleCount = 0;

                    travelItems.forEach((item, index) => {
                        const status = item.dataset.status;
                        const jenis = item.dataset.jenis;
                        const kabupaten = item.dataset.kabupaten;
                        const name = item.dataset.name;

                        const statusMatch = !statusValue || status === statusValue;
                        const jenisMatch = !jenisValue || jenis === jenisValue;
                        const kabupatenMatch = !kabupatenValue || kabupaten === kabupatenValue;
                        const searchMatch = !searchValue || name.includes(searchValue);

                        if (statusMatch && jenisMatch && kabupatenMatch && searchMatch) {
                            item.style.display = 'block';
                            item.style.animation = 'slideInUp 0.4s ease forwards';
                            item.style.animationDelay = `${index * 0.05}s`;
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Show/hide no results message
                    if (visibleCount === 0) {
                        noResults.style.display = 'block';
                        noResults.style.animation = 'fadeIn 0.3s ease';
                    } else {
                        noResults.style.display = 'none';
                    }

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
                const visibleItems = Array.from(travelItems).filter(item => 
                    item.style.display !== 'none'
                );
                
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

            // Add clear button to filter section
            const filterSection = document.querySelector('.filter-section .row');
            const clearBtn = document.createElement('div');
            clearBtn.className = 'col-12 mt-3';
            clearBtn.innerHTML = `
                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </button>
            `;
            filterSection.appendChild(clearBtn);

            // Make clearFilters globally accessible
            window.clearFilters = clearFilters;
        });

        // Enhanced animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes slideInUp {
                from { 
                    opacity: 0; 
                    transform: translateY(30px) scale(0.95); 
                }
                to { 
                    opacity: 1; 
                    transform: translateY(0) scale(1); 
                }
            }
            
            .travel-item {
                animation-fill-mode: both;
            }
            
            .btn-outline-secondary {
                border-radius: 10px;
                padding: 0.6rem 1.2rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-outline-secondary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
        `;
        document.head.appendChild(style);

        // Smooth scroll for pagination
        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination .page-link')) {
                e.preventDefault();
                const href = e.target.closest('.page-link').getAttribute('href');
                if (href) {
                    window.location.href = href;
                }
            }
        });
    </script>
</body>

</html>

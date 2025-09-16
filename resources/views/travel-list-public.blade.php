<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar Travel Berizin | UHK Kanwil NTB</title>
    <meta name="description" content="Daftar lengkap perusahaan travel berizin di wilayah NTB" />
    <meta name="keywords" content="travel berizin, PPIU, PIHK, NTB, haji, umrah" />

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon" />
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS File -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />

    <style>
        .travel-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .travel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .status-ppiu {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .status-pihk {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .travel-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .travel-body {
            padding: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .info-item i {
            width: 20px;
            color: #6c757d;
            margin-right: 0.75rem;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: white;
            transform: translateX(-5px);
        }

        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #6c757d;
        }

        .stats-summary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .stats-summary h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
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
                <p>Perusahaan Travel Terdaftar di Kanwil Kemenag NTB</p>
            </div>

            <!-- Stats Summary -->
            <div class="stats-summary" data-aos="fade-up" data-aos-delay="100">
                <h3><i class="fas fa-chart-bar me-2"></i>Ringkasan Data Travel</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number">{{ $data->count() }}</span>
                        <span class="stat-label">Total Travel</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $data->where('Status', 'PPIU')->count() }}</span>
                        <span class="stat-label">PPIU</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $data->where('Status', 'PIHK')->count() }}</span>
                        <span class="stat-label">PIHK</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $data->groupBy('kab_kota')->count() }}</span>
                        <span class="stat-label">Kabupaten</span>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section" data-aos="fade-up" data-aos-delay="200">
                <div class="row">
                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label">Filter Status:</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="PPIU">PPIU (Umrah)</option>
                            <option value="PIHK">PIHK (Haji & Umrah)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="kabupatenFilter" class="form-label">Filter Kabupaten:</label>
                        <select class="form-select" id="kabupatenFilter">
                            <option value="">Semua Kabupaten</option>
                            @foreach($data->groupBy('kab_kota') as $kabupaten => $travelList)
                            <option value="{{ $kabupaten }}">{{ $kabupaten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="searchInput" class="form-label">Cari Travel:</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Nama travel...">
                    </div>
                </div>
            </div>

            <!-- Travel Cards -->
            <div class="row" id="travelCards">
                @foreach($data as $travel)
                <div class="col-lg-6 col-xl-4 mb-4 travel-item" 
                     data-status="{{ $travel->Status }}" 
                     data-kabupaten="{{ $travel->kab_kota }}"
                     data-name="{{ strtolower($travel->Penyelenggara) }}">
                    <div class="card travel-card h-100" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="travel-header">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $travel->Penyelenggara }}</h5>
                                <span class="status-badge {{ $travel->Status === 'PPIU' ? 'status-ppiu' : 'status-pihk' }}">
                                    {{ $travel->Status }}
                                </span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $travel->kab_kota }}
                            </p>
                        </div>
                        <div class="travel-body">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span><strong>Pimpinan:</strong> {{ $travel->Pimpinan }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <span><strong>Telepon:</strong> {{ $travel->Telepon }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><strong>Alamat:</strong> {{ $travel->alamat_kantor_baru ?: $travel->alamat_kantor_lama }}</span>
                            </div>
                            @if($travel->nilai_akreditasi)
                            <div class="info-item">
                                <i class="fas fa-certificate"></i>
                                <span><strong>Akreditasi:</strong> {{ $travel->nilai_akreditasi }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Tanggal SK:</strong> {{ $travel->Tanggal ? $travel->Tanggal->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($data->count() == 0)
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada data travel</h4>
                <p class="text-muted">Data travel berizin akan ditampilkan di sini.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer dark-background">
        <div class="container text-center py-3">
            <p class="mb-0 text-white-50">
                © <script>document.write(new Date().getFullYear())</script> 
                <strong class="text-white">UHK Kanwil NTB</strong>. All Rights Reserved
            </p>
        </div>
    </footer>

    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const kabupatenFilter = document.getElementById('kabupatenFilter');
            const searchInput = document.getElementById('searchInput');
            const travelItems = document.querySelectorAll('.travel-item');

            function filterTravel() {
                const statusValue = statusFilter.value;
                const kabupatenValue = kabupatenFilter.value;
                const searchValue = searchInput.value.toLowerCase();

                travelItems.forEach(item => {
                    const status = item.dataset.status;
                    const kabupaten = item.dataset.kabupaten;
                    const name = item.dataset.name;

                    const statusMatch = !statusValue || status === statusValue;
                    const kabupatenMatch = !kabupatenValue || kabupaten === kabupatenValue;
                    const searchMatch = !searchValue || name.includes(searchValue);

                    if (statusMatch && kabupatenMatch && searchMatch) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeIn 0.3s ease';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            statusFilter.addEventListener('change', filterTravel);
            kabupatenFilter.addEventListener('change', filterTravel);
            searchInput.addEventListener('input', filterTravel);
        });

        // Add fadeIn animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>

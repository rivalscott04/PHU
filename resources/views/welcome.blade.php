<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Agency Landing Page</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css -->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://source.unsplash.com/random/1920x1080/?travel');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .travel-card {
            transition: transform 0.3s;
        }

        .travel-card:hover {
            transform: translateY(-5px);
        }

        .schedule-table th,
        .schedule-table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">PHU</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#schedule">Jadwal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#travel-list">List Travel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#complaint">Pengaduan</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4 text-white">Lorem ipsum dolor sit amet</h1>
                    <p class="lead mb-4">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Provident saepe
                        repellat laudantium doloremque praesentium nihil a veritatis incidunt, accusantium adipisci!</p>
                    <a href="#schedule" class="btn btn-primary btn-lg me-3">Cek Jadwal</a>
                    <a href="#travel-list" class="btn btn-success btn-lg">Lihat Travel</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Jadwal Keberangkatan</h2>
            <div class="table-responsive">
                <table class="table table-striped schedule-table">
                    <thead>
                        <tr>
                            <th>PPIU</th>
                            <th>Maskapai Berangkat</th>
                            <th>Tanggal Berangkat</th>
                            <th>Tanggal Kembali</th>
                            <th>Maskapai Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bapData as $data)
                            <tr>
                                <td>{{ $data->ppiuname }}</td>
                                <td>{{ $data->airlines }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->datetime)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->returndate)->format('d M Y') }}</td>
                                <td>{{ $data->airlines2 }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $data->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Detail -->
                            <div class="modal fade" id="detailModal{{ $data->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Keberangkatan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <strong>PPIU:</strong> {{ $data->ppiuname }}
                                            </div>
                                            <div class="mb-3">
                                                <strong>Maskapai Berangkat:</strong> {{ $data->airlines }}
                                            </div>
                                            <div class="mb-3">
                                                <strong>Tanggal Keberangkatan:</strong>
                                                {{ \Carbon\Carbon::parse($data->datetime)->format('d M Y') }}
                                            </div>
                                            <div class="mb-3">
                                                <strong>Tanggal Kembali:</strong>
                                                {{ \Carbon\Carbon::parse($data->returndate)->format('d M Y') }}
                                            </div>
                                            <div class="mb-3">
                                                <strong>Maskapai Kembali:</strong> {{ $data->airlines2 }}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="button" class="btn btn-primary">Hubungi PPIU</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Travel List Section -->
    <section id="travel-list" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">List Travel Partner</h2>
            <div class="row">
                @foreach ($travelData as $travel)
                    <div class="col-md-4 mb-4">
                        <div class="card travel-card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $travel->Penyelenggara }}</h5>
                                <p class="card-text">
                                    <i class="fas fa-award text-warning"></i> Akreditasi:
                                    {{ $travel->Jml_Akreditasi }}<br>
                                    <i class="fas fa-phone"></i> {{ $travel->Telepon }}<br>
                                    <i class="fas fa-map-marker-alt"></i> {{ $travel->kab_kota }}
                                </p>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary me-2" data-bs-toggle="modal"
                                        data-bs-target="#travelModal{{ $loop->index }}">
                                        Lihat Detail
                                    </button>
                                    <a class="btn btn-success">
                                        <i class="fas fa-phone"></i> Hubungi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail Travel -->
                    <div class="modal fade" id="travelModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Travel Partner</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <strong>Nama Penyelenggara:</strong><br>
                                        {{ $travel->Penyelenggara }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Jumlah Akreditasi:</strong><br>
                                        {{ $travel->Jml_Akreditasi }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Nomor Telepon:</strong><br>
                                        {{ $travel->Telepon }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Lokasi:</strong><br>
                                        {{ $travel->kab_kota }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tutup</button>
                                    <a href="tel:{{ $travel->Telepon }}" class="btn btn-primary">
                                        <i class="fas fa-phone"></i> Hubungi Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Complaint Section -->
    <section id="complaint" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Formulir Pengaduan</h2>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="/submit-complaint" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Nama Pengadu</label>
                                    <input type="text" class="form-control" name="nama_pengadu" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Travel</label>
                                    <select class="form-select" name="travel" required>
                                        <option value="">Pilih Travel</option>
                                        @foreach ($travelData as $travel)
                                            <option value="{{ $travel->Penyelenggara }}">{{ $travel->Penyelenggara }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hal yang Diadukan</label>
                                    <input type="text" class="form-control" name="hal_pengaduan" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Berkas Pendukung</label>
                                    <input type="file" class="form-control" name="berkas_pendukung"
                                        accept=".jpg,.png,.pdf">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Kirim Pengaduan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Tentang Kami</h5>
                    <p>Kami adalah penyedia layanan travel terpercaya dengan jaringan travel partner terluas di
                        Indonesia.</p>
                </div>
                <div class="col-md-4">
                    <h5>Kontak Darurat</h5>
                    <p>Hotline: 0800-123-4567<br>
                        WhatsApp: +62 812-3456-7890<br>
                        Email: cs@travelagency.com</p>
                </div>
                <div class="col-md-4">
                    <h5>Jam Operasional</h5>
                    <p>Senin - Minggu<br>
                        24 Jam Non-Stop<br>
                        Termasuk Hari Libur</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Travel Agency. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS and Popper.js -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>

    <!-- apexcharts -->
    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Sweet alert init js-->
    <script src="{{ asset('js/pages/sweet-alerts.init.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ asset('js/pages/dashboard.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>

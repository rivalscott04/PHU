<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>UHK KANWIL</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />

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
    <link href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet" />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS File -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />

    <style>
        .travel-stats-card {
            transition: all 0.3s ease-in-out;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .travel-stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            cursor: pointer;
        }

        .travel-stats-card .card-body {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        .travel-stats-card:hover .bi {
            animation: bounce 0.5s;
        }

        .stats-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
        }

        .stats-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            background-color: rgba(0, 0, 0, 0.05);
        }

        i.bi {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .stats-item:hover i.bi {
            transform: scale(1.2);
            color: #007bff;
            /* Warna biru Bootstrap, bisa diganti */
        }


        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .company-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .company-icon i {
            font-size: 2rem;
            color: #0d6efd;
        }

        .accreditation {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .accreditation i {
            color: #ffc107;
        }

        /* Updated icon colors */
        .detail-item i.bi-telephone-fill {
            color: #29cc61;
            /* Green color for telephone */
        }

        .detail-item i.bi-geo-alt-fill {
            color: #ff2828;
            /* Orange-red color for location */
        }

        .company-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1a1a1a;
            min-height: 2.5rem;
        }

        .company-details {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .detail-item {
            display: flex;
            align-items: start;
            flex-direction: column;
        }

        .detail-item i {
            color: #6c757d;
            font-size: 1rem;
        }

        .detail-item span {
            font-size: 0.9rem;
            color: #4a4a4a;
        }

        .hidden-item {
            display: none;
        }

        .travel-item {
            transition: all 0.3s ease-in-out;
        }

        #showMoreBtn {
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #showMoreBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Animation for items appearing */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .travel-item.show {
            display: block;
            animation: fadeIn 0.5s ease forwards;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --transition: all 0.3s ease;
        }

        #calendar {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 20px;
            min-height: 700px;
            margin-bottom: 20px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.5em;
            margin: 0;
            padding: 0;
        }

        .fc .fc-button-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .fc .fc-button-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Custom styling for all calendar buttons to avoid black colors */
        .fc .fc-button-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .fc .fc-button-primary:hover {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
        }

        .fc .fc-button-primary:active {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
        }

        /* Ensure active button has good contrast */
        .fc .fc-button-primary.fc-button-active {
            background-color: var(--success-color) !important;
            border-color: var(--success-color) !important;
            color: white !important;
            font-weight: 600;
        }

        .event-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 90%;
            max-width: 500px;
        }

        .event-popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .detail-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .detail-value {
            color: var(--text-primary);
        }

        .close-btn {
            position: absolute;
            right: 8px;
            top: -5px;
            background: none;
            border: none;
            font-size: 45px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .year-select-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .year-select-content {
            background: white;
            padding: 20px;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
        }

        .year-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 10px;
        }

        .year-button {
            padding: 10px;
            border: 1px solid var(--primary-color);
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .year-button:hover {
            background: var(--primary-color);
            color: white;
        }

        .current-year {
            background: var(--primary-color);
            color: white;
        }

        .fc .fc-toolbar {
            justify-content: center;
            gap: 20px;
        }

        .fc .fc-toolbar-title {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 6px;
            transition: var(--transition);
        }

        .fc .fc-toolbar-title:hover {
            background: rgba(37, 99, 235, 0.1);
        }

        .fc .fc-multimonth {
            padding: 20px;
            border-radius: var(--border-radius);
            background: var(--card-background);
        }

        .fc-event {
            cursor: pointer;
            padding: 2px 4px;
        }

        .fc-event .fc-content {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-event .fc-description {
            margin-top: 2px;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc-header-toolbar {
                margin-bottom: 1.5em !important;
            }

            .fc .fc-button {
                padding: 0.4em 0.65em;
            }

            .event-popup {
                width: 95%;
                padding: 15px;
            }

            .fc-event {
                font-size: 0.85em;
            }

            .fc-toolbar-title {
                font-size: 1.2em !important;
            }

            .year-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .close-btn {
                right: 4px;
            }
        }
    </style>
</head>

<body class="index-page">
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
            <a href="index.html" class="logo d-flex align-items-center">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">UHK Kanwil NTB</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#beranda" class="active">Beranda</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#calendar-section">Jadwal Keberangkatan</a></li>
                    <li><a href="#informasi">Informasi</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </header>

    <main class="main">
        <!-- Hero Section -->
        <section id="beranda" class="hero section dark-background">
            <img src="{{ asset('img/hero-2.jpg') }}" alt="" class="hero-bg" />

            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row gy-4 justify-content-between">
                    <div class="col-lg-4 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
                        <img src="{{ asset('img/hero-1.png') }}" class="img-fluid animated" alt="" />
                    </div>

                    <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-in">
                        <h1>
                            Sistem Informasi Haji dan Umroh Khusus
                            <span>Kanwil Kemenag NTB</span>
                        </h1>
                        <p>
                            Informasi Keberangkatan dan List Travel.
                        </p>
                        <div class="d-flex">
                            <a href="{{ route('login') }}" class="btn-get-started">Masuk</a>
                        </div>
                    </div>
                </div>
            </div>

            <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                viewBox="0 24 150 28 " preserveAspectRatio="none">
                <defs>
                    <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
                    </path>
                </defs>
                <g class="wave1">
                    <use xlink:href="#wave-path" x="50" y="3"></use>
                </g>
                <g class="wave2">
                    <use xlink:href="#wave-path" x="50" y="0"></use>
                </g>
                <g class="wave3">
                    <use xlink:href="#wave-path" x="50" y="9"></use>
                </g>
            </svg>
        </section>
        <!-- /Hero Section -->

        <!-- About Section -->
        <section id="about" class="about section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row align-items-xl-center gy-5">
                    <div class="col-xl-5 content">
                        <h3>5 Pasti Umroh</h3>
                        <h2>PASTIKAN Travel Umrah Berizin Kemenag.</h2>
                        <p>
                            Travel harus memiliki izin umrah agar terjamin perlindungan, pelayanan, dan bimbingan selama di Tanah Suci.
                        </p>
                        <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
                    </div>

                    <div class="col-xl-7">
                        <div class="row gy-4 icon-boxes">
                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                                <div class="icon-box">
                                    <i class="bi bi-airplane"></i>
                                    <h3>PASTIKAN Tiket Pesawat dan Jadwal Penerbangan.</h3>
                                    <p>
                                        Maskapai penerbangannya harus jelas, jadwal berangkatnya pasti, tiketnya harus pulang-pergi, dan hanya satu kali transit dengan maskapai penerbangan yang sama.
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon-box">
                                    <i class="bi bi-clipboard-pulse"></i>
                                    <h3>PASTIKAN Harga dan Paket Layanannya.</h3>
                                    <p>
                                        Jangan tergiur harga murah, cek rincian harga paket yang ditawarkan. Paket layanan terdiri dari konsumsi, transportasi, manasik, petugas yang mendampingi, dan asuransi perjalanan.
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                                <div class="icon-box">
                                    <i class="bi bi-buildings"></i>
                                    <h3>PASTIKAN Akomodasi (Hotel) selama berada di Arab Saudi.</h3>
                                    <p>
                                        Hotel tempat menginap minimal hotel bintang 3 dan jarak dari tempat ibadah maksimal 1 km.
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                                <div class="icon-box">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    <h3>PASTIKAN Visanya</h3>
                                    <p>
                                        Visa harus selesai minimal 3 hari sebelum keberangkatan.
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /About Section -->

        <section id="calendar-section" class="calendar-section section">
            <div class="container section-title" data-aos="fade-up" style="padding: 15px;">
                <h2>Kalender</h2>
                <div>
                    <span>Jadwal</span>
                    <span class="description-title">Keberangkatan</span>
                </div>
            </div>

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div id="calendar"></div>
            </div>

            <!-- Event Popup Overlay -->
            <div class="event-popup-overlay" id="popupOverlay" onclick="closePopup()"></div>

            <!-- Event Popup -->
            <div id="eventPopup" class="event-popup">
                <div id="popupContent" class="event-details"></div>
            </div>

            <!-- Year Selection Modal -->
            <div id="yearSelectModal" class="year-select-modal">
                <div class="year-select-content">
                    <div class="year-grid" id="yearGrid"></div>
                </div>
            </div>
        </section>

        <!-- Informasi Section -->
        <section id="informasi" class="informasi section light-background">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Informasi</h2>
                <div>
                    <span>Informasi</span>
                    <span class="description-title">Publik</span>
                </div>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4 icon-boxes">
                    <!-- FAQ Section -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box">
                            <i class="bi bi-question-circle"></i>
                            <h3>FAQ (Frequently Asked Questions)</h3>
                            <p>
                                Temukan jawaban untuk pertanyaan yang sering diajukan tentang sistem PHU Kanwil NTB, cara melihat jadwal keberangkatan, dan prosedur pengaduan.
                            </p>
                            <div class="mt-3">
                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse">
                                    Lihat FAQ <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse mt-3" id="faqCollapse">
                                <div class="card card-body border-0 bg-light">
                                    <div class="mb-3">
                                        <strong>Q: Apa itu Sistem PHU Kanwil NTB?</strong><br>
                                        <small class="text-muted">A: Sistem Informasi Haji dan Umroh Khusus (PHU) Kanwil NTB adalah platform digital untuk mengelola dan memantau kegiatan haji dan umroh di wilayah NTB.</small>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Q: Bagaimana cara melihat jadwal keberangkatan?</strong><br>
                                        <small class="text-muted">A: Anda dapat melihat jadwal keberangkatan di menu "Jadwal Keberangkatan" yang menampilkan kalender dengan detail waktu keberangkatan dan kepulangan.</small>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Q: Berapa lama proses pengaduan?</strong><br>
                                        <small class="text-muted">A: Proses pengaduan akan ditindaklanjuti dalam waktu maksimal 3-5 hari kerja setelah pengaduan diterima.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Icon Box -->

                    <!-- Informasi Pengaduan Section -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="icon-box">
                            <i class="bi bi-exclamation-triangle"></i>
                            <h3>Informasi Pengaduan</h3>
                            <p>
                                Pelajari cara mengajukan pengaduan dan jenis pengaduan yang dapat diajukan terkait layanan haji dan umroh.
                            </p>
                            <div class="mt-3">
                                <button class="btn btn-outline-warning btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#pengaduanCollapse">
                                    Lihat Detail <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse mt-3" id="pengaduanCollapse">
                                <div class="card card-body border-0 bg-light">
                                    <div class="mb-3">
                                        <strong>Cara Mengajukan Pengaduan:</strong><br>
                                        <small class="text-muted">
                                            1. Isi form pengaduan dengan lengkap<br>
                                            2. Pilih travel yang terkait dengan pengaduan<br>
                                            3. Jelaskan detail hal yang diadukan<br>
                                            4. Lampirkan bukti pendukung (opsional)<br>
                                            5. Klik tombol "Kirim Pengaduan"
                                        </small>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Kontak Darurat:</strong><br>
                                        <small class="text-muted">
                                            <i class="bi bi-telephone me-1"></i>0370-123456<br>
                                            <i class="bi bi-envelope me-1"></i>ntb.kemenag@go.id<br>
                                            <i class="bi bi-geo-alt me-1"></i>Jl. Udayana No.6, Mataram
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Icon Box -->



                    <!-- Panduan Section -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="icon-box">
                            <i class="bi bi-book"></i>
                            <h3>Panduan Penggunaan</h3>
                            <p>
                                Pelajari cara menggunakan sistem PHU dengan panduan lengkap untuk setiap fitur yang tersedia.
                            </p>
                            <div class="mt-3">
                                <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panduanCollapse">
                                    Lihat Panduan <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse mt-3" id="panduanCollapse">
                                <div class="card card-body border-0 bg-light">
                                    <div class="mb-2">
                                        <strong>Fitur Utama:</strong><br>
                                        <small class="text-muted">
                                            • Jadwal Keberangkatan: Lihat kalender keberangkatan<br>
                                            • Pengaduan: Ajukan keluhan terkait layanan<br>
                                            • Informasi Travel: Data travel berizin<br>
                                            • Statistik: Data terkini keberangkatan
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Icon Box -->
                </div>
            </div>
        </section>
        <!-- /Informasi Section -->

        <!-- Details Section -->
        <section id="details" class="details section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Details</h2>
                <div>
                    <span>Check Our</span>
                    <span class="description-title">Details</span>
                </div>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row gy-4 align-items-center features-item">
                    <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="100">
                        <img src="{{ asset('img/hero-2.png') }}" class="img-fluid" alt="" />
                    </div>
                    <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
                        <h3>
                            Sistem Terpadu Pengelolaan Haji dan Umroh Khusus
                            Kanwil NTB
                        </h3>
                        <p class="fst-italic">
                            Platform digital yang mengintegrasikan seluruh proses pengelolaan haji dan umroh khusus 
                            di wilayah Nusa Tenggara Barat dengan sistem yang terpusat dan terkoordinasi.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i><span>
                                    Monitoring real-time keberangkatan jamaah haji dan umroh khusus</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Pengelolaan data travel berizin dan terverifikasi Kemenag</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Sistem pengaduan terintegrasi untuk perlindungan jamaah</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Features Item -->

                <div class="row gy-4 align-items-center features-item">
                    <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out"
                        data-aos-delay="200">
                        <img src="{{ asset('img/hero-3.png') }}" class="img-fluid" alt="" />
                    </div>
                    <div class="col-md-7 order-2 order-md-1" data-aos="fade-up" data-aos-delay="200">
                        <h3>Pelayanan Terpadu dan Transparan</h3>
                        <p class="fst-italic">
                            Memberikan pelayanan yang terintegrasi dan transparan dalam pengelolaan 
                            haji dan umroh khusus dengan standar pelayanan yang tinggi.
                        </p>
                        <p>
                            Sistem ini dirancang untuk memastikan setiap jamaah mendapatkan pelayanan 
                            yang optimal mulai dari pendaftaran, pembayaran, hingga keberangkatan. 
                            Dengan teknologi yang modern, kami memastikan transparansi dalam setiap 
                            tahap proses dan memberikan kemudahan akses informasi bagi semua pihak terkait.
                        </p>
                    </div>
                </div>
                <!-- Features Item -->

                <div class="row gy-4 align-items-center features-item">
                    <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out">
                        <img src="{{ asset('img/hero-4.png') }}" class="img-fluid" alt="" />
                    </div>
                    <div class="col-md-7" data-aos="fade-up">
                        <h3>
                            Keamanan dan Perlindungan Jamaah
                        </h3>
                        <p>
                            Memprioritaskan keamanan dan kenyamanan jamaah dengan sistem monitoring 
                            yang komprehensif dan mekanisme pengaduan yang responsif untuk memastikan 
                            perlindungan hak-hak jamaah.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Sistem verifikasi travel berizin dan terpercaya</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i><span>
                                    Monitoring real-time status keberangkatan jamaah</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Mekanisme pengaduan cepat dan terintegrasi</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Features Item -->

                <div class="row gy-4 align-items-center features-item">
                    <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out">
                        <img src="{{ asset('img/hero-5.png') }}" class="img-fluid" alt="" />
                    </div>
                    <div class="col-md-7 order-2 order-md-1" data-aos="fade-up">
                        <h3>
                            Teknologi Modern untuk Pelayanan Optimal
                        </h3>
                        <p class="fst-italic">
                            Menggunakan teknologi terkini untuk memberikan pelayanan yang efisien, 
                            akurat, dan mudah diakses oleh semua stakeholder dalam pengelolaan haji dan umroh khusus.
                        </p>
                        <p>
                            Platform ini dibangun dengan teknologi modern yang memungkinkan akses 
                            real-time, pelaporan yang akurat, dan komunikasi yang efektif antara 
                            jamaah, travel, dan pihak terkait. Dengan sistem yang terintegrasi, 
                            kami memastikan setiap proses berjalan lancar dan transparan sesuai 
                            dengan standar pelayanan yang telah ditetapkan.
                        </p>
                    </div>
                </div>
                <!-- Features Item -->
            </div>
        </section>
        <!-- /Details Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Pengaduan</h2>
                <div>
                    <span>Form</span>
                    <span class="description-title">Pengaduan</span>
                </div>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade" data-aos-delay="100">
                @if ($errors->any())
                    <div class="alert alert-danger" data-aos="fade-up" data-aos-delay="150">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tab Navigation -->
                <div class="row mb-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="col-12">
                        <ul class="nav nav-tabs nav-fill" id="pengaduanTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#form-pengaduan" type="button" role="tab">
                                    <i class="bi bi-plus-circle me-2"></i>Buat Pengaduan Baru
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pengaduan" type="button" role="tab">
                                    <i class="bi bi-clock-history me-2"></i>Riwayat Pengaduan
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="pengaduanTabContent">
                    <!-- Tab 1: Form Pengaduan -->
                    <div class="tab-pane fade show active" id="form-pengaduan" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-4">
                                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                                    <i class="bi bi-geo-alt flex-shrink-0"></i>
                                    <div>
                                        <h3>Alamat Kantor</h3>
                                        <p>Jl. Udayana No.6, Mataram</p>
                                    </div>
                                </div>

                                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                                    <i class="bi bi-telephone flex-shrink-0"></i>
                                    <div>
                                        <h3>Telephone</h3>
                                        <p>0370-123456</p>
                                    </div>
                                </div>

                                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                                    <i class="bi bi-envelope flex-shrink-0"></i>
                                    <div>
                                        <h3>Email</h3>
                                        <p>ntb.kemenag@go.id</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <form id="pengaduanForm" action="{{ route('pengaduan.store-public') }}" method="post"
                                    class="php-email-form" data-aos="fade-up" data-aos-delay="200"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row gy-4">
                                        <div class="col-md-6">
                                            <input type="text" name="nama_pengadu" class="form-control"
                                                placeholder="Nama Pengadu" required value="{{ old('nama_pengadu') }}" 
                                                style="background-color: white;" />
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-control" name="travels_id" required style="background-color: white;">
                                                <option value="">-- Pilih Travel --</option>
                                                @foreach ($travels as $travel)
                                                    <option value="{{ $travel->id }}"
                                                        {{ old('travels_id') == $travel->id ? 'selected' : '' }}>
                                                        {{ $travel->Penyelenggara }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <textarea class="form-control" name="hal_aduan" rows="6" placeholder="Hal yang Diadukan" required 
                                                      style="background-color: white;">{{ old('hal_aduan') }}</textarea>
                                        </div>

                                        <div class="col-md-12">
                                            <input type="file" class="form-control" name="berkas_aduan" style="background-color: white;" />
                                            <small class="text-muted mt-1">File maksimal 300KB</small>
                                        </div>

                                        <div class="col-md-12 text-center">
                                            <div class="loading">Loading</div>
                                            <div class="error-message"></div>
                                            <div class="sent-message">Pengaduan Anda telah terkirim. Terima kasih!</div>
                                            <div class="mt-3">
                                                <button type="button"
                                                    class="btn btn-success rounded-pill px-3 py-2 border border-0"
                                                    style="background-color: #1acc8d" onclick="confirmSubmit()">Kirim
                                                    Pengaduan</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Riwayat Pengaduan -->
                    <div class="tab-pane fade" id="riwayat-pengaduan" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                                    <div class="card-body p-4">
                                        <div class="text-center" id="riwayatContent">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Memuat data pengaduan...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Contact Section -->
    </main>

    <footer id="footer" class="footer dark-background">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="footer-contact">
                        <h5 class="mb-4 text-white">Kantor Wilayah Kementerian Agama Provinsi NTB</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <p class="mb-2 text-white-50">
                                    <i class="bi bi-geo-alt me-2"></i>Jl. Udayana No. 6, Mataram, NTB
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2 text-white-50">
                                    <i class="bi bi-telephone me-2"></i>0370-123456
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2 text-white-50">
                                    <i class="bi bi-envelope me-2"></i>ntb.kemenag@kemenag.go.id
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container text-center py-3 border-top border-secondary">
            <div class="row">
                <div class="col-12">
                    <p class="mb-2 text-white-50">
                        © <script>document.write(new Date().getFullYear())</script> 
                        <strong class="text-white">UHK Kanwil NTB</strong>. All Rights Reserved
                    </p>
                    <p class="mb-0 text-white-50 small">
                        Designed with <i class="bi bi-heart-fill text-danger"></i> by 
                        <strong class="text-white">UHK Kanwil NTB</strong>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>
    <script src="{{ asset('js/purecounter_vanilla.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showMoreBtn = document.getElementById('showMoreBtn');
            const hiddenItems = document.querySelectorAll('.hidden-item');
            let isExpanded = false;

            if (showMoreBtn) {
                showMoreBtn.addEventListener('click', function() {
                    if (!isExpanded) {
                        hiddenItems.forEach(item => {
                            item.classList.remove('hidden-item');
                            item.classList.add('show');
                        });
                        showMoreBtn.textContent = 'Show Less';
                        isExpanded = true;
                    } else {
                        hiddenItems.forEach(item => {
                            item.classList.add('hidden-item');
                            item.classList.remove('show');
                        });
                        showMoreBtn.textContent = 'Show More';
                        isExpanded = false;

                        // Scroll to features section if it's out of view
                        document.getElementById('features').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var yearSelectModal = document.getElementById('yearSelectModal');
            var yearGrid = document.getElementById('yearGrid');

            // Setup year selection modal
            function setupYearGrid() {
                const currentYear = new Date().getFullYear();
                yearGrid.innerHTML = '';

                for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                    const btn = document.createElement('button');
                    btn.className = `year-button ${year === currentYear ? 'current-year' : ''}`;
                    btn.textContent = year;
                    btn.onclick = function() {
                        calendar.gotoDate(`${year}-01-01`);
                        yearSelectModal.style.display = 'none';
                        calendar.changeView('multiMonth');
                    };
                    yearGrid.appendChild(btn);
                }
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,multiMonth'
                },
                views: {
                    multiMonth: {
                        type: 'multiMonth',
                        duration: {
                            months: 12
                        },
                        multiMonthMaxColumns: 3,
                        multiMonthMinWidth: 350,
                        showNonCurrentDates: false
                    }
                },
                locale: 'id',
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    multiMonth: 'Tahun'
                },
                events: {
                    url: window.location.protocol + '//' + window.location.host + '/keberangkatan/events',
                    method: 'GET',
                    failure: function() {
                        alert('Error mengambil data keberangkatan!');
                    }
                },
                eventClick: function(info) {
                    showPopup(info.event);
                },
                eventContent: function(arg) {
                    return {
                        html: `<div class="fc-content">
                        <div class="fc-title">${arg.event.title}</div>
                        <div class="fc-description" style="font-size: 0.8em;">
                            ${arg.event.extendedProps.package} hari
                        </div>
                    </div>`
                    };
                },
                titleFormat: {
                    year: 'numeric',
                    month: 'long'
                },
                dayMaxEvents: true,
                displayEventTime: false,
                // Make title clickable for year selection
                titleRender: function(info) {
                    info.el.onclick = function() {
                        setupYearGrid();
                        yearSelectModal.style.display = 'flex';
                    };
                },
                viewDidMount: function(info) {
                    // Trigger year selection modal when switching to multiMonth view
                    if (info.view.type === 'multiMonth') {
                        setupYearGrid();
                        yearSelectModal.style.display = 'flex';
                    }
                }
            });

            calendar.render();

            // Close year select modal when clicking outside
            yearSelectModal.onclick = function(e) {
                if (e.target === yearSelectModal) {
                    yearSelectModal.style.display = 'none';
                }
            };

            // Close year select modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    yearSelectModal.style.display = 'none';
                    closePopup();
                }
            });
        });

        function showPopup(event) {
            const popup = document.getElementById('eventPopup');
            const overlay = document.getElementById('popupOverlay');
            const content = document.getElementById('popupContent');

            const departureDate = new Date(event.extendedProps.returndate).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Format the raw date
            const rawDate = event.extendedProps.returndate;
            const formattedRawDate = rawDate.split('T')[0]; // Extract YYYY-MM-DD

            content.innerHTML = `
        <h3 class="text-primary text-xl font-semibold mb-6">
            ${event.title}
            <button class="close-btn" onclick="closePopup()">×</button>
        </h3>

        <div class="space-y-4">
            <div class="detail-item">
                <div class="detail-label mb-2">
                    <i class="fas fa-user text-gray-500"></i> Penanggung Jawab
                </div>
                <div class="detail-value text-gray-700 pl-6">
                    ${event.extendedProps.name} (${event.extendedProps.jabatan})
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label mb-2">
                    <i class="fas fa-plane-arrival text-gray-500"></i> Tanggal Kepulangan
                </div>
                <div class="detail-value pl-6">
                    <div class="text-gray-700">${departureDate}</div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label mb-2">
                    <i class="fas fa-users text-gray-500"></i> Jumlah Jamaah
                </div>
                <div class="detail-value text-gray-700 pl-6">
                    ${event.extendedProps.people} orang
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label mb-2">
                    <i class="fas fa-clock text-gray-500"></i> Durasi
                </div>
                <div class="detail-value text-gray-700 pl-6">
                    ${event.extendedProps.package} Hari
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label mb-2">
                    <i class="fas fa-plane text-gray-500"></i> Maskapai Keberangkatan
                </div>
                <div class="detail-value text-gray-700 pl-6">
                    ${event.extendedProps.airlines}
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label mb-2">
                    <i class="fas fa-plane text-gray-500"></i> Maskapai Kepulangan
                </div>
                <div class="detail-value text-gray-700 pl-6">
                    ${event.extendedProps.airlines2}
                </div>
            </div>
        </div>
    `;

            overlay.style.display = 'block';
            popup.style.display = 'block';
        }

        function closePopup() {
            const popup = document.getElementById('eventPopup');
            const overlay = document.getElementById('popupOverlay');

            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        function closePopup() {
            document.getElementById('eventPopup').style.display = 'none';
            document.getElementById('popupOverlay').style.display = 'none';
        }

        function confirmSubmit() {
            Swal.fire({
                title: 'Apakah anda yakin mengirim aduan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('pengaduanForm').submit();
                }
            })
        }

        // Tab navigation handler
        document.addEventListener('DOMContentLoaded', function() {
            const riwayatTab = document.getElementById('riwayat-tab');
            if (riwayatTab) {
                riwayatTab.addEventListener('click', function() {
                    loadRiwayatPengaduan();
                });
            }
        });

        function loadRiwayatPengaduan() {
            const riwayatContent = document.getElementById('riwayatContent');
            
            // Show loading state
            riwayatContent.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data pengaduan...</p>
                </div>
            `;
            
            // Fetch completed pengaduan data
            fetch('/api/pengaduan-completed')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-hover">';
                        html += '<thead class="table-dark"><tr><th>No</th><th>Nama Pengadu</th><th>Travel</th><th>Hal Pengaduan</th><th>Tanggal Selesai</th><th>Aksi</th></tr></thead><tbody>';
                        
                        data.forEach((item, index) => {
                            const travelName = item.travel ? item.travel.Penyelenggara : 'Tidak diketahui';
                            const halAduan = item.hal_aduan ? (item.hal_aduan.length > 50 ? item.hal_aduan.substring(0, 50) + '...' : item.hal_aduan) : 'Tidak ada detail';
                            const completedDate = item.completed_at ? new Date(item.completed_at).toLocaleDateString('id-ID') : 'Tidak diketahui';
                            
                            html += `<tr>
                                <td>${index + 1}</td>
                                <td><strong>${item.nama_pengadu || 'Tidak diketahui'}</strong></td>
                                <td><span class="text-primary">${travelName}</span></td>
                                <td><span class="text-muted">${halAduan}</span></td>
                                <td><small class="text-muted">${completedDate}</small></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm rounded-pill" 
                                            onclick="downloadPDF(${item.id})">
                                        <i class="bi bi-download me-1"></i> PDF
                                    </button>
                                </td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table></div>';
                        riwayatContent.innerHTML = html;
                    } else {
                        riwayatContent.innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">Belum ada pengaduan yang selesai diproses</h5>
                                <p class="text-muted">Pengaduan yang sudah selesai akan ditampilkan di sini</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    riwayatContent.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h5 class="text-danger mt-3">Terjadi kesalahan</h5>
                            <p class="text-muted">Gagal memuat data pengaduan. Silakan coba lagi nanti.</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="loadRiwayatPengaduan()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                            </button>
                        </div>
                    `;
                });
        }

        // Keep the old function for backward compatibility (if needed)
        function openPengaduanModal() {
            // Redirect to riwayat tab
            const riwayatTab = document.getElementById('riwayat-tab');
            if (riwayatTab) {
                riwayatTab.click();
            }
        }

        function downloadPDF(id) {
            window.open(`/pengaduan/${id}/download-pdf`, '_blank');
        }
    </script>


</body>

</html>

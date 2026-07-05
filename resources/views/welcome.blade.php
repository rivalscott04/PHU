<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name') }}</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Roboto:wght@400;500;600&display=swap" rel="stylesheet" />

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
    <link href="{{ asset('css/public-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/interactive-cursor.css') }}" rel="stylesheet" />

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
            background: #FFFFFF;
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
            color: #e2a712;
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
            color: #c8940e;
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
            color: #e2a712;
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

        /* Stats Section Styles */
        .stats-section {
            padding: 60px 0;
        }

        .stats-bar {
            background: #FFFFFF;
            border-radius: 12px;
            padding: min(4vw, 40px) min(3vw, 30px);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #E2E2E2;
        }

        .stat-item {
            text-align: center;
            padding: 20px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #FAFAF8;
            border: 1px solid #E2E2E2;
        }

        .stat-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(226, 167, 18, 0.12);
            background: #FFFFFF;
            border-color: #e2a712;
        }

        .stat-icon {
            margin-bottom: 15px;
        }

        .stat-icon i {
            font-size: 2.8rem;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .stat-item:hover .stat-icon i {
            transform: scale(1.15) rotate(5deg);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .stat-number {
            font-size: clamp(22px, 3vw, 35px);
            font-weight: 700;
            color: #e2a712;
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            font-size: clamp(13px, 2vw, 15px);
            color: #5a5a5a;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .stats-bar {
                padding: 30px 20px;
            }
            
            .stat-item {
                padding: 15px 10px;
            }
            
            .stat-icon i {
                font-size: 2.2rem;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
        }

        /* Modal Styles */
        .stat-detail-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: #2B2B2B !important;
            color: #ffffff !important;
            border-bottom: none;
            border-radius: 0 !important;
            margin: 0;
        }

        :root {
            --primary-color: #e2a712;
            --secondary-color: #c8940e;
            --success-color: #e2a712;
            --background-color: #FFFFFF;
            --card-background: #FFFFFF;
            --text-primary: #333333;
            --text-secondary: #5a5a5a;
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
        /* Visual Improvements */
        .header {
            padding: 10px 0 !important;
            transition: all 0.3s ease !important;
        }

        .scrolled .header {
            padding: 5px 0 !important;
        }

        .hero {
            padding: 140px 0 100px 0 !important;
        }

        .hero h1 {
            font-size: 3.5rem !important;
            font-weight: 800 !important;
            letter-spacing: -2px;
            margin-bottom: 25px;
            line-height: 1.1;
            color: #ffffff !important;
        }

        .hero-subtitle,
        .hero-desc,
        .hero p {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 30px;
            color: #ffffff;
        }

        .hero .btn-get-started,
        .hero .btn-check-travel {
            padding: 14px 35px !important;
            border-radius: 50px !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .hero .btn-get-started {
            margin-right: 15px;
        }

        .hero .btn-get-started:hover,
        .hero .btn-check-travel:hover {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: #ffffff !important;
            color: #ffffff !important;
        }

        /* Form Improvements */
        .form-control::placeholder {
            color: #6c757d !important;
            opacity: 1; /* Firefox */
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(226, 167, 18, 0.2);
        }

        /* Micro-interactions */
        .btn-success {
            transition: all 0.3s ease !important;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        /* Logo Refinement */
        .header .logo .site-logo {
            height: 85px;
            max-height: 85px;
            width: auto;
        }

        @media (max-width: 576px) {
            .header .logo .site-logo {
                height: 64px;
                max-height: 64px;
            }
        }

        /* Statistik Mobile Refinement */
        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2rem !important;
                line-height: 1.2 !important;
                letter-spacing: -1px;
            }
            .hero-subtitle,
            .hero p {
                font-size: 1rem;
            }
            .stat-item {
                margin-bottom: 10px;
                padding: 12px 8px !important;
            }
            .stat-icon i {
                font-size: 1.8rem !important;
            }
            .stat-number {
                font-size: 1.5rem !important;
            }
            .stats-bar {
                padding: 20px 10px !important;
            }
        }

        /* Branding — page-specific only; base tokens in public-theme.css */
        section, .section {
            padding: min(5vw, 60px) 0 !important;
        }

        .section-title {
            padding-bottom: min(4vw, 50px) !important;
        }

        .riwayat-pengaduan-panel .table {
            margin-bottom: 0;
        }

        .riwayat-pengaduan-panel .table thead th {
            font-size: 0.72rem;
            padding: 0.65rem 0.75rem;
            border-bottom-width: 1px;
        }

        .riwayat-pengaduan-panel .table tbody td {
            padding: 0.7rem 0.75rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .riwayat-pengaduan-panel .col-aksi {
            width: 1%;
            white-space: nowrap;
            text-align: center;
        }

        .riwayat-pengaduan-panel .col-hal {
            max-width: 320px;
        }

        .riwayat-pengaduan-panel .col-hal p {
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: var(--phu-text-muted, #6c757d);
            line-height: 1.45;
        }

        .btn-riwayat-pdf {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0.2rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.2;
            border-radius: 6px;
            min-height: auto;
            height: auto;
        }

        .riwayat-pengaduan-panel .pagination {
            margin-bottom: 0;
        }

        .riwayat-pengaduan-panel .pagination .page-link {
            font-size: 0.8rem;
            padding: 0.35rem 0.65rem;
            color: var(--phu-accent, #e2a712);
            border-color: var(--phu-border, #dee2e6);
            background-color: #fff;
        }

        .riwayat-pengaduan-panel .pagination .page-link:hover,
        .riwayat-pengaduan-panel .pagination .page-link:focus,
        .riwayat-pengaduan-panel .pagination .page-link:focus-visible {
            background-color: var(--phu-accent, #e2a712);
            border-color: var(--phu-accent, #e2a712);
            color: #fff;
        }

        .riwayat-pengaduan-panel .pagination .page-item.active .page-link {
            background-color: var(--phu-accent, #e2a712);
            border-color: var(--phu-accent, #e2a712);
            color: #fff;
        }

        .riwayat-pengaduan-meta {
            font-size: 0.8rem;
            color: var(--phu-text-muted, #6c757d);
        }
    </style>
    @include('partials.public-trust-styles')
</head>

<body class="index-page">
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}" class="site-logo" height="85">
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
                        <h1>Mengawal Kepatuhan, Melindungi Jemaah.</h1>
                        <p class="hero-subtitle">Sistem Pengawasan Haji dan Umrah Kanwil NTB</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="btn-get-started">Masuk</a>
                            <a href="#stats" class="btn-check-travel">Cek Travel</a>
                        </div>
                    </div>
                </div>
            </div>

            <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                viewBox="0 24 150 28" preserveAspectRatio="none">
                <defs>
                    <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
                    </path>
                </defs>
                <g class="wave1">
                    <use xlink:href="#wave-path" x="50" y="3" fill="rgba(226, 167, 18, .12)"></use>
                </g>
                <g class="wave2">
                    <use xlink:href="#wave-path" x="50" y="0" fill="rgba(226, 167, 18, .22)"></use>
                </g>
                <g class="wave3">
                    <use xlink:href="#wave-path" x="50" y="9" fill="#fff"></use>
                </g>
            </svg>
        </section>
        <!-- /Hero Section -->


        <!-- About Section -->
        <section id="about" class="about section">
            <div class="container section-title" data-aos="fade-up">
                <h2>5 Pasti Umroh</h2>
                <div>
                    <span>Jangan Tergiur Harga Murah</span>
                    <span class="description-title">Pastikan 5 Hal Ini!</span>
                </div>
            </div>

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4 justify-content-center">
                    <!-- Pasti 1 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon-box phu-pasti-card">
                            <div class="phu-pasti-card__head">
                                <span class="phu-pasti-card__num">01</span>
                                <div class="phu-pasti-card__icon"><i class="bi bi-shield-check"></i></div>
                            </div>
                            <h3>PASTIKAN Travel Umrah Berizin Kementerian Haji.</h3>
                            <p>
                                Travel harus memiliki izin umrah agar terjamin perlindungan, pelayanan, dan bimbingan selama di Tanah Suci.
                            </p>
                        </div>
                    </div>

                    <!-- Pasti 2 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box phu-pasti-card">
                            <div class="phu-pasti-card__head">
                                <span class="phu-pasti-card__num">02</span>
                                <div class="phu-pasti-card__icon"><i class="bi bi-airplane"></i></div>
                            </div>
                            <h3>PASTIKAN Tiket Pesawat dan Jadwal Penerbangan.</h3>
                            <p>
                                Maskapai penerbangannya harus jelas, jadwal berangkatnya pasti, tiketnya harus pulang-pergi, dan hanya satu kali transit dengan maskapai penerbangan yang sama.
                            </p>
                        </div>
                    </div>

                    <!-- Pasti 3 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="icon-box phu-pasti-card">
                            <div class="phu-pasti-card__head">
                                <span class="phu-pasti-card__num">03</span>
                                <div class="phu-pasti-card__icon"><i class="bi bi-clipboard-pulse"></i></div>
                            </div>
                            <h3>PASTIKAN Harga dan Paket Layanannya.</h3>
                            <p>
                                Jangan tergiur harga murah, rincian harga paket harus rasional. Layanan terdiri dari konsumsi, transportasi, manasik, petugas, dan asuransi.
                            </p>
                        </div>
                    </div>

                    <!-- Pasti 4 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="icon-box phu-pasti-card">
                            <div class="phu-pasti-card__head">
                                <span class="phu-pasti-card__num">04</span>
                                <div class="phu-pasti-card__icon"><i class="bi bi-buildings"></i></div>
                            </div>
                            <h3>PASTIKAN Akomodasi (Hotel) di Arab Saudi.</h3>
                            <p>
                                Hotel tempat menginap minimal bintang 3 dan jarak dari tempat ibadah maksimal 1 km (untuk hotel non-bintang/standar).
                            </p>
                        </div>
                    </div>

                    <!-- Pasti 5 -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="icon-box phu-pasti-card">
                            <div class="phu-pasti-card__head">
                                <span class="phu-pasti-card__num">05</span>
                                <div class="phu-pasti-card__icon"><i class="bi bi-credit-card-2-front"></i></div>
                            </div>
                            <h3>PASTIKAN Visanya</h3>
                            <p>
                                Visa harus selesai minimal 3 hari sebelum keberangkatan. Pastikan jenis visa sesuai dengan peruntukannya (Visa Umrah).
                            </p>
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

        <!-- Stats Section -->
        <section id="stats" class="stats-section section light-background">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Statistik</h2>
                <div>
                    <span>Data</span>
                    <span class="description-title">Terbaru</span>
                </div>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row">
                    <div class="col-12">
                        <div class="stats-bar">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <div class="stat-item" data-bs-toggle="modal" data-bs-target="#travelModal">
                                        <div class="stat-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-number">{{ $stats['travelCount'] ?: 'Menunggu Data' }}</h3>
                                            <p class="stat-label">Travel Berizin</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="stat-item" data-bs-toggle="modal" data-bs-target="#jamaahModal">
                                        <div class="stat-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-number">{{ ($stats['jamaahHajiCount'] + $stats['jamaahUmrahCount']) ?: 'Menunggu Data' }}</h3>
                                            <p class="stat-label">Total Jamaah</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="stat-item" data-bs-toggle="modal" data-bs-target="#maskapaiModal">
                                        <div class="stat-icon">
                                            <i class="fas fa-plane"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-number">{{ $stats['airlineCount'] ?: 'Menunggu Data' }}</h3>
                                            <p class="stat-label">Maskapai</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="stat-item" data-bs-toggle="modal" data-bs-target="#kabupatenModal">
                                        <div class="stat-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-number">{{ $allKabupatens->count() }}</h3>
                                            <p class="stat-label">Kabupaten</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Stats Section -->

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
                <div class="row gy-5 icon-boxes">
                    <!-- FAQ Section -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box">
                            <i class="bi bi-question-circle"></i>
                            <h3>FAQ (Frequently Asked Questions)</h3>
                            <p>
                                Temukan jawaban untuk pertanyaan yang sering diajukan tentang PANTAU, cara melihat jadwal keberangkatan, dan prosedur pengaduan.
                            </p>
                            <div class="mt-3">
                                <button class="btn btn-card btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse">
                                    Lihat FAQ <i class="bi bi-chevron-down ms-1"></i>
                                </button>
                            </div>
                            <div class="collapse mt-3" id="faqCollapse">
                                <div class="card card-body border-0 bg-light">
                                    <div class="mb-3">
                                        <strong>Q: Apa itu PANTAU?</strong><br>
                                        <small class="text-muted">A: PANTAU adalah platform digital Kanwil Kementerian Haji dan Umroh NTB untuk mengelola dan memantau kegiatan haji dan umrah di wilayah NTB.</small>
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
                                <button class="btn btn-card btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#pengaduanCollapse">
                                    Lihat Detail <i class="bi bi-chevron-down ms-1"></i>
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
                                            @include('partials.kanwil-contact', ['variant' => 'inline'])
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
                                Pelajari cara menggunakan PANTAU dengan panduan lengkap untuk setiap fitur yang tersedia.
                            </p>
                            <div class="mt-3">
                                <button class="btn btn-card btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panduanCollapse">
                                    Lihat Panduan <i class="bi bi-chevron-down ms-1"></i>
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
                <h2>Tentang</h2>
                <div>
                    <span>Sistem</span>
                    <span class="description-title">PANTAU</span>
                </div>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row gy-4 align-items-center features-item">
                    <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="100">
                        <img src="{{ asset('img/hero-2.png') }}" class="img-fluid" alt="" />
                    </div>
                    <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
                        <h3>Apa itu PANTAU?</h3>
                        <p>
                            <strong>PANTAU</strong> adalah sistem pengawasan digital Kanwil Kementerian Haji dan Umroh NTB.
                            Platform ini membantu masyarakat, travel, dan petugas memantau kepatuhan penyelenggara
                            perjalanan ibadah haji dan umrah di seluruh wilayah Nusa Tenggara Barat.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Cek jadwal keberangkatan jamaah secara terbuka</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Lihat daftar travel berizin dan indeks kepercayaan</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Ajukan pengaduan jika ada masalah dengan travel</span>
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
                        <h3>Untuk Siapa PANTAU Digunakan?</h3>
                        <p>
                            Sistem ini menghubungkan semua pihak yang terlibat dalam perjalanan ibadah,
                            dari calon jamaah hingga petugas Kanwil, dalam satu platform yang sama.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i>
                                <span><strong>Masyarakat &amp; jamaah</strong>: cek travel, jadwal keberangkatan, dan ajukan pengaduan</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span><strong>Travel berizin</strong>: kelola data jamaah dan ajukan keberangkatan</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span><strong>Petugas Kanwil</strong>: pantau kepatuhan, pengawasan, dan tindak lanjut pengaduan</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Features Item -->

                <div class="row gy-4 align-items-center features-item">
                    <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out">
                        <img src="{{ asset('img/hero-4.png') }}" class="img-fluid" alt="" />
                    </div>
                    <div class="col-md-7" data-aos="fade-up">
                        <h3>Perlindungan Jamaah, Transparan</h3>
                        <p>
                            PANTAU dirancang agar masyarakat bisa memilih travel dengan lebih aman
                            dan petugas bisa mengawasi lebih cepat jika ada pelanggaran.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Data travel hanya menampilkan penyelenggara berizin resmi</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Indeks kepercayaan membantu Anda menilai rekam jejak travel</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Pengaduan online dan bisa dilacak hingga selesai</span>
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
                        <h3>Informasi Terbuka, Mudah Diakses</h3>
                        <p>
                            Semua informasi penting, dari statistik travel berizin, jadwal keberangkatan,
                            hingga status pengaduan, tersedia di halaman ini tanpa perlu login.
                        </p>
                        <p>
                            Untuk petugas dan travel, tersedia dashboard khusus setelah masuk ke sistem
                            guna mengelola data, pengawasan, dan tindak lanjut sesuai peran masing-masing.
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
                <!-- Validation Summary - will be shown dynamically -->
                <div id="validation-summary-welcome" class="alert alert-danger d-none" data-aos="fade-up" data-aos-delay="150" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>Mohon lengkapi data berikut:</strong>
                            <ul id="validation-errors-list-welcome" class="mb-0 mt-2">
                            </ul>
                        </div>
                    </div>
                </div>

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
                <div class="tab-content phu-form-panel" id="pengaduanTabContent">
                    <!-- Tab 1: Form Pengaduan -->
                    <div class="tab-pane fade show active" id="form-pengaduan" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-4">
                                @include('partials.kanwil-contact', ['variant' => 'form-sidebar'])
                            </div>

                            <div class="col-lg-8">
                                <form id="pengaduanForm" action="{{ route('pengaduan.store-public') }}" method="post"
                                    class="php-email-form" data-aos="fade-up" data-aos-delay="200"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row gy-4">
                                        <div class="col-md-6">
                                            <input type="text" name="nama_pengadu" id="nama_pengadu_welcome" class="form-control"
                                                placeholder="Nama Pengadu" required value="{{ old('nama_pengadu') }}" />
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-control" name="travels_id" id="travels_id_welcome" required>
                                                <option value="">-- Pilih Travel --</option>
                                                @foreach ($travels as $travel)
                                                    @php $trust = $travel->trust ?? []; @endphp
                                                    <option value="{{ $travel->id }}"
                                                        data-trust-label="{{ $trust['label'] ?? '' }}"
                                                        data-trust-score="{{ $trust['score'] ?? '' }}"
                                                        data-trust-has="{{ ($trust['has_data'] ?? false) ? '1' : '0' }}"
                                                        data-profile-url="{{ route('travel.public.show', $travel->public_uuid) }}"
                                                        {{ old('travels_id') == $travel->id ? 'selected' : '' }}>
                                                        {{ $travel->Penyelenggara }} ({{ $travel->kab_kota }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="trustHintWelcome" class="trust-hint-box" aria-live="polite"></div>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-12">
                                            <textarea class="form-control" name="hal_aduan" id="hal_aduan_welcome" rows="6" placeholder="Hal yang Diadukan" required>{{ old('hal_aduan') }}</textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-12">
                                            <input type="file" class="form-control" name="berkas_aduan" id="berkas_aduan_welcome" accept=".pdf,.jpg,.jpeg,.png" />
                                            <small class="text-muted mt-1">File maksimal 2MB. Format yang diperbolehkan: PDF, JPG, PNG, DOC, DOCX</small>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-12 text-center">
                                            <div class="loading">Loading</div>
                                            <div class="error-message"></div>
                                            <div class="sent-message">Pengaduan Anda telah terkirim. Terima kasih!</div>
                                            <div class="mt-3">
                                                <button type="button"
                                                    class="btn btn-success rounded-pill px-4 py-3"
                                                    style="font-weight: 600; text-transform: uppercase; letter-spacing: 1px;" onclick="confirmSubmit()">Kirim
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
                                <div class="card border-0 shadow-sm riwayat-pengaduan-panel" data-aos="fade-up" data-aos-delay="200">
                                    <div class="card-body p-3 p-md-4">
                                        <div id="riwayatContent">
                                            <div class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-3 text-muted mb-0">Memuat data pengaduan...</p>
                                            </div>
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

    @include('partials.kanwil-contact', ['variant' => 'footer-full'])

    <!-- Stats Detail Modals -->
    <!-- Travel Modal -->
    <div class="modal fade" id="travelModal" tabindex="-1" aria-labelledby="travelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="travelModalLabel">
                        <i class="fas fa-building me-2"></i>Detail Travel Berizin
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-detail-card">
                                    <h6 class="text-primary">Travel Pusat</h6>
                                    <h3 class="text-success">{{ $travelPusat->count() }}</h3>
                                    <p class="text-muted">Travel pusat berizin</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-detail-card">
                                    <h6 class="text-primary">Travel Cabang</h6>
                                    <h3 class="text-info">{{ $travelCabang->count() }}</h3>
                                    <p class="text-muted">Travel cabang berizin</p>
                                </div>
                            </div>
                        </div>
                    <hr>
                    <div class="mt-3">
                        <h6>Distribusi Travel Pusat per Kabupaten/Kota:</h6>
                        <div class="row">
                            @foreach($travelPusat->groupBy('kab_kota') as $kabupaten => $travelList)
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $kabupaten }}: <strong>{{ $travelList->count() }} travel pusat</strong>
                                </small>
                            </div>
                            @endforeach
                        </div>
                        <h6 class="mt-3">Distribusi Travel Cabang per Kabupaten/Kota:</h6>
                        <div class="row">
                            @foreach($travelCabang->groupBy('kabupaten') as $kabupaten => $travelList)
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $kabupaten }}: <strong>{{ $travelList->count() }} travel cabang</strong>
                                </small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('travel.public') }}" class="btn btn-primary">Lihat Daftar Travel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Jamaah Modal -->
    <div class="modal fade" id="jamaahModal" tabindex="-1" aria-labelledby="jamaahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jamaahModalLabel">
                        <i class="fas fa-users me-2"></i>Detail Data Jamaah
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-detail-card">
                                <h6 class="text-primary">Jamaah Haji</h6>
                                <h3 class="text-warning">{{ $stats['jamaahHajiCount'] > 0 ? $stats['jamaahHajiCount'] : 'Belum Ada Data' }}</h3>
                                <p class="text-muted">Jamaah haji khusus</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-detail-card">
                                <h6 class="text-primary">Jamaah Umrah</h6>
                                <h3 class="text-success">{{ $stats['jamaahUmrahCount'] > 0 ? $stats['jamaahUmrahCount'] : 'Belum Ada Data' }}</h3>
                                <p class="text-muted">Jamaah umrah</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h4 class="text-primary">Total Jamaah: {{ ($stats['jamaahHajiCount'] + $stats['jamaahUmrahCount']) > 0 ? ($stats['jamaahHajiCount'] + $stats['jamaahUmrahCount']) : 'Belum Ada Data' }}</h4>
                        <p class="text-muted">Terdaftar dalam PANTAU</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Maskapai Modal -->
    <div class="modal fade" id="maskapaiModal" tabindex="-1" aria-labelledby="maskapaiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maskapaiModalLabel">
                        <i class="fas fa-plane me-2"></i>Detail Maskapai
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h3 class="text-primary">{{ $stats['airlineCount'] > 0 ? $stats['airlineCount'] : 'Belum Ada Data' }}</h3>
                        <p class="text-muted">Maskapai yang bekerja sama</p>
                    </div>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> Maskapai yang tersedia untuk penerbangan haji dan umrah dari wilayah NTB.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Kabupaten Modal -->
    <div class="modal fade" id="kabupatenModal" tabindex="-1" aria-labelledby="kabupatenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kabupatenModalLabel">
                        <i class="fas fa-map-marker-alt me-2"></i>Detail Kabupaten/Kota
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h3 class="text-primary">{{ $allKabupatens->count() }}</h3>
                        <p class="text-muted">Kabupaten/Kota yang terlayani</p>
                    </div>
                    <hr>
                    <h6>Wilayah Terlayani:</h6>
                    <div class="row">
                        @foreach($allKabupatens as $kabupaten)
                        @php
                            $pusatCount = $travelPusat->where('kab_kota', $kabupaten)->count();
                            $cabangCount = $travelCabang->where('kabupaten', $kabupaten)->count();
                        @endphp
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-map-marker-alt me-2 text-success"></i>{{ $kabupaten }}</span>
                                <span>
                                    @if($pusatCount > 0)
                                        <span class="badge bg-success me-1">{{ $pusatCount }} pusat</span>
                                    @endif
                                    @if($cabangCount > 0)
                                        <span class="badge bg-info">{{ $cabangCount }} cabang</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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
                            ${arg.event.extendedProps.days} hari
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
        <h3 class="mb-4">
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
                    ${event.extendedProps.days} Hari
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
            // Reset validation states first
            resetWelcomeValidationStates();
            
            // Validate form
            const validationErrors = validateWelcomeForm();
            
            if (validationErrors.length > 0) {
                showWelcomeValidationErrors(validationErrors);
                scrollToFirstWelcomeError();
                return;
            }
            
            // If validation passes, show confirmation dialog
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

        function validateWelcomeForm() {
            const errors = [];
            
            // Required fields
            const requiredFields = [
                { id: 'nama_pengadu_welcome', name: 'Nama Pengadu' },
                { id: 'travels_id_welcome', name: 'Travel' },
                { id: 'hal_aduan_welcome', name: 'Hal yang Diadukan' }
            ];
            
            // Validate required fields
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                const value = element.value.trim();
                
                if (!value) {
                    errors.push({
                        field: field.id,
                        message: `${field.name} wajib diisi`,
                        element: element
                    });
                }
            });
            
            // Validate file size if uploaded
            const fileInput = document.getElementById('berkas_aduan_welcome');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    errors.push({
                        field: 'berkas_aduan_welcome',
                        message: 'Ukuran file maksimal 2MB',
                        element: fileInput
                    });
                }
            }
            
            return errors;
        }

        function showWelcomeValidationErrors(errors) {
            // Show validation summary
            const validationErrorsList = document.getElementById('validation-errors-list-welcome');
            validationErrorsList.innerHTML = '';
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error.message;
                validationErrorsList.appendChild(li);
            });
            document.getElementById('validation-summary-welcome').classList.remove('d-none');
            
            // Mark fields as invalid
            errors.forEach(error => {
                const element = error.element;
                element.classList.add('is-invalid');
                
                const feedback = element.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = error.message;
                }
            });
        }

        function resetWelcomeValidationStates() {
            // Hide validation summary
            document.getElementById('validation-summary-welcome').classList.add('d-none');
            
            // Remove validation classes from all fields
            const form = document.getElementById('pengaduanForm');
            form.querySelectorAll('.form-control').forEach(field => {
                field.classList.remove('is-invalid', 'is-valid');
            });
            
            // Clear error messages
            form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                feedback.textContent = '';
            });
        }

        function scrollToFirstWelcomeError() {
            const firstError = document.getElementById('pengaduanForm').querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstError.focus();
            }
        }

        const RIWAYAT_PER_PAGE = 6;
        let riwayatCurrentPage = 1;

        // Tab navigation handler and form validation
        document.addEventListener('DOMContentLoaded', function() {
            const riwayatTab = document.getElementById('riwayat-tab');
            if (riwayatTab) {
                riwayatTab.addEventListener('shown.bs.tab', function() {
                    loadRiwayatPengaduan(riwayatCurrentPage);
                });
            }

            // Add real-time validation for welcome form
            const welcomeForm = document.getElementById('pengaduanForm');
            if (welcomeForm) {
                welcomeForm.querySelectorAll('.form-control').forEach(field => {
                    field.addEventListener('blur', function() {
                        validateWelcomeField(this);
                    });
                    
                    field.addEventListener('input', function() {
                        if (this.classList.contains('is-invalid')) {
                            validateWelcomeField(this);
                        }
                    });
                });
            }

            const travelSelect = document.getElementById('travels_id_welcome');
            const trustHint = document.getElementById('trustHintWelcome');

            function updateTrustHint() {
                if (!travelSelect || !trustHint) return;

                const option = travelSelect.options[travelSelect.selectedIndex];
                if (!option || !option.value) {
                    trustHint.classList.remove('is-visible');
                    trustHint.innerHTML = '';
                    return;
                }

                if (option.dataset.trustHas !== '1') {
                    trustHint.classList.add('is-visible');
                    trustHint.innerHTML = '<span class="trust-hint-box__label">Info kepercayaan:</span> Data indeks kepercayaan untuk travel ini belum tersedia.';
                    return;
                }

                const label = option.dataset.trustLabel || 'Belum diketahui';
                const score = option.dataset.trustScore || 'Tidak ada';
                const profileUrl = option.dataset.profileUrl || '#';

                trustHint.classList.add('is-visible');
                trustHint.innerHTML = `
                    <span class="trust-hint-box__label">Indeks Kepercayaan:</span>
                    ${label} (${score}/100).
                    <a href="${profileUrl}" class="text-success fw-semibold" target="_blank" rel="noopener">Lihat penjelasan lengkap</a>
                `;
            }

            if (travelSelect) {
                travelSelect.addEventListener('change', updateTrustHint);
                updateTrustHint();
            }
        });

        function validateWelcomeField(field) {
            const value = field.value.trim();
            const fieldId = field.id;
            
            // Required fields
            const requiredFields = [
                { id: 'nama_pengadu_welcome', name: 'Nama Pengadu' },
                { id: 'travels_id_welcome', name: 'Travel' },
                { id: 'hal_aduan_welcome', name: 'Hal yang Diadukan' }
            ];
            
            // Check if it's a required field
            const isRequired = requiredFields.some(f => f.id === fieldId);
            
            if (isRequired && !value) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                
                const feedback = field.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    const fieldName = requiredFields.find(f => f.id === fieldId).name;
                    feedback.textContent = `${fieldName} wajib diisi`;
                }
            } else if (fieldId === 'berkas_aduan_welcome' && field.files.length > 0) {
                // Validate file size
                const file = field.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = 'Ukuran file maksimal 2MB';
                    }
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = '';
                    }
                }
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                
                const feedback = field.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = '';
                }
            }
        }

        function loadRiwayatPengaduan(page = 1) {
            const riwayatContent = document.getElementById('riwayatContent');
            riwayatCurrentPage = page;

            riwayatContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted mb-0">Memuat data pengaduan...</p>
                </div>
            `;

            fetch(`/api/pengaduan-completed?page=${page}&per_page=${RIWAYAT_PER_PAGE}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(payload => {
                    const data = payload.data || [];
                    const meta = payload.meta || {};

                    if (data.length > 0) {
                        const rowOffset = meta.from ? meta.from - 1 : (page - 1) * RIWAYAT_PER_PAGE;
                        let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
                        html += '<thead><tr>';
                        html += '<th class="text-center" style="width:3rem;">No</th>';
                        html += '<th>Travel</th>';
                        html += '<th class="col-hal">Hal Pengaduan</th>';
                        html += '<th style="width:7.5rem;">Tanggal Selesai</th>';
                        html += '<th class="col-aksi">Aksi</th>';
                        html += '</tr></thead><tbody>';

                        data.forEach((item, index) => {
                            const travelName = item.travel ? item.travel.Penyelenggara : 'Tidak diketahui';
                            const halAduan = item.hal_aduan ? item.hal_aduan : 'Tidak ada detail';
                            const completedDate = item.completed_at
                                ? new Date(item.completed_at).toLocaleDateString('id-ID')
                                : 'Tidak diketahui';
                            const pdfUrl = item.public_token
                                ? `/public/pengaduan/${item.public_token}/download-pdf`
                                : '#';

                            html += `<tr>
                                <td class="text-center text-muted">${rowOffset + index + 1}</td>
                                <td><span class="phu-table-link">${travelName}</span></td>
                                <td class="col-hal"><p>${halAduan}</p></td>
                                <td><small class="text-muted">${completedDate}</small></td>
                                <td class="col-aksi">
                                    <a href="${pdfUrl}" class="btn btn-outline-success btn-riwayat-pdf" target="_blank" rel="noopener noreferrer" title="Unduh PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>PDF</span>
                                    </a>
                                </td>
                            </tr>`;
                        });

                        html += '</tbody></table></div>';
                        html += renderRiwayatPagination(meta);
                        riwayatContent.innerHTML = html;
                    } else {
                        riwayatContent.innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">Belum ada pengaduan yang selesai diproses</h5>
                                <p class="text-muted mb-0">Pengaduan yang sudah selesai akan ditampilkan di sini</p>
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
                            <button type="button" class="btn btn-primary btn-sm" onclick="loadRiwayatPengaduan(${page})">
                                <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                            </button>
                        </div>
                    `;
                });
        }

        function renderRiwayatPagination(meta) {
            const current = meta.current_page || 1;
            const last = meta.last_page || 1;
            const total = meta.total || 0;
            const from = meta.from || 0;
            const to = meta.to || 0;

            if (last <= 1) {
                return total > 0
                    ? `<div class="d-flex justify-content-between align-items-center mt-3 riwayat-pengaduan-meta">
                            <span>Menampilkan ${from}–${to} dari ${total} pengaduan</span>
                       </div>`
                    : '';
            }

            let pages = '';
            const windowSize = 2;
            const start = Math.max(1, current - windowSize);
            const end = Math.min(last, current + windowSize);

            if (current > 1) {
                pages += `<li class="page-item"><button type="button" class="page-link" onclick="loadRiwayatPengaduan(${current - 1})" aria-label="Sebelumnya">&laquo;</button></li>`;
            }

            for (let i = start; i <= end; i++) {
                pages += `<li class="page-item ${i === current ? 'active' : ''}">
                    <button type="button" class="page-link" onclick="loadRiwayatPengaduan(${i})">${i}</button>
                </li>`;
            }

            if (current < last) {
                pages += `<li class="page-item"><button type="button" class="page-link" onclick="loadRiwayatPengaduan(${current + 1})" aria-label="Berikutnya">&raquo;</button></li>`;
            }

            return `
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
                    <span class="riwayat-pengaduan-meta">Menampilkan ${from}–${to} dari ${total} pengaduan</span>
                    <nav aria-label="Paginasi riwayat pengaduan">
                        <ul class="pagination pagination-sm mb-0">${pages}</ul>
                    </nav>
                </div>
            `;
        }

        // Keep the old function for backward compatibility (if needed)
        function openPengaduanModal() {
            const riwayatTab = document.getElementById('riwayat-tab');
            if (riwayatTab) {
                bootstrap.Tab.getOrCreateInstance(riwayatTab).show();
            }
        }
    </script>

</body>

</html>

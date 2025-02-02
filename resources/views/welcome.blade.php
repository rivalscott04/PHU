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
    <!-- Main CSS File -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />

    <style>
        .travel-companies {
            background-color: #f8f9fa;
        }

        .travel-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .travel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
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
            align-items: center;
            gap: 0.75rem;
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
                    <li><a href="#travel">Travel</a></li>
                    <li><a href="#details">Jadwal Keberangkatan</a></li>
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
                            <a href="#about" class="btn-get-started">Masuk</a>
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
                        <h3>About Us</h3>
                        <h2>Ducimus rerum libero reprehenderit cumque</h2>
                        <p>
                            Ipsa sint sit. Quis ducimus tempore dolores
                            impedit et dolor cumque alias maxime. Enim
                            reiciendis minus et rerum hic non. Dicta quas
                            cum quia maiores iure. Quidem nulla qui
                            assumenda incidunt voluptatem tempora deleniti
                            soluta.
                        </p>
                        <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
                    </div>

                    <div class="col-xl-7">
                        <div class="row gy-4 icon-boxes">
                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                                <div class="icon-box">
                                    <i class="bi bi-buildings"></i>
                                    <h3>Eius provident</h3>
                                    <p>
                                        Magni repellendus vel ullam hic
                                        officia accusantium ipsa dolor omnis
                                        dolor voluptatem
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon-box">
                                    <i class="bi bi-clipboard-pulse"></i>
                                    <h3>Rerum aperiam</h3>
                                    <p>
                                        Autem saepe animi et aut aspernatur
                                        culpa facere. Rerum saepe rerum
                                        voluptates quia
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                                <div class="icon-box">
                                    <i class="bi bi-command"></i>
                                    <h3>Veniam omnis</h3>
                                    <p>
                                        Omnis perferendis molestias culpa
                                        sed. Recusandae quas possimus. Quod
                                        consequatur corrupti
                                    </p>
                                </div>
                            </div>
                            <!-- End Icon Box -->

                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                                <div class="icon-box">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    <h3>Delares sapiente</h3>
                                    <p>
                                        Sint et dolor voluptas minus
                                        possimus nostrum. Reiciendis commodi
                                        eligendi omnis quideme lorenda
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

        <section class="travel-companies py-5" id="travel">
            <div class="container">
                <div class="container section-title" data-aos="fade-up">
                    <h2>Travel</h2>
                    <div>
                        <span>Travel Tersedia</span>
                        <span class="description-title"></span>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach ($travelData as $index => $travel)
                        <div class="col-lg-3 col-md-6 travel-item {{ $index >= 4 ? 'hidden-item' : '' }}">
                            <div class="travel-card">
                                <div class="company-header">
                                    <div class="company-icon">
                                        <i class="bi bi-building-fill"></i>
                                    </div>
                                </div>

                                <h3 class="company-name">{{ $travel->Penyelenggara }}</h3>

                                <div class="company-details">
                                    <div class="accreditation">
                                        <i class="bi bi-award-fill"></i>
                                        <span>Akreditasi: {{ $travel->Jml_Akreditasi ?: '-' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="bi bi-telephone-fill"></i>
                                        <span>{{ $travel->Telepon }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>{{ $travel->kab_kota }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (count($travelData) > 4)
                    <div class="text-center mt-4">
                        <button id="showMoreBtn" class="btn btn-outline-primary">Show More</button>
                    </div>
                @endif
            </div>
        </section>

        <!-- Stats Section -->
        <section id="stats" class="stats section light-background">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-building-fill"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="{{ $stats['travelCount'] }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Jumlah Travel</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->

                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-people"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="{{ $stats['jamaahCount'] }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Jumlah Jamaah</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->

                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-airplane"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="{{ $stats['airlineCount'] }}"
                                data-purecounter-duration="1" class="purecounter"></span>
                            <p>Jumlah Airline</p>
                        </div>
                    </div>
                    <!-- End Stats Item -->
                </div>
            </div>
        </section>
        <!-- /Stats Section -->

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
                            Voluptatem dignissimos provident quasi corporis
                            voluptates sit assumenda.
                        </h3>
                        <p class="fst-italic">
                            Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore magna aliqua.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i><span>
                                    Ullamco laboris nisi ut aliquip ex ea
                                    commodo consequat.</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Duis aute irure dolor in reprehenderit
                                    in voluptate velit.</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Ullam est qui quos consequatur eos
                                    accusamus.</span>
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
                        <h3>Corporis temporibus maiores provident</h3>
                        <p class="fst-italic">
                            Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore magna aliqua.
                        </p>
                        <p>
                            Ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in
                            reprehenderit in voluptate velit esse cillum
                            dolore eu fugiat nulla pariatur. Excepteur sint
                            occaecat cupidatat non proident, sunt in culpa
                            qui officia deserunt mollit anim id est laborum
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
                            Sunt consequatur ad ut est nulla consectetur
                            reiciendis animi voluptas
                        </h3>
                        <p>
                            Cupiditate placeat cupiditate placeat est ipsam
                            culpa. Delectus quia minima quod. Sunt saepe
                            odit aut quia voluptatem hic voluptas dolor
                            doloremque.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Ullamco laboris nisi ut aliquip ex ea
                                    commodo consequat.</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i><span>
                                    Duis aute irure dolor in reprehenderit
                                    in voluptate velit.</span>
                            </li>
                            <li>
                                <i class="bi bi-check"></i>
                                <span>Facilis ut et voluptatem aperiam. Autem
                                    soluta ad fugiat</span>.
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
                            Quas et necessitatibus eaque impedit ipsum animi
                            consequatur incidunt in
                        </h3>
                        <p class="fst-italic">
                            Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore magna aliqua.
                        </p>
                        <p>
                            Ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in
                            reprehenderit in voluptate velit esse cillum
                            dolore eu fugiat nulla pariatur. Excepteur sint
                            occaecat cupidatat non proident, sunt in culpa
                            qui officia deserunt mollit anim id est laborum
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
                        <form action="{{ route('pengaduan.store') }}" method="post" class="php-email-form"
                            data-aos="fade-up" data-aos-delay="200" enctype="multipart/form-data">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <input type="text" name="nama_pengadu" class="form-control"
                                        placeholder="Nama Pengadu" required value="{{ old('nama_pengadu') }}" />
                                </div>

                                <div class="col-md-6">
                                    <select class="form-control" name="travels_id" required>
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
                                    <textarea class="form-control" name="hal_aduan" rows="6" placeholder="Hal yang Diadukan" required>{{ old('hal_aduan') }}</textarea>
                                </div>

                                <div class="col-md-12">
                                    <input type="file" class="form-control" name="berkas_aduan" />
                                    <small class="text-muted mt-1">File maksimal 2MB</small>
                                </div>

                                <div class="col-md-12 text-center">
                                    <div class="loading">Loading</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">Pengaduan Anda telah terkirim. Terima kasih!</div>

                                    <div class="mt-3">
                                        <button type="submit">Kirim Pengaduan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Contact Section -->
    </main>

    <footer id="footer" class="footer dark-background">
        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="logo d-flex align-items-center">
                        <span class="sitename">UHK Kanwil NTB</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>Jl Udayana No 6</p>
                        <p>Mataram. NTB</p>
                        <p class="mt-3">
                            <strong>Phone:</strong>
                            <span>12345678</span>
                        </p>
                        <p>
                            <strong>Email:</strong>
                            <span>ntb.kemenag@kemenag.go.id</span>
                        </p>
                    </div>
                    <div class="social-links d-flex mt-4">
                        <a href=""><i class="bi bi-twitter-x"></i></a>
                        <a href=""><i class="bi bi-facebook"></i></a>
                        <a href=""><i class="bi bi-instagram"></i></a>
                        <a href=""><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Tautan</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Terms of service</a></li>
                        <li><a href="#">Privacy policy</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Our Services</h4>
                    <ul>
                        <li><a href="#">Web Design</a></li>
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Product Management</a></li>
                        <li><a href="#">Marketing</a></li>
                        <li><a href="#">Graphic Design</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 footer-newsletter">
                    <h4>Our Newsletter</h4>
                    <p>
                        Subscribe to our newsletter and receive the latest
                        news about our products and services!
                    </p>
                    <form action="forms/newsletter.php" method="post" class="php-email-form">
                        <div class="newsletter-form">
                            <input type="email" name="email" /><input type="submit" value="Subscribe" />
                        </div>
                        <div class="loading">Loading</div>
                        <div class="error-message"></div>
                        <div class="sent-message">
                            Your subscription request has been sent. Thank
                            you!
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>
                © <span>Copyright</span>
                <strong class="px-1 sitename">UHK Kanwil NTB</strong>
                <span>All Rights Reserved</span>
            </p>
            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you've purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
                Designed with Love
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
    </script>
</body>

</html>

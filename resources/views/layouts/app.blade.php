<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="PANTAU, Sistem Pengawasan Haji dan Umrah Kanwil NTB" name="description" />
    <meta content="PANTAU" name="author" />
    <!-- App favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css -->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-layout-footer.css') }}?v={{ filemtime(public_path('css/app-layout-footer.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-typography.css') }}?v={{ filemtime(public_path('css/app-typography.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-mobile.css') }}?v={{ filemtime(public_path('css/app-mobile.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/interactive-cursor.css') }}?v={{ filemtime(public_path('css/interactive-cursor.css')) }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- SweetAlert2 CSS -->
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body data-sidebar="dark">
    <!-- <body data-layout="horizontal" data-topbar="dark"> -->
    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Impersonate Banner -->
        @if (app('impersonate')->isImpersonating())
            <div class="impersonate-banner"
                style="position: fixed; top: 0; left: 0; right: 0; background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; padding: 10px; text-align: center; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <strong><i class="bx bx-user-check me-2"></i>You are currently impersonating:
                                {{ auth()->user()->getDisplayName() }}</strong>
                            <small class="d-block">You can see the system from this user's perspective</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('impersonate.leave') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-log-out me-1"></i>
                                Stop Impersonating
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('components.navbar')
        @include('components.sidebar')
        <div class="main-content">
            <div class="page-content">
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
                @if (session('reset_link'))
                    @php($resetLink = session('reset_link'))
                    <div class="alert alert-info" role="alert">
                        <h6 class="mb-1">Tautan set password untuk {{ $resetLink['nama'] }}</h6>
                        <p class="mb-2">Kirim tautan ini hanya ke pemilik akun, yaitu {{ $resetLink['email'] }}
                            @if ($resetLink['nomor_hp'])
                                atau nomor terdaftar {{ $resetLink['nomor_hp'] }}
                            @endif
                            . Tautan berlaku 60 menit, sekali pakai, dan tidak dapat dilihat lagi setelah halaman ini ditutup.
                        </p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="reset-link-value" value="{{ $resetLink['link'] }}" readonly
                                   aria-label="Tautan set password">
                            <button class="btn btn-primary" type="button" id="reset-link-copy">
                                <i class="bx bx-copy me-1"></i> Salin tautan
                            </button>
                        </div>
                        @if ($resetLink['wa_url'])
                            <a href="{{ $resetLink['wa_url'] }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                <i class="bx bxl-whatsapp me-1"></i> Kirim ke WhatsApp {{ $resetLink['nomor_hp'] }}
                            </a>
                        @else
                            <span class="text-muted">Akun ini belum punya nomor HP terdaftar, jadi tautan harus dikirim lewat email.</span>
                        @endif
                    </div>
                    <script>
                        document.getElementById('reset-link-copy').addEventListener('click', function () {
                            var field = document.getElementById('reset-link-value');
                            var button = this;
                            field.select();
                            field.setSelectionRange(0, field.value.length);

                            var done = function () {
                                button.innerHTML = '<i class="bx bx-check me-1"></i> Tautan tersalin';
                            };
                            var failed = function () {
                                button.innerHTML = '<i class="bx bx-error me-1"></i> Gagal menyalin, salin manual';
                            };

                            if (navigator.clipboard) {
                                navigator.clipboard.writeText(field.value).then(done, failed);
                            } else if (document.execCommand('copy')) {
                                done();
                            } else {
                                failed();
                            }
                        });
                    </script>
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
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('components.footer')
        </div>
    </div>

    @include('components.pdf-preview-modal')

    <!-- Scripts -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>

    <!-- apexcharts -->
    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Sweet Alerts js (must match CSS version above) -->
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/confirm-dialogs.js') }}"></script>
    <script src="{{ asset('js/pdf-preview.js') }}"></script>

    <!-- Sweet alert init js-->
    {{-- <script src="{{ asset('js/pages/sweet-alerts.init.js') }}"></script> --}}

    <!-- dashboard init -->
    {{-- <script src="{{ asset('js/pages/dashboard.init.js') }}"></script> --}}

    <script src="{{ asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons examples -->
    <script src="{{ asset('libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('js/pages/datatables.init.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('js/app.js') }}"></script>
    @include('partials.input-limits-script')
    @stack('js')

    <style>
        .impersonate-banner {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(0);
            }
        }

        /* Adjust main content when banner is present */
        @if (app('impersonate')->isImpersonating())
            .main-content {
                margin-top: 60px;
            }
        @endif
    </style>
</body>

</html>

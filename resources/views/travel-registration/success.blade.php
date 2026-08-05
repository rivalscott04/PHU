<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Pendaftaran Terkirim | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-success bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-success p-4">
                                        <h5 class="text-success">Pendaftaran Terkirim</h5>
                                        <p class="mb-0">Menunggu verifikasi Admin Kanwil.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <a href="{{ route('login') }}" class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <div class="text-center mb-4">
                                    <div class="avatar-md mx-auto mb-3">
                                        <span class="avatar-title rounded-circle bg-success font-size-24">
                                            <i class="bx bx-check"></i>
                                        </span>
                                    </div>
                                    <p class="text-muted">
                                        Terima kasih. Data travel Anda sudah kami terima dan sedang
                                        <strong>menunggu verifikasi Admin Kanwil</strong>.
                                        Setelah disetujui, Anda bisa login dengan email/HP dan password yang didaftarkan.
                                    </p>
                                </div>

                                <div class="alert alert-info text-start mb-4">
                                    <h6 class="alert-heading">Langkah selanjutnya</h6>
                                    <ol class="mb-0 ps-3">
                                        <li>Tunggu konfirmasi Admin Kanwil (biasanya 1 s.d. 3 hari kerja)</li>
                                        <li>Setelah disetujui, buka halaman login</li>
                                        <li>Masuk dengan email/HP dan password Anda</li>
                                    </ol>
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('login') }}" class="btn btn-primary waves-effect waves-light">
                                        Ke Halaman Login
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <p class="text-muted mb-0 small">
                            {{ config('app.kanwil.short_name') }} · {{ config('app.kanwil.address') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>

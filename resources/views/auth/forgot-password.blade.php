<!doctype html>
<html lang="id">


<head>

    <meta charset="utf-8" />
    <title>Lupa Password | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="PANTAU, Sistem Pengawasan Haji dan Umrah Kanwil NTB" name="description" />
    <meta content="PANTAU" name="author" />
    <!-- App favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/interactive-cursor.css') }}" rel="stylesheet" type="text/css" />

</head>


<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Lupa Password</h5>
                                        <p>Buat password baru melalui tautan pemulihan akun.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <a href="{{ url('/') }}" class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}"
                                                height="34">
                                        </span>
                                    </div>
                                </a>

                                <a href="{{ url('/') }}" class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}"
                                                height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (! config('auth.password_reset_email_enabled'))
                                    <p>Hubungi petugas Kanwil untuk meminta tautan set password melalui nomor yang terdaftar pada akun Anda.</p>
                                    @include('partials.kanwil-contact', ['variant' => 'support', 'supportStyle' => 'card'])
                                    <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
                                @else
                                <form class="form-horizontal" action="{{ route('password.email') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email akun</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" placeholder="Masukkan email akun Anda"
                                               value="{{ old('email') }}" autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Tautan hanya dikirim ke email yang terdaftar pada akun, dan berlaku 60 menit.</small>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Kirim Tautan</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ route('login') }}" class="text-muted">
                                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke halaman masuk
                                        </a>
                                    </div>

                                    <div class="mt-4">
                                        <p class="text-muted mb-0">Tidak ingat email akun, atau emailnya sudah tidak aktif? Hubungi petugas Kanwil atau Kantor Kemenag Kabupaten/Kota Anda. Petugas dapat menerbitkan tautan dan mengirimkannya ke nomor WhatsApp yang terdaftar pada akun Anda.</p>
                                    </div>

                                    @include('partials.kanwil-contact', ['variant' => 'support', 'supportStyle' => 'card'])
                                </form>
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <div>
                            <p>©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ config('app.kanwil.short_name') }}.<br>
                                <span class="text-muted">{{ config('app.kanwil.address') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end account-pages -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('js/app.js') }}"></script>
    @include('partials.input-limits-script')
</body>



</html>

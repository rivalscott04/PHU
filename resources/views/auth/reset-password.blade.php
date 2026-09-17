<!doctype html>
<html lang="id">


<head>

    <meta charset="utf-8" />
    <title>Buat Password | {{ config('app.name') }}</title>
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
                                        <h5 class="text-primary">Buat Password</h5>
                                        <p>Password ini yang Anda pakai untuk masuk.</p>
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
                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <form class="form-horizontal" action="{{ route('password.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email akun</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="{{ old('email', $email) }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password baru</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password" placeholder="Minimal 8 karakter"
                                                   aria-label="Password baru" aria-describedby="password-addon" autofocus>
                                            <button class="btn btn-light" type="button" id="password-addon"><i
                                                    class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Ulangi password baru</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                               name="password_confirmation" placeholder="Ketik ulang password baru">
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Simpan Password</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ route('login') }}" class="text-muted">
                                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke halaman masuk
                                        </a>
                                    </div>
                                </form>
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

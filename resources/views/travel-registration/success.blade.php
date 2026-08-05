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
                <div class="col-md-8 col-lg-6">
                    <div class="card overflow-hidden text-center">
                        <div class="card-body p-5">
                            <div class="avatar-lg mx-auto mb-4">
                                <span class="avatar-title rounded-circle bg-success-subtle text-success display-4">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                            <h4 class="mb-3">Pendaftaran Berhasil Dikirim!</h4>
                            <p class="text-muted mb-4">
                                Terima kasih. Data travel Anda sudah kami terima dan sedang <strong>menunggu verifikasi Admin Kanwil</strong>.
                                Setelah disetujui, Anda bisa login menggunakan email/HP dan password yang didaftarkan.
                            </p>
                            <div class="alert alert-warning text-start mb-4">
                                <strong>Langkah selanjutnya:</strong>
                                <ol class="mb-0 ps-3 mt-2">
                                    <li>Tunggu konfirmasi dari Admin Kanwil (biasanya 1 s.d. 3 hari kerja)</li>
                                    <li>Setelah disetujui, buka halaman login</li>
                                    <li>Masuk dengan email/HP dan password Anda</li>
                                </ol>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                Ke Halaman Login
                            </a>
                        </div>
                    </div>
                    <p class="text-center text-muted mt-4 mb-0">
                        {{ config('app.kanwil.short_name') }} · {{ config('app.kanwil.address') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

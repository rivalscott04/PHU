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
    <style>
        .travel-success-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(18, 38, 63, 0.08);
        }

        .travel-success-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            background: #34c38f;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }

        .travel-success-steps {
            background: #f8f9ff;
            border: 1px solid #eef1ff;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            text-align: left;
        }

        .travel-success-steps-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .travel-success-steps ol {
            margin-bottom: 0;
            padding-left: 1.25rem;
            color: #74788d;
            font-size: 14px;
        }

        .travel-success-steps li + li {
            margin-top: 0.35rem;
        }
    </style>
</head>
<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="text-center mb-4">
                        <a href="{{ route('login') }}">
                            <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}" height="40">
                        </a>
                    </div>

                    <div class="card travel-success-card overflow-hidden">
                        <div class="card-body p-4 p-md-5 text-center">
                            <div class="travel-success-icon">
                                <i class="bx bx-check"></i>
                            </div>

                            <h4 class="mb-2">Pendaftaran Berhasil Dikirim</h4>
                            <p class="text-muted mb-4">
                                Terima kasih. Data travel Anda sudah kami terima dan sedang
                                <strong>menunggu verifikasi Admin Kanwil</strong>.
                                Setelah disetujui, Anda bisa login dengan email/HP dan password yang didaftarkan.
                            </p>

                            <div class="travel-success-steps mb-4">
                                <div class="travel-success-steps-title">Langkah selanjutnya</div>
                                <ol>
                                    <li>Tunggu konfirmasi Admin Kanwil (biasanya 1 s.d. 3 hari kerja)</li>
                                    <li>Setelah disetujui, buka halaman login</li>
                                    <li>Masuk dengan email/HP dan password Anda</li>
                                </ol>
                            </div>

                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                Ke Halaman Login
                            </a>
                        </div>
                    </div>

                    <p class="text-center text-muted mt-4 mb-0 small">
                        {{ config('app.kanwil.short_name') }} · {{ config('app.kanwil.address') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

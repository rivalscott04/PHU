<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Terjadi Kesalahan — {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet">
</head>
<body>
    <div class="account-pages my-5 pt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body p-4 text-center">
                            <h1 class="display-4 fw-medium">500</h1>
                            <h4 class="text-uppercase">Terjadi Kesalahan</h4>
                            <p class="text-muted mb-4">
                                Maaf, sistem mengalami gangguan saat memproses permintaan Anda.
                                Silakan coba lagi. Jika masalah berlanjut, hubungi admin.
                            </p>
                            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

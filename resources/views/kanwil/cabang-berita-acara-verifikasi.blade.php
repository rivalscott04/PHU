<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Verifikasi Berita Acara Cabang PPIU</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5" style="max-width: 720px">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="" height="64">
            <h1 class="h5 mt-3 mb-0">Verifikasi Berita Acara Pembukaan PPIU Cabang</h1>
            <p class="text-muted small mb-0">{{ \App\Support\KanwilContact::get('letterhead_office') }}</p>
        </div>

        <div class="card">
            <div class="card-body p-4">
                @if ($berlaku)
                    <div class="alert alert-success d-flex gap-3 align-items-center">
                        <i class="bx bx-check-shield fs-1"></i>
                        <div>
                            <strong>Tanda tangan sah.</strong><br>
                            Berita acara ini diterbitkan oleh Kanwil dan cabangnya masih berstatus disetujui.
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger d-flex gap-3 align-items-center">
                        <i class="bx bx-x-circle fs-1"></i>
                        <div>
                            <strong>Tidak berlaku lagi.</strong><br>
                            Berita acara ini pernah diterbitkan, tetapi status cabangnya sekarang
                            {{ $cabang->registration_status?->label() ?? 'tidak aktif' }}.
                        </div>
                    </div>
                @endif

                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Nomor</dt>
                    <dd class="col-sm-8">{{ $cabang->nomor_berita_acara }}</dd>
                    <dt class="col-sm-4 text-muted fw-normal">PPIU Cabang</dt>
                    <dd class="col-sm-8">{{ $cabang->Penyelenggara }}</dd>
                    <dt class="col-sm-4 text-muted fw-normal">Wilayah</dt>
                    <dd class="col-sm-8">{{ $cabang->kabupaten }}</dd>
                    <dt class="col-sm-4 text-muted fw-normal">Kepala Cabang</dt>
                    <dd class="col-sm-8">{{ $cabang->pimpinan_cabang }}</dd>
                    <dt class="col-sm-4 text-muted fw-normal">Disetujui</dt>
                    <dd class="col-sm-8">{{ $cabang->verified_at?->translatedFormat('j F Y, H:i') }} WITA</dd>
                    @if ($penandatangan->nama)
                        <dt class="col-sm-4 text-muted fw-normal">Ditandatangani</dt>
                        <dd class="col-sm-8">{{ $penandatangan->nama }}@if ($penandatangan->nip)<br><span class="text-muted small">NIP {{ $penandatangan->nip }}</span>@endif</dd>
                    @endif
                </dl>
            </div>
        </div>

        <p class="text-center text-muted small mt-3">Dicek {{ now()->translatedFormat('j F Y, H:i') }}</p>
    </main>
</body>

</html>

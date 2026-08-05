<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Registrasi Travel | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Daftar sebagai PPIU/PIHK di PANTAU Kanwil NTB" name="description" />
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    @include('travel-registration.partials.wizard-styles')
</head>
<body>
    <div class="account-pages my-4 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">
                    <div class="text-center mb-4">
                        <a href="{{ route('login') }}">
                            <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}" height="40">
                        </a>
                        <h4 class="mt-3 mb-1">Registrasi Travel (PPIU / PIHK)</h4>
                        <p class="text-muted mb-0 mx-auto" style="max-width: 520px">
                            Isi formulir langkah demi langkah. Setiap halaman hanya beberapa pertanyaan. Tidak perlu diisi sekaligus.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger col-lg-8 mx-auto">
                            <strong>Periksa kembali formulir:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card mb-5 travel-wizard-card">
                        <div class="card-body p-4 p-md-5">
                            <form id="travel-registration-form" method="POST" action="{{ route('travel.registration.store') }}" enctype="multipart/form-data">
                                @csrf

                                @include('travel-registration.partials.wizard-progress')

                                <div id="travel-registration-wizard">
                                    <h3>Profil</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-buildings',
                                            'title' => 'Profil Perusahaan',
                                            'description' => 'Informasi dasar travel Anda. Isi sesuai data pada izin resmi.',
                                        ])
                                        @include('partials.travel-company-fields', [
                                            'kabupatens' => $kabupatens,
                                            'section' => 'profil',
                                            'compact' => true,
                                        ])
                                    </section>

                                    <h3>Izin</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-file-blank',
                                            'title' => 'Izin Operasional',
                                            'description' => 'Nomor dan tanggal Surat Keputusan (SK) izin travel.',
                                        ])
                                        @include('partials.travel-company-fields', [
                                            'kabupatens' => $kabupatens,
                                            'section' => 'izin',
                                            'compact' => true,
                                        ])
                                    </section>

                                    <h3>Akreditasi</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-certification',
                                            'title' => 'Data Akreditasi',
                                            'description' => 'Informasi sertifikat akreditasi travel Anda.',
                                        ])
                                        @include('partials.travel-company-fields', [
                                            'kabupatens' => $kabupatens,
                                            'section' => 'akreditasi',
                                            'compact' => true,
                                        ])
                                    </section>

                                    <h3>Alamat</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-map',
                                            'title' => 'Kontak & Alamat Kantor',
                                            'description' => 'Nomor telepon dan alamat kantor travel.',
                                        ])
                                        @include('partials.travel-company-fields', [
                                            'kabupatens' => $kabupatens,
                                            'section' => 'alamat',
                                            'compact' => true,
                                        ])
                                    </section>

                                    <h3>Dokumen</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-cloud-upload',
                                            'title' => 'Upload Dokumen',
                                            'description' => 'Unggah scan dokumen resmi. Admin Kanwil akan memeriksa saat verifikasi.',
                                        ])
                                        @include('partials.travel-registration-documents', ['compact' => true])
                                    </section>

                                    <h3>Akun</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-user-circle',
                                            'title' => 'Akun PIC',
                                            'description' => 'Buat akun login untuk Penanggung Jawab (PIC). Aktif setelah pendaftaran disetujui.',
                                        ])
                                        @include('travel-registration.partials.pic-fields')
                                    </section>

                                    <h3>Review</h3>
                                    <section>
                                        <div class="alert alert-light border mb-4 col-lg-8 mx-auto">
                                            <i class="bx bx-info-circle me-1 text-primary"></i>
                                            Periksa kembali semua data. Gunakan tombol <strong>Kembali</strong> jika ada yang perlu diperbaiki.
                                        </div>
                                        <div class="alert alert-info mb-4 col-lg-8 mx-auto">
                                            <i class="bx bx-time-five me-1"></i>
                                            Setelah dikirim, status Anda <strong>Menunggu Verifikasi</strong>.
                                            Login baru bisa dilakukan setelah Admin Kanwil menyetujui.
                                        </div>
                                        <div id="travel-registration-review" class="col-lg-10 mx-auto"></div>
                                    </section>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @include('travel-registration.partials.wizard-scripts')
</body>
</html>

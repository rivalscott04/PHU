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
</head>
<body>
    <div class="account-pages my-4 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-11">
                    <div class="text-center mb-4">
                        <a href="{{ route('login') }}">
                            <img src="{{ asset('images/logo_web.png') }}" alt="{{ config('app.name') }}" height="40">
                        </a>
                        <h4 class="mt-3 mb-1">Registrasi Travel (PPIU / PIHK)</h4>
                        <p class="text-muted mb-0">Isi data perusahaan dan akun PIC. Setelah dikirim, Admin Kanwil akan memverifikasi pendaftaran Anda.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Periksa kembali formulir:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('travel.registration.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-primary rounded-pill me-2">1</span>
                                    <h5 class="mb-0">Data Perusahaan Travel</h5>
                                </div>
                                <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>
                                @include('partials.travel-company-fields', ['kabupatens' => $kabupatens])
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-primary rounded-pill me-2">2</span>
                                    <h5 class="mb-0">Upload Dokumen Pendukung</h5>
                                </div>
                                <p class="text-muted small mb-3">Unggah scan dokumen resmi sesuai data yang diisi. Admin Kanwil akan memeriksa dokumen ini saat verifikasi.</p>
                                @include('partials.travel-registration-documents')
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-primary rounded-pill me-2">3</span>
                                    <h5 class="mb-0">Data PIC &amp; Akun Login</h5>
                                </div>
                                <p class="text-muted small">PIC (Penanggung Jawab) yang akan login ke sistem setelah pendaftaran disetujui.</p>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="pic_nama" class="form-label">Nama Lengkap PIC @include('partials.required-star')</label>
                                        <input type="text" class="form-control @error('pic_nama') is-invalid @enderror"
                                            id="pic_nama" name="pic_nama" value="{{ old('pic_nama') }}" required>
                                        @error('pic_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pic_email" class="form-label">Email @include('partials.required-star')</label>
                                        <input type="email" class="form-control @error('pic_email') is-invalid @enderror"
                                            id="pic_email" name="pic_email" value="{{ old('pic_email') }}" required>
                                        @error('pic_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">Digunakan untuk login</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pic_nomor_hp" class="form-label">Nomor HP (WhatsApp) @include('partials.required-star')</label>
                                        <input type="tel" class="form-control @error('pic_nomor_hp') is-invalid @enderror"
                                            id="pic_nomor_hp" name="pic_nomor_hp" value="{{ old('pic_nomor_hp') }}"
                                            inputmode="numeric" pattern="08[0-9]{6,12}" minlength="8" maxlength="14"
                                            placeholder="081234567890" autocomplete="tel" required>
                                        @error('pic_nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">Angka saja, diawali 08, 8–14 digit. Bisa dipakai untuk login.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password @include('partials.required-star')</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" minlength="8" required>
                                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">Minimal 8 karakter</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Ulangi Password @include('partials.required-star')</label>
                                        <input type="password" class="form-control"
                                            id="password_confirmation" name="password_confirmation" minlength="8" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-1"></i>
                            Setelah formulir dikirim, status pendaftaran <strong>Menunggu Verifikasi</strong>.
                            Anda baru bisa login setelah Admin Kanwil menyetujui.
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-5">
                            <a href="{{ route('login') }}" class="btn btn-light">
                                <i class="bx bx-arrow-back me-1"></i> Kembali ke Login
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-send me-1"></i> Kirim Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById('pic_nomor_hp')?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 14);
        });
    </script>
</body>
</html>

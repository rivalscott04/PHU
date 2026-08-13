@php
    $wizardSteps = [
        ['label' => 'Pusat', 'full' => 'Travel Pusat'],
        ['label' => 'Cabang', 'full' => 'Data Cabang'],
        ['label' => 'Dokumen', 'full' => 'Dokumen Cabang'],
        ['label' => 'Akun', 'full' => 'Akun PIC Cabang'],
        ['label' => 'Review', 'full' => 'Review & Kirim'],
    ];

    $reviewGroups = [
        ['title' => 'Travel Pusat', 'fields' => ['travel_id']],
        ['title' => 'Data Cabang', 'fields' => ['kabupaten', 'pimpinan_cabang', 'SK_BA', 'tanggal', 'telepon', 'alamat_cabang']],
        ['title' => 'Dokumen Cabang', 'fields' => array_column(\App\Models\CabangTravel::DOKUMEN_PENDAFTARAN, 'column')],
        ['title' => 'Akun PIC', 'fields' => ['pic_nama', 'pic_email', 'pic_nomor_hp', 'password']],
    ];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Registrasi Cabang Travel | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Daftarkan kantor cabang travel di PANTAU Kanwil NTB" name="description" />
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <h4 class="mt-3 mb-1">Registrasi Travel</h4>
                        <p class="text-muted mb-0 mx-auto" style="max-width: 560px">
                            Untuk kantor cabang dari travel yang izin pusatnya sudah terdaftar.
                            Isi langkah demi langkah, tidak perlu sekaligus.
                        </p>
                    </div>

                    @include('travel-registration.partials.jenis-switcher', ['jenis' => 'cabang'])

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

                    <div class="card mb-5">
                        <div class="card-body p-4 p-md-5">
                            <form id="travel-registration-form" method="POST" action="{{ route('cabang.registration.store') }}" enctype="multipart/form-data">
                                @csrf

                                @include('travel-registration.partials.wizard-progress', ['wizardSteps' => $wizardSteps])

                                <div id="travel-registration-wizard">
                                    <h3>Pusat</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-buildings',
                                            'title' => 'Travel Pusat',
                                            'description' => 'Pilih travel pusat yang menaungi cabang ini. Nomor SK pusat terbaca otomatis, tidak perlu diunggah ulang.',
                                        ])

                                        <div class="row">
                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="travel_id" class="form-label">Travel Pusat @include('partials.required-star')</label>
                                                <select class="form-select form-select-lg @error('travel_id') is-invalid @enderror"
                                                    id="travel_id" name="travel_id" required>
                                                    <option value="">Pilih travel pusat</option>
                                                    @foreach ($travels as $travel)
                                                        <option value="{{ $travel->id }}"
                                                            data-sk="{{ $travel->Pusat }}"
                                                            data-pimpinan="{{ $travel->Pimpinan }}"
                                                            @selected(old('travel_id') == $travel->id)>
                                                            {{ $travel->Penyelenggara }} ({{ $travel->kab_kota }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('travel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                <div class="form-text">
                                                    Pusat belum terdaftar?
                                                    <a href="{{ route('travel.registration.create') }}">Daftarkan pusat lebih dulu</a>.
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <div class="alert alert-light border mb-0 d-none" id="pusat-info">
                                                    <div class="small text-muted mb-1">Data pusat yang akan tercatat:</div>
                                                    <div><strong>No. SK Pusat:</strong> <span id="pusat-sk">-</span></div>
                                                    <div><strong>Pimpinan Pusat:</strong> <span id="pusat-pimpinan">-</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <h3>Cabang</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-map',
                                            'title' => 'Data Cabang',
                                            'description' => 'Kabupaten/kota menentukan kantor Kemenag mana yang meninjau cabang Anda.',
                                        ])

                                        <div class="row">
                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="kabupaten" class="form-label">Kabupaten / Kota @include('partials.required-star')</label>
                                                <select class="form-select form-select-lg @error('kabupaten') is-invalid @enderror"
                                                    id="kabupaten" name="kabupaten" required>
                                                    <option value="">Pilih kabupaten/kota</option>
                                                    @foreach ($kabupatens as $kabupaten)
                                                        <option value="{{ $kabupaten }}" @selected(old('kabupaten') === $kabupaten)>{{ $kabupaten }}</option>
                                                    @endforeach
                                                </select>
                                                @error('kabupaten')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="pimpinan_cabang" class="form-label">Nama Kepala Cabang @include('partials.required-star')</label>
                                                <input type="text" class="form-control form-control-lg @error('pimpinan_cabang') is-invalid @enderror"
                                                    id="pimpinan_cabang" name="pimpinan_cabang" value="{{ old('pimpinan_cabang') }}"
                                                    placeholder="Nama penanggung jawab kantor cabang" required>
                                                @error('pimpinan_cabang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="SK_BA" class="form-label">No. SK / BA Pembukaan Cabang @include('partials.required-star')</label>
                                                <input type="text" class="form-control form-control-lg @error('SK_BA') is-invalid @enderror"
                                                    id="SK_BA" name="SK_BA" value="{{ old('SK_BA') }}" required>
                                                @error('SK_BA')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                <div class="form-text">Nomor pada surat keputusan atau berita acara pembukaan cabang</div>
                                            </div>

                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="tanggal" class="form-label">Tanggal SK / BA @include('partials.required-star')</label>
                                                <input type="date" class="form-control form-control-lg @error('tanggal') is-invalid @enderror"
                                                    id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                                                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="telepon" class="form-label">Telepon Cabang @include('partials.required-star')</label>
                                                <input type="tel" class="form-control form-control-lg @error('telepon') is-invalid @enderror"
                                                    id="telepon" name="telepon" value="{{ old('telepon') }}"
                                                    inputmode="numeric" maxlength="16" required>
                                                @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="col-12 col-lg-8 mx-auto mb-4">
                                                <label for="alamat_cabang" class="form-label">Alamat Kantor Cabang @include('partials.required-star')</label>
                                                <textarea class="form-control @error('alamat_cabang') is-invalid @enderror"
                                                    id="alamat_cabang" name="alamat_cabang" rows="3" required>{{ old('alamat_cabang') }}</textarea>
                                                @error('alamat_cabang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </section>

                                    <h3>Dokumen</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-cloud-upload',
                                            'title' => 'Dokumen Cabang',
                                            'description' => 'Unggah scan dokumen resmi cabang. Petugas Kabupaten/Kota memeriksanya saat peninjauan.',
                                        ])

                                        <div class="row">
                                            @foreach (\App\Models\CabangTravel::DOKUMEN_PENDAFTARAN as $meta)
                                                <div class="col-12 col-lg-8 mx-auto mb-4">
                                                    <label for="{{ $meta['column'] }}" class="form-label">
                                                        {{ $meta['label'] }} @include('partials.required-star')
                                                    </label>
                                                    <input type="file" class="form-control @error($meta['column']) is-invalid @enderror"
                                                        id="{{ $meta['column'] }}" name="{{ $meta['column'] }}"
                                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                                    @error($meta['column'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                    <div class="form-text">Unggah PDF atau foto (JPG/PNG), maksimal 1,5 MB</div>
                                                </div>
                                            @endforeach

                                            <div class="col-12 col-lg-8 mx-auto">
                                                <div class="alert alert-light border mb-0 small">
                                                    <i class="bx bx-info-circle text-primary me-1"></i>
                                                    SK izin pusat tidak perlu diunggah. Sistem membacanya dari travel pusat yang Anda pilih.
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <h3>Akun</h3>
                                    <section>
                                        @include('travel-registration.partials.step-intro', [
                                            'icon' => 'bx-user-circle',
                                            'title' => 'Akun PIC Cabang',
                                            'description' => 'Buat akun login untuk penanggung jawab cabang. Aktif setelah pendaftaran disetujui.',
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
                                            Alur: <strong>Menunggu Verifikasi</strong> (peninjauan Kabupaten/Kota) →
                                            <strong>Menunggu Kanwil</strong> → <strong>Disetujui</strong>.
                                            Login baru bisa dilakukan setelah disetujui Kanwil.
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
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/confirm-dialogs.js') }}"></script>
    @include('partials.input-limits-script')
    @include('travel-registration.partials.wizard-scripts', ['reviewGroups' => $reviewGroups])
    <script>
        document.getElementById('travel_id').addEventListener('change', function () {
            const option = this.selectedOptions[0];
            const box = document.getElementById('pusat-info');

            box.classList.toggle('d-none', !this.value);
            document.getElementById('pusat-sk').textContent = option?.dataset.sk || '-';
            document.getElementById('pusat-pimpinan').textContent = option?.dataset.pimpinan || '-';
        });
    </script>
</body>
</html>

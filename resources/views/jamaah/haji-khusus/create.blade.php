@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tambah Jamaah Haji Khusus</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jamaah.haji-khusus.index') }}">Jamaah Haji Khusus</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah Jamaah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h4 class="card-title mb-1">Form Pendaftaran Jamaah Haji Khusus</h4>
                        <p class="card-title-desc mb-0">Lengkapi data jamaah haji khusus langkah demi langkah</p>
                    </div>
                    <a href="{{ route('jamaah.haji-khusus.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali ke daftar
                    </a>
                </div>
                <div class="card-body">
                    <form id="jamaah-haji-khusus-form" action="{{ route('jamaah.haji-khusus.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        @include('jamaah.haji-khusus.partials.form-wizard-progress')

                        <div id="jamaah-haji-khusus-wizard">
                            <h3>Pribadi</h3>
                            <section>
                        <p class="text-muted small mb-3">Identitas jamaah sesuai NIK.</p>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                        id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="no_ktp" class="form-label">NIK <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_ktp') is-invalid @enderror"
                                        id="no_ktp" name="no_ktp" value="{{ old('no_ktp') }}"
                                        data-digits-only="16" inputmode="numeric" pattern="[0-9]*"
                                        maxlength="16" autocomplete="off" spellcheck="false" required>
                                    @error('no_ktp')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                        id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                        id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            </section>

                            <h3>Alamat</h3>
                            <section>
                        <p class="text-muted small mb-3">Alamat domisili jamaah.</p>
                        <div class="row g-3">

                            <div class="col-12">
                                <div class="mb-3 position-relative">
                                    <label for="alamat" class="form-label">Alamat Lengkap <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                                        maxlength="{{ \App\Helpers\ValidationHelper::VARCHAR_MAX }}" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="provinsi" class="form-label">
                                        <i class="bx bx-map-pin me-1"></i>
                                        Provinsi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('provinsi') is-invalid @enderror"
                                        id="provinsi" name="provinsi" required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    @include('partials.wilayah-override', [
                                        'buttonId' => 'provinsi_override_btn',
                                        'panelId' => 'provinsi_override_panel',
                                        'inputId' => 'provinsi_manual',
                                        'title' => 'Isi provinsi secara manual',
                                        'label' => 'Nama Provinsi',
                                        'placeholder' => 'Contoh: Kepulauan Riau',
                                    ])
                                    @error('provinsi')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="kota" class="form-label">
                                        <i class="bx bx-building me-1"></i>
                                        Kota/Kabupaten <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('kota') is-invalid @enderror"
                                        id="kota" name="kota" required disabled>
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                    @include('partials.wilayah-override', [
                                        'buttonId' => 'kota_override_btn',
                                        'panelId' => 'kota_override_panel',
                                        'inputId' => 'kota_manual',
                                        'title' => 'Isi kabupaten/kota secara manual',
                                        'label' => 'Nama Kabupaten/Kota',
                                        'placeholder' => 'Contoh: Kabupaten Karimun',
                                    ])
                                    @error('kota')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="kecamatan" class="form-label">
                                        <i class="bx bx-map-alt me-1"></i>
                                        Kecamatan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('kecamatan') is-invalid @enderror"
                                        id="kecamatan" name="kecamatan" required disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    @include('partials.wilayah-override', [
                                        'buttonId' => 'kecamatan_override_btn',
                                        'panelId' => 'kecamatan_override_panel',
                                        'inputId' => 'kecamatan_manual',
                                        'title' => 'Isi kecamatan secara manual',
                                        'label' => 'Nama Kecamatan',
                                        'placeholder' => 'Contoh: Kecamatan Tanjung Pinang Barat',
                                    ])
                                    @error('kecamatan')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="kode_pos" class="form-label">Kode Pos <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_pos') is-invalid @enderror"
                                        id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}"
                                        data-digits-only="5" inputmode="numeric" pattern="[0-9]*"
                                        maxlength="5" autocomplete="off" required>
                                    @error('kode_pos')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            </section>

                            <h3>Kontak</h3>
                            <section>
                        <p class="text-muted small mb-3">Nomor yang bisa dihubungi dan data ayah kandung.</p>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="no_hp" class="form-label">No. HP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                        id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                                        data-digits-only="16" inputmode="numeric" pattern="[0-9]*"
                                        maxlength="16" autocomplete="off" required>
                                    @error('no_hp')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="nama_ayah" class="form-label">Nama Ayah <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror"
                                        id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}" required>
                                    @error('nama_ayah')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            </section>

                            <h3>Tambahan</h3>
                            <section>
                        <p class="text-muted small mb-3">Informasi pekerjaan, pendidikan, dan kesehatan.</p>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="pekerjaan" class="form-label">Pekerjaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror"
                                        id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}" required>
                                    @error('pekerjaan')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('pendidikan_terakhir') is-invalid @enderror"
                                        id="pendidikan_terakhir" name="pendidikan_terakhir"
                                        value="{{ old('pendidikan_terakhir') }}" required>
                                    @error('pendidikan_terakhir')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="status_pernikahan" class="form-label">Status Pernikahan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('status_pernikahan') is-invalid @enderror"
                                        id="status_pernikahan" name="status_pernikahan" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Belum Menikah"
                                            {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum
                                            Menikah</option>
                                        <option value="Menikah"
                                            {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="Cerai"
                                            {{ old('status_pernikahan') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                    </select>
                                    @error('status_pernikahan')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="pergi_haji" class="form-label">Pergi Haji</label>
                                    <select class="form-select @error('pergi_haji') is-invalid @enderror" id="pergi_haji"
                                        name="pergi_haji">
                                        <option value="">Pilih Status</option>
                                        <option value="Belum" {{ old('pergi_haji') == 'Belum' ? 'selected' : '' }}>Belum
                                        </option>
                                        <option value="Sudah" {{ old('pergi_haji') == 'Sudah' ? 'selected' : '' }}>Sudah
                                        </option>
                                    </select>
                                    @error('pergi_haji')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="golongan_darah" class="form-label">Golongan Darah <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('golongan_darah') is-invalid @enderror"
                                        id="golongan_darah" name="golongan_darah" required>
                                        <option value="">Pilih Golongan Darah</option>
                                        <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A
                                        </option>
                                        <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B
                                        </option>
                                        <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB
                                        </option>
                                        <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O
                                        </option>
                                    </select>
                                    @error('golongan_darah')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="alergi" class="form-label">Alergi</label>
                                    <input type="text" class="form-control @error('alergi') is-invalid @enderror"
                                        id="alergi" name="alergi" value="{{ old('alergi') }}"
                                        maxlength="{{ \App\Helpers\ValidationHelper::VARCHAR_MAX }}">
                                    @error('alergi')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            </section>

                            <h3>Paspor</h3>
                            <section>
                        <p class="text-muted small mb-3">Data paspor dan informasi haji khusus.</p>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="no_paspor" class="form-label">No. Paspor</label>
                                    <input type="text" class="form-control @error('no_paspor') is-invalid @enderror"
                                        id="no_paspor" name="no_paspor" value="{{ old('no_paspor') }}">
                                    @error('no_paspor')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="tanggal_berlaku_paspor" class="form-label">Tanggal Berlaku Paspor</label>
                                    <input type="date"
                                        class="form-control @error('tanggal_berlaku_paspor') is-invalid @enderror"
                                        id="tanggal_berlaku_paspor" name="tanggal_berlaku_paspor"
                                        value="{{ old('tanggal_berlaku_paspor') }}">
                                    @error('tanggal_berlaku_paspor')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="tempat_terbit_paspor" class="form-label">Tempat Terbit Paspor</label>
                                    <input type="text"
                                        class="form-control @error('tempat_terbit_paspor') is-invalid @enderror"
                                        id="tempat_terbit_paspor" name="tempat_terbit_paspor"
                                        value="{{ old('tempat_terbit_paspor') }}">
                                    @error('tempat_terbit_paspor')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="nomor_porsi" class="form-label">
                                        Nomor SPPH
                                        @if (auth()->user()->role === 'user')
                                            <span class="text-muted">(Admin Only)</span>
                                        @endif
                                    </label>
                                    <input type="text" class="form-control @error('nomor_porsi') is-invalid @enderror"
                                        id="nomor_porsi" name="nomor_porsi" value="{{ old('nomor_porsi') }}"
                                        maxlength="20" @if (auth()->user()->role === 'user') disabled @endif>
                                    @if (auth()->user()->role === 'user')
                                        <small class="text-muted">Nomor SPPH akan ditetapkan oleh admin setelah verifikasi
                                            bukti setor bank</small>
                                    @endif
                                    @error('nomor_porsi')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="tahun_pendaftaran" class="form-label">Tahun Pendaftaran</label>
                                    <input type="date"
                                        class="form-control @error('tahun_pendaftaran') is-invalid @enderror"
                                        id="tahun_pendaftaran" name="tahun_pendaftaran"
                                        value="{{ old('tahun_pendaftaran') }}">
                                    @error('tahun_pendaftaran')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3 position-relative">
                                    <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
                                    <textarea class="form-control @error('catatan_khusus') is-invalid @enderror" id="catatan_khusus"
                                        name="catatan_khusus" rows="3"
                                        maxlength="{{ \App\Helpers\ValidationHelper::TEXT_MAX }}">{{ old('catatan_khusus') }}</textarea>
                                    @error('catatan_khusus')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            </section>

                            <h3>Dokumen</h3>
                            <section>
                        <p class="text-muted small mb-3">Upload dokumen pendukung. Format PDF/JPG/PNG, maks. 2MB per file.</p>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="dokumen_ktp" class="form-label">Dokumen KTP</label>
                                    <input type="file" class="form-control @error('dokumen_ktp') is-invalid @enderror"
                                        id="dokumen_ktp" name="dokumen_ktp" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('dokumen_ktp')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="dokumen_kk" class="form-label">Dokumen KK</label>
                                    <input type="file" class="form-control @error('dokumen_kk') is-invalid @enderror"
                                        id="dokumen_kk" name="dokumen_kk" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('dokumen_kk')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="dokumen_paspor" class="form-label">Dokumen Paspor</label>
                                    <input type="file"
                                        class="form-control @error('dokumen_paspor') is-invalid @enderror"
                                        id="dokumen_paspor" name="dokumen_paspor" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('dokumen_paspor')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="dokumen_foto" class="form-label">Foto 3x4</label>
                                    <input type="file"
                                        class="form-control @error('dokumen_foto') is-invalid @enderror"
                                        id="dokumen_foto" name="dokumen_foto" accept=".jpg,.jpeg,.png">
                                    <small class="text-muted">Format: JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('dokumen_foto')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="surat_keterangan" class="form-label">Surat Keterangan</label>
                                    <input type="file"
                                        class="form-control @error('surat_keterangan') is-invalid @enderror"
                                        id="surat_keterangan" name="surat_keterangan" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('surat_keterangan')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="bukti_setor_bank" class="form-label">
                                        Bukti Setor Bank
                                    </label>
                                    <input type="file"
                                        class="form-control @error('bukti_setor_bank') is-invalid @enderror"
                                        id="bukti_setor_bank" name="bukti_setor_bank" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    <div class="alert alert-info mt-2">
                                        <i class="bx bx-info-circle me-1"></i>
                                        <strong>Info:</strong> Bukti setor bank akan diverifikasi oleh Kanwil.
                                        Nomor SPPH akan ditetapkan setelah verifikasi berhasil.
                                    </div>
                                    @error('bukti_setor_bank')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            </section>

                            @include('jamaah.haji-khusus.partials.form-wizard-review-step')
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('jamaah.haji-khusus.partials.form-wizard-styles')
@include('jamaah.haji-khusus.partials.form-wizard-scripts', [
    'finishLabel' => 'Simpan Data',
    'locationDefaults' => [
        'provinsi' => old('provinsi'),
        'kota' => old('kota'),
        'kecamatan' => old('kecamatan'),
    ],
])

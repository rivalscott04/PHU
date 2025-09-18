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
                <div class="card-header">
                    <h4 class="card-title">Form Pendaftaran Jamaah Haji Khusus</h4>
                    <p class="card-title-desc">Lengkapi data jamaah haji khusus dengan informasi yang akurat</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('jamaah.haji-khusus.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Data Pribadi -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="bx bx-user me-2"></i>
                                    Data Pribadi
                                </h5>
                            </div>

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
                                    <label for="no_ktp" class="form-label">No. KTP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_ktp') is-invalid @enderror"
                                        id="no_ktp" name="no_ktp" value="{{ old('no_ktp') }}" maxlength="16" required>
                                    @error('no_ktp')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
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

                            <div class="col-md-4">
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

                            <div class="col-md-4">
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

                        <!-- Alamat -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-map me-2"></i>
                                    Alamat
                                </h5>
                            </div>

                            <div class="col-12">
                                <div class="mb-3 position-relative">
                                    <label for="alamat" class="form-label">Alamat Lengkap <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                                        required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="provinsi" class="form-label">
                                        <i class="bx bx-map-pin me-1"></i>
                                        Provinsi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select location-select @error('provinsi') is-invalid @enderror"
                                        id="provinsi" name="provinsi" required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Pilih provinsi untuk memuat daftar kota/kabupaten
                                    </div>
                                    @error('provinsi')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="kota" class="form-label">
                                        <i class="bx bx-building me-1"></i>
                                        Kota/Kabupaten <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select location-select @error('kota') is-invalid @enderror"
                                        id="kota" name="kota" required disabled>
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Pilih kota/kabupaten sesuai dengan provinsi yang dipilih
                                    </div>
                                    @error('kota')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="kecamatan" class="form-label">
                                        <i class="bx bx-map-alt me-1"></i>
                                        Kecamatan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select location-select @error('kecamatan') is-invalid @enderror"
                                        id="kecamatan" name="kecamatan" required disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Pilih kecamatan sesuai dengan kota/kabupaten yang dipilih
                                    </div>
                                    @error('kecamatan')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="kode_pos" class="form-label">Kode Pos <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_pos') is-invalid @enderror"
                                        id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}" maxlength="5"
                                        required>
                                    @error('kode_pos')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Kontak -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-phone me-2"></i>
                                    Informasi Kontak
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="no_hp" class="form-label">No. HP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                        id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
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
                        </div>

                        <!-- Data Keluarga -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-group me-2"></i>
                                    Data Keluarga
                                </h5>
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

                        <!-- Data Tambahan -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Data Tambahan
                                </h5>
                            </div>

                            <div class="col-md-4">
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

                            <div class="col-md-4">
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

                            <div class="col-md-4">
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

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="pergi_haji" class="form-label">Pergi Haji <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('pergi_haji') is-invalid @enderror" id="pergi_haji"
                                        name="pergi_haji" required>
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



                            <div class="col-md-4">
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

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="alergi" class="form-label">Alergi</label>
                                    <input type="text" class="form-control @error('alergi') is-invalid @enderror"
                                        id="alergi" name="alergi" value="{{ old('alergi') }}">
                                    @error('alergi')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Data Paspor -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-passport me-2"></i>
                                    Data Paspor
                                </h5>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 position-relative">
                                    <label for="no_paspor" class="form-label">No. Paspor</label>
                                    <input type="text" class="form-control @error('no_paspor') is-invalid @enderror"
                                        id="no_paspor" name="no_paspor" value="{{ old('no_paspor') }}">
                                    @error('no_paspor')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
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

                            <div class="col-md-4">
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
                        </div>

                        <!-- Data Haji Khusus -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-star me-2"></i>
                                    Data Haji Khusus
                                </h5>
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
                                        name="catatan_khusus" rows="3">{{ old('catatan_khusus') }}</textarea>
                                    @error('catatan_khusus')
                                        <div class="invalid-tooltip">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Upload Dokumen -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">
                                    <i class="bx bx-upload me-2"></i>
                                    Upload Dokumen
                                </h5>
                            </div>

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
                                        Bukti Setor Bank <span class="text-danger">*</span>
                                    </label>
                                    <input type="file"
                                        class="form-control @error('bukti_setor_bank') is-invalid @enderror"
                                        id="bukti_setor_bank" name="bukti_setor_bank" accept=".pdf,.jpg,.jpeg,.png"
                                        required>
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

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('jamaah.haji-khusus.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-arrow-back me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>
                                        Simpan Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom styling for location dropdowns */
        .location-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            background-color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .location-select:focus {
            border-color: #556ee6;
            box-shadow: 0 0 0 0.2rem rgba(85, 110, 230, 0.25);
            background-color: #fff;
        }

        .location-select:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .location-select option {
            padding: 8px 12px;
            font-size: 14px;
            color: #495057;
            background-color: #fff;
        }

        .location-select option:hover {
            background-color: #556ee6;
            color: #fff;
        }

        /* Enhanced form validation styling */
        .validation-error {
            animation: shake 0.5s ease-in-out;
        }

        .validation-error .form-control,
        .validation-error .form-select {
            border-color: #f46a6a !important;
            box-shadow: 0 0 0 0.2rem rgba(244, 106, 106, 0.25) !important;
        }

        .validation-error .form-label {
            color: #f46a6a !important;
            font-weight: 700;
        }

        .validation-error-message {
            display: block !important;
            font-size: 12px;
            font-weight: 500;
            color: #f46a6a;
            margin-top: 4px;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Success state styling */
        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #34c38f !important;
            box-shadow: 0 0 0 0.2rem rgba(52, 195, 143, 0.25) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2334c38f' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        /* Validation summary styling */
        #validation-summary {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(244, 106, 106, 0.15);
            animation: slideInDown 0.5s ease-out;
        }

        #validation-summary ul {
            list-style: none;
            padding-left: 0;
        }

        #validation-summary li {
            padding: 4px 0;
            font-size: 14px;
            color: #721c24;
            position: relative;
            padding-left: 20px;
        }

        #validation-summary li:before {
            content: "•";
            color: #f46a6a;
            font-weight: bold;
            position: absolute;
            left: 0;
            top: 2px;
        }

        /* Animations */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus states for better UX */
        .form-control:focus,
        .form-select:focus {
            border-color: #556ee6;
            box-shadow: 0 0 0 0.2rem rgba(85, 110, 230, 0.25);
            transition: all 0.3s ease;
        }

        /* Required field indicator */
        .form-label .text-danger {
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        /* Form text styling */
        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
            display: flex;
            align-items: center;
        }

        .form-text i {
            font-size: 14px;
            margin-right: 4px;
            color: #556ee6;
        }

        /* Label styling */
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            color: #556ee6;
            margin-right: 6px;
        }

        /* Card styling improvements */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 12px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Section headers */
        h5 {
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #556ee6;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        h5 i {
            color: #556ee6;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .location-select {
                font-size: 16px;
                /* Prevents zoom on iOS */
            }

            .form-label {
                font-size: 14px;
            }

            .form-text {
                font-size: 11px;
            }
        }

        /* Hover effects */
        .location-select:hover:not(:disabled) {
            border-color: #556ee6;
            box-shadow: 0 4px 8px rgba(85, 110, 230, 0.15);
        }

        /* Success state */
        .location-select.is-valid {
            border-color: #34c38f;
            box-shadow: 0 0 0 0.2rem rgba(52, 195, 143, 0.25);
        }

        /* Error state */
        .location-select.is-invalid {
            border-color: #f46a6a;
            box-shadow: 0 0 0 0.2rem rgba(244, 106, 106, 0.25);
        }
    </style>
@endpush

@push('js')
    <script>
        // Enhanced form validation with focus on unfilled fields
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const requiredFields = form.querySelectorAll('[required]');

            // Add visual indicators for required fields
            requiredFields.forEach(field => {
                const label = field.closest('.mb-3').querySelector('.form-label');
                if (label && !label.querySelector('.text-danger')) {
                    const asterisk = document.createElement('span');
                    asterisk.className = 'text-danger';
                    asterisk.textContent = ' *';
                    label.appendChild(asterisk);
                }
            });

            // Form submission with enhanced validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Reset all validation states
                resetValidationStates();

                // Check for empty required fields
                const emptyFields = [];
                let hasErrors = false;

                requiredFields.forEach(field => {
                    const value = field.value.trim();
                    const fieldContainer = field.closest('.mb-3');

                    if (!value) {
                        // Mark field as invalid
                        field.classList.add('is-invalid');
                        fieldContainer.classList.add('validation-error');

                        // Add error message if not exists
                        if (!fieldContainer.querySelector('.validation-error-message')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-tooltip validation-error-message';
                            errorDiv.textContent = 'Field ini wajib diisi';
                            fieldContainer.appendChild(errorDiv);
                        }

                        emptyFields.push(field);
                        hasErrors = true;
                    } else {
                        // Mark field as valid
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                        fieldContainer.classList.remove('validation-error');

                        // Remove error message if exists
                        const errorDiv = fieldContainer.querySelector('.validation-error-message');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                });

                // Special validation for select fields
                const requiredSelects = form.querySelectorAll('select[required]');
                requiredSelects.forEach(select => {
                    const fieldContainer = select.closest('.mb-3');
                    if (select.value === '') {
                        select.classList.add('is-invalid');
                        fieldContainer.classList.add('validation-error');

                        if (!fieldContainer.querySelector('.validation-error-message')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-tooltip validation-error-message';
                            errorDiv.textContent = 'Field ini wajib diisi';
                            fieldContainer.appendChild(errorDiv);
                        }

                        emptyFields.push(select);
                        hasErrors = true;
                    } else {
                        select.classList.remove('is-invalid');
                        select.classList.add('is-valid');
                        fieldContainer.classList.remove('validation-error');

                        const errorDiv = fieldContainer.querySelector('.validation-error-message');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                });

                // Special validation for file fields
                const requiredFiles = form.querySelectorAll('input[type="file"][required]');
                requiredFiles.forEach(fileInput => {
                    const fieldContainer = fileInput.closest('.mb-3');
                    if (!fileInput.files || fileInput.files.length === 0) {
                        fileInput.classList.add('is-invalid');
                        fieldContainer.classList.add('validation-error');

                        if (!fieldContainer.querySelector('.validation-error-message')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-tooltip validation-error-message';
                            errorDiv.textContent = 'File ini wajib diupload';
                            fieldContainer.appendChild(errorDiv);
                        }

                        emptyFields.push(fileInput);
                        hasErrors = true;
                    } else {
                        fileInput.classList.remove('is-invalid');
                        fileInput.classList.add('is-valid');
                        fieldContainer.classList.remove('validation-error');

                        const errorDiv = fieldContainer.querySelector('.validation-error-message');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                });

                if (hasErrors) {
                    // Show summary of missing fields
                    showValidationSummary(emptyFields);

                    // Scroll to first error
                    if (emptyFields.length > 0) {
                        emptyFields[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        emptyFields[0].focus();
                    }

                    return false;
                }

                // If validation passes, submit the form
                form.submit();
            });

            // Real-time validation on field blur
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    validateField(this);
                });

                field.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });

            function validateField(field) {
                const value = field.value.trim();
                const fieldContainer = field.closest('.mb-3');

                if (!value) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    fieldContainer.classList.add('validation-error');

                    if (!fieldContainer.querySelector('.validation-error-message')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-tooltip validation-error-message';
                        errorDiv.textContent = 'Field ini wajib diisi';
                        fieldContainer.appendChild(errorDiv);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    fieldContainer.classList.remove('validation-error');

                    const errorDiv = fieldContainer.querySelector('.validation-error-message');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            }

            function resetValidationStates() {
                // Remove all validation classes
                form.querySelectorAll('.is-invalid, .is-valid').forEach(field => {
                    field.classList.remove('is-invalid', 'is-valid');
                });

                form.querySelectorAll('.validation-error').forEach(container => {
                    container.classList.remove('validation-error');
                });

                // Remove all validation error messages
                form.querySelectorAll('.validation-error-message').forEach(message => {
                    message.remove();
                });
            }

            function showValidationSummary(emptyFields) {
                // Remove existing summary if any
                const existingSummary = document.getElementById('validation-summary');
                if (existingSummary) {
                    existingSummary.remove();
                }

                // Create validation summary
                const summary = document.createElement('div');
                summary.id = 'validation-summary';
                summary.className = 'alert alert-danger alert-dismissible fade show mt-3';
                summary.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle me-2" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Mohon lengkapi data berikut:</strong>
                    <ul class="mb-0 mt-2">
                        ${emptyFields.map(field => {
                            const label = field.closest('.mb-3').querySelector('.form-label');
                            const labelText = label ? label.textContent.replace(' *', '').trim() : 'Field';
                            return `<li>${labelText}</li>`;
                        }).join('')}
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

                // Insert summary before the submit buttons
                const submitRow = form.querySelector('.d-flex.justify-content-end');
                submitRow.parentNode.insertBefore(summary, submitRow);

                // Auto-hide summary after 5 seconds
                setTimeout(() => {
                    if (summary.parentNode) {
                        summary.remove();
                    }
                }, 5000);
            }
        });

        // Auto format KTP number
        document.getElementById('no_ktp').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 16) {
                value = value.substring(0, 16);
            }
            e.target.value = value;
        });

        // Auto format phone number with validation
        document.getElementById('no_hp').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
            
            // Validate phone number starts with 08
            const phoneInput = e.target;
            const phoneValue = phoneInput.value;
            
            // Remove existing validation message
            const existingMessage = phoneInput.parentNode.querySelector('.phone-validation-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Add validation if phone number is entered but doesn't start with 08
            if (phoneValue && phoneValue.length > 0 && !phoneValue.startsWith('08')) {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'phone-validation-message text-danger small mt-1';
                errorMessage.textContent = 'Nomor HP harus diawali dengan 08';
                phoneInput.parentNode.appendChild(errorMessage);
                phoneInput.classList.add('is-invalid');
            } else if (phoneValue && phoneValue.startsWith('08')) {
                phoneInput.classList.remove('is-invalid');
                phoneInput.classList.add('is-valid');
            } else {
                phoneInput.classList.remove('is-invalid', 'is-valid');
            }
        });

        // Auto format postal code
        document.getElementById('kode_pos').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5);
            }
            e.target.value = value;
        });

        // Province, City, and District hierarchical selection with improved UX
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('provinsi');
            const citySelect = document.getElementById('kota');
            const districtSelect = document.getElementById('kecamatan');

            // Load provinces on page load
            loadProvinces();

            // Handle province change
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                if (provinceId) {
                    loadCities(provinceId);
                } else {
                    resetCitySelect();
                    resetDistrictSelect();
                }
            });

            // Handle city change
            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                if (cityId) {
                    // Find the city ID from the API response
                    const selectedCity = Array.from(this.options).find(option => option.value === cityId);
                    if (selectedCity && selectedCity.dataset.cityId) {
                        loadDistricts(selectedCity.dataset.cityId);
                    }
                } else {
                    resetDistrictSelect();
                }
            });

            function loadProvinces() {
                fetch('{{ route('api.provinces') }}')
                    .then(response => response.json())
                    .then(provinces => {
                        provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                        provinces.forEach(province => {
                            const option = document.createElement('option');
                            option.value = province.id;
                            option.textContent = province.name;
                            provinceSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading provinces:', error);
                        provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
                    });
            }

            function loadCities(provinceId) {
                citySelect.disabled = true;
                citySelect.innerHTML = '<option value="">Memuat kota/kabupaten...</option>';
                resetDistrictSelect();

                fetch(`{{ route('api.cities') }}?province_id=${provinceId}`)
                    .then(response => response.json())
                    .then(cities => {
                        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.name;
                            option.textContent = city.name;
                            option.dataset.cityId = city.id;
                            citySelect.appendChild(option);
                        });
                        citySelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading cities:', error);
                        citySelect.innerHTML = '<option value="">Error loading cities</option>';
                        citySelect.disabled = true;
                    });
            }

            function loadDistricts(regencyId) {
                districtSelect.disabled = true;
                districtSelect.innerHTML = '<option value="">Memuat kecamatan...</option>';

                fetch(`{{ route('api.districts') }}?regency_id=${regencyId}`)
                    .then(response => response.json())
                    .then(districts => {
                        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.name;
                            option.textContent = district.name;
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading districts:', error);
                        districtSelect.innerHTML = '<option value="">Error loading districts</option>';
                        districtSelect.disabled = true;
                    });
            }

            function resetCitySelect() {
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                citySelect.disabled = true;
            }

            function resetDistrictSelect() {
                districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                districtSelect.disabled = true;
            }
        });
    </script>
@endpush

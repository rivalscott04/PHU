@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Jamaah Haji Khusus</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jamaah.haji-khusus.index') }}">Jamaah Haji Khusus</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                    <h4 class="card-title mb-1">Edit Data Jamaah Haji Khusus</h4>
                    <p class="card-title-desc mb-0 text-muted">Perbarui data jamaah haji khusus langkah demi langkah</p>
                </div>
                <a href="{{ route('jamaah.haji-khusus.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i>Kembali ke daftar
                </a>
            </div>
            <div class="card-body">
                <form id="jamaah-haji-khusus-form" action="{{ route('jamaah.haji-khusus.update', $jamaahHajiKhusus->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('jamaah.haji-khusus.partials.form-wizard-progress')

                    <div id="jamaah-haji-khusus-wizard">
                        <h3>Pribadi</h3>
                        <section>
                    <p class="text-muted small mb-3">Identitas jamaah sesuai NIK.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3 position-relative">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                       id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $jamaahHajiKhusus->nama_lengkap) }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-tooltip">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_ktp" class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" 
                                       id="no_ktp" name="no_ktp" value="{{ old('no_ktp', $jamaahHajiKhusus->no_ktp) }}"
                                       data-digits-only="16" inputmode="numeric" pattern="[0-9]*"
                                       maxlength="16" autocomplete="off" spellcheck="false" required>
                                @error('no_ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                       id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $jamaahHajiKhusus->tempat_lahir) }}" required>
                                @error('tempat_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                       id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $jamaahHajiKhusus->tanggal_lahir?->format('Y-m-d')) }}" required>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin', $jamaahHajiKhusus->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $jamaahHajiKhusus->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                          id="alamat" name="alamat" rows="3" required>{{ old('alamat', $jamaahHajiKhusus->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="provinsi" class="form-label">
                                    <i class="bx bx-map-pin me-1"></i>
                                    Provinsi <span class="text-danger">*</span>
                                </label>
                                <select class="form-select location-select @error('provinsi') is-invalid @enderror" 
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kota" class="form-label">
                                    <i class="bx bx-building me-1"></i>
                                    Kota/Kabupaten <span class="text-danger">*</span>
                                </label>
                                <select class="form-select location-select @error('kota') is-invalid @enderror" 
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kecamatan" class="form-label">
                                    <i class="bx bx-map-alt me-1"></i>
                                    Kecamatan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select location-select @error('kecamatan') is-invalid @enderror" 
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" 
                                       id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $jamaahHajiKhusus->kode_pos) }}"
                                       data-digits-only="5" inputmode="numeric" pattern="[0-9]*"
                                       maxlength="5" autocomplete="off" required>
                                @error('kode_pos')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                       id="no_hp" name="no_hp" value="{{ old('no_hp', $jamaahHajiKhusus->no_hp) }}"
                                       data-digits-only="15" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $jamaahHajiKhusus->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_ayah" class="form-label">Nama Ayah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" 
                                       id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $jamaahHajiKhusus->nama_ayah) }}" required>
                                @error('nama_ayah')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="mb-3">
                                <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" 
                                       id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $jamaahHajiKhusus->pekerjaan) }}" required>
                                @error('pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pendidikan_terakhir') is-invalid @enderror" 
                                       id="pendidikan_terakhir" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $jamaahHajiKhusus->pendidikan_terakhir) }}" required>
                                @error('pendidikan_terakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status_pernikahan" class="form-label">Status Pernikahan <span class="text-danger">*</span></label>
                                <select class="form-select @error('status_pernikahan') is-invalid @enderror" 
                                        id="status_pernikahan" name="status_pernikahan" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Belum Menikah" {{ old('status_pernikahan', $jamaahHajiKhusus->status_pernikahan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                    <option value="Menikah" {{ old('status_pernikahan', $jamaahHajiKhusus->status_pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="Cerai" {{ old('status_pernikahan', $jamaahHajiKhusus->status_pernikahan) == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                </select>
                                @error('status_pernikahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pergi_haji" class="form-label">Pergi Haji <span class="text-danger">*</span></label>
                                <select class="form-select @error('pergi_haji') is-invalid @enderror" 
                                        id="pergi_haji" name="pergi_haji" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Belum" {{ old('pergi_haji', $jamaahHajiKhusus->pergi_haji) == 'Belum' ? 'selected' : '' }}>Belum</option>
                                    <option value="Sudah" {{ old('pergi_haji', $jamaahHajiKhusus->pergi_haji) == 'Sudah' ? 'selected' : '' }}>Sudah</option>
                                </select>
                                @error('pergi_haji')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        

                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="golongan_darah" class="form-label">Golongan Darah <span class="text-danger">*</span></label>
                                <select class="form-select @error('golongan_darah') is-invalid @enderror" id="golongan_darah" name="golongan_darah" required>
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="A" {{ old('golongan_darah', $jamaahHajiKhusus->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('golongan_darah', $jamaahHajiKhusus->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('golongan_darah', $jamaahHajiKhusus->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ old('golongan_darah', $jamaahHajiKhusus->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                                @error('golongan_darah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        

                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alergi" class="form-label">Alergi</label>
                                <textarea class="form-control @error('alergi') is-invalid @enderror" 
                                          id="alergi" name="alergi" rows="2">{{ old('alergi', $jamaahHajiKhusus->alergi) }}</textarea>
                                @error('alergi')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="mb-3">
                                <label for="no_paspor" class="form-label">Nomor Paspor</label>
                                <input type="text" class="form-control @error('no_paspor') is-invalid @enderror" 
                                       id="no_paspor" name="no_paspor" value="{{ old('no_paspor', $jamaahHajiKhusus->no_paspor) }}">
                                @error('no_paspor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_berlaku_paspor" class="form-label">Tanggal Berlaku Paspor</label>
                                <input type="date" class="form-control @error('tanggal_berlaku_paspor') is-invalid @enderror" 
                                       id="tanggal_berlaku_paspor" name="tanggal_berlaku_paspor" 
                                       value="{{ old('tanggal_berlaku_paspor', $jamaahHajiKhusus->tanggal_berlaku_paspor?->format('Y-m-d')) }}">
                                @error('tanggal_berlaku_paspor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempat_terbit_paspor" class="form-label">Tempat Terbit Paspor</label>
                                <input type="text" class="form-control @error('tempat_terbit_paspor') is-invalid @enderror" 
                                       id="tempat_terbit_paspor" name="tempat_terbit_paspor" value="{{ old('tempat_terbit_paspor', $jamaahHajiKhusus->tempat_terbit_paspor) }}">
                                @error('tempat_terbit_paspor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_porsi" class="form-label">
                                    Nomor SPPH
                                    @if(auth()->user()->role === 'user')
                                        <span class="text-muted">(Admin Only)</span>
                                    @endif
                                </label>
                                <input type="text" class="form-control @error('nomor_porsi') is-invalid @enderror"
                                       id="nomor_porsi" name="nomor_porsi"
                                       value="{{ old('nomor_porsi', $jamaahHajiKhusus->nomor_porsi) }}"
                                       maxlength="20"
                                       @if(auth()->user()->role === 'user') disabled @endif>
                                @if(auth()->user()->role === 'user')
                                    <small class="text-muted">Nomor SPPH akan ditetapkan oleh admin setelah verifikasi bukti setor bank</small>
                                @endif
                                @error('nomor_porsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tahun_pendaftaran" class="form-label">
                                    Tahun Pendaftaran
                                    @if(auth()->user()->role === 'user')
                                        <span class="text-muted">(Ditetapkan oleh Kanwil)</span>
                                    @endif
                                </label>
                                <input type="date" class="form-control @error('tahun_pendaftaran') is-invalid @enderror"
                                       id="tahun_pendaftaran" name="tahun_pendaftaran"
                                       value="{{ old('tahun_pendaftaran', $jamaahHajiKhusus->tahun_pendaftaran) }}"
                                       @if(auth()->user()->role === 'user') disabled @endif>
                                @if(auth()->user()->role === 'user')
                                    <small class="text-muted">Tahun pendaftaran akan ditetapkan oleh Kanwil</small>
                                @endif
                                @error('tahun_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if($jamaahHajiKhusus->bukti_setor_bank)
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Status Bukti Setor Bank
                                </h6>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge {{ $jamaahHajiKhusus->getBuktiSetorStatusBadgeClass() }}">
                                        {{ $jamaahHajiKhusus->getBuktiSetorStatusText() }}
                                    </span>
                                    @if($jamaahHajiKhusus->catatan_verifikasi)
                                        <span class="text-muted">| {{ $jamaahHajiKhusus->catatan_verifikasi }}</span>
                                    @endif
                                    @if($jamaahHajiKhusus->tanggal_verifikasi)
                                        <span class="text-muted">| {{ $jamaahHajiKhusus->tanggal_verifikasi->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                        </section>

                        <h3>Dokumen</h3>
                        <section>
                    <p class="text-muted small mb-3">Upload dokumen pendukung. Format PDF/JPG/PNG, maks. 2MB per file.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dokumen_ktp" class="form-label">Dokumen KTP</label>
                                <input type="file" class="form-control @error('dokumen_ktp') is-invalid @enderror" 
                                       id="dokumen_ktp" name="dokumen_ktp" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                @if($jamaahHajiKhusus->dokumen_ktp)
                                    <div class="mt-1">
                                        @if(Str::endsWith($jamaahHajiKhusus->dokumen_ktp, '.pdf'))
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPdfPreview('{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_ktp) }}', 'Dokumen KTP')">
                                                <i class="bx bx-file-find me-1"></i>Lihat Dokumen
                                            </button>
                                        @else
                                            <a href="{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_ktp) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-image me-1"></i>Lihat Dokumen
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @error('dokumen_ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dokumen_kk" class="form-label">Dokumen KK</label>
                                <input type="file" class="form-control @error('dokumen_kk') is-invalid @enderror" 
                                       id="dokumen_kk" name="dokumen_kk" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                @if($jamaahHajiKhusus->dokumen_kk)
                                    <div class="mt-1">
                                        @if(Str::endsWith($jamaahHajiKhusus->dokumen_kk, '.pdf'))
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPdfPreview('{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_kk) }}', 'Dokumen KK')">
                                                <i class="bx bx-file-find me-1"></i>Lihat Dokumen
                                            </button>
                                        @else
                                            <a href="{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_kk) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-image me-1"></i>Lihat Dokumen
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @error('dokumen_kk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dokumen_paspor" class="form-label">Dokumen Paspor</label>
                                <input type="file" class="form-control @error('dokumen_paspor') is-invalid @enderror" 
                                       id="dokumen_paspor" name="dokumen_paspor" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                @if($jamaahHajiKhusus->dokumen_paspor)
                                    <div class="mt-1">
                                        @if(Str::endsWith($jamaahHajiKhusus->dokumen_paspor, '.pdf'))
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPdfPreview('{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_paspor) }}', 'Dokumen Paspor')">
                                                <i class="bx bx-file-find me-1"></i>Lihat Dokumen
                                            </button>
                                        @else
                                            <a href="{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_paspor) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-image me-1"></i>Lihat Dokumen
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @error('dokumen_paspor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dokumen_foto" class="form-label">Foto 3x4</label>
                                <input type="file" class="form-control @error('dokumen_foto') is-invalid @enderror" 
                                       id="dokumen_foto" name="dokumen_foto" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Format: JPG, JPEG, PNG (Max: 2MB)</small>
                                @if($jamaahHajiKhusus->dokumen_foto)
                                    <div class="mt-1">
                                        <a href="{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->dokumen_foto) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-download me-1"></i>Lihat Dokumen
                                        </a>
                                    </div>
                                @endif
                                @error('dokumen_foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="surat_keterangan" class="form-label">Surat Keterangan</label>
                                <input type="file" class="form-control @error('surat_keterangan') is-invalid @enderror" 
                                       id="surat_keterangan" name="surat_keterangan" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                @if($jamaahHajiKhusus->surat_keterangan)
                                    <div class="mt-1">
                                        @if(Str::endsWith($jamaahHajiKhusus->surat_keterangan, '.pdf'))
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPdfPreview('{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->surat_keterangan) }}', 'Surat Keterangan')">
                                                <i class="bx bx-file-find me-1"></i>Lihat Dokumen
                                            </button>
                                        @else
                                            <a href="{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->surat_keterangan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-image me-1"></i>Lihat Dokumen
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @error('surat_keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bukti_setor_bank" class="form-label">Bukti Setor Bank</label>
                                <input type="file" class="form-control @error('bukti_setor_bank') is-invalid @enderror"
                                       id="bukti_setor_bank" name="bukti_setor_bank" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                @if($jamaahHajiKhusus->bukti_setor_bank)
                                    <div class="mt-1">
                                        @if(Str::endsWith($jamaahHajiKhusus->bukti_setor_bank, '.pdf'))
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPdfPreview('{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->bukti_setor_bank) }}', 'Bukti Setor Bank')">
                                                <i class="bx bx-file-find me-1"></i>Lihat Bukti Setor
                                            </button>
                                        @else
                                            <a href="{{ \App\Helpers\StorageHelper::publicUrl($jamaahHajiKhusus->bukti_setor_bank) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-image me-1"></i>Lihat Bukti Setor
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                @error('bukti_setor_bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
    'finishLabel' => 'Update Data',
    'locationDefaults' => [
        'provinsi' => old('provinsi', $jamaahHajiKhusus->provinsi),
        'kota' => old('kota', $jamaahHajiKhusus->kota),
        'kecamatan' => old('kecamatan', $jamaahHajiKhusus->kecamatan),
    ],
])

@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Detail Jamaah Haji Khusus</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jamaah.haji-khusus.index') }}">Jamaah Haji Khusus</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Informasi Jamaah Haji Khusus</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('jamaah.haji-khusus.edit', $jamaahHajiKhusus->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i>
                            Edit
                        </a>
                        <a href="{{ route('jamaah.haji-khusus.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bx bx-user me-2"></i>Informasi Pribadi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Nama Lengkap:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->nama_lengkap }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>No. KTP:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <code>{{ $jamaahHajiKhusus->no_ktp }}</code>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Tempat Lahir:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->tempat_lahir }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Tanggal Lahir:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->tanggal_lahir ? $jamaahHajiKhusus->tanggal_lahir->format('d/m/Y') : '-' }}
                                        @if($jamaahHajiKhusus->tanggal_lahir)
                                            <br><small class="text-muted">({{ $jamaahHajiKhusus->getAge() }} tahun)</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Jenis Kelamin:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge {{ $jamaahHajiKhusus->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-pink' }}">
                                            {{ $jamaahHajiKhusus->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Golongan Darah:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-info">{{ $jamaahHajiKhusus->golongan_darah }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Status Pernikahan:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->status_pernikahan }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bx bx-phone me-2"></i>Informasi Kontak</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Alamat:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->alamat }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Kota:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->kota }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Provinsi:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->provinsi }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Kode Pos:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->kode_pos }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>No. HP:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <a href="tel:{{ $jamaahHajiKhusus->no_hp }}" class="text-decoration-none">
                                            {{ $jamaahHajiKhusus->no_hp }}
                                        </a>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        @if($jamaahHajiKhusus->email)
                                            <a href="mailto:{{ $jamaahHajiKhusus->email }}" class="text-decoration-none">
                                                {{ $jamaahHajiKhusus->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Additional Information -->
                    <div class="col-md-6">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="bx bx-info-circle me-2"></i>Informasi Tambahan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Nama Ayah:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->nama_ayah }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Pekerjaan:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->pekerjaan }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Pendidikan Terakhir:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->pendidikan_terakhir }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Pergi Haji:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        @if($jamaahHajiKhusus->pergi_haji)
                                            <span class="badge {{ $jamaahHajiKhusus->pergi_haji === 'Sudah' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $jamaahHajiKhusus->pergi_haji }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Alergi:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->alergi ?: '-' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Catatan Khusus:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->catatan_khusus ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Information -->
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bx bx-building me-2"></i>Informasi PPIU</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>PPIU:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->travel->Penyelenggara ?? 'Tidak Diketahui' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Kabupaten/Kota:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->travel->kab_kota ?? 'Tidak Diketahui' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Status PPIU:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge {{ $jamaahHajiKhusus->travel->Status === 'PPIU' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $jamaahHajiKhusus->travel->Status ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Status Pendaftaran:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge {{ $jamaahHajiKhusus->getStatusBadgeClass() }}">
                                            {{ $jamaahHajiKhusus->getStatusText() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Tanggal Daftar:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $jamaahHajiKhusus->created_at ? $jamaahHajiKhusus->created_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Information -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="bx bx-file me-2"></i>Informasi Dokumen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Dokumen Pribadi</h6>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>No. Paspor:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                @if($jamaahHajiKhusus->no_paspor)
                                                    <code>{{ $jamaahHajiKhusus->no_paspor }}</code>
                                                    @if($jamaahHajiKhusus->tanggal_berlaku_paspor)
                                                        <br><small class="text-muted">Berlaku: {{ $jamaahHajiKhusus->tanggal_berlaku_paspor->format('d/m/Y') }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>No. SPPH:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                @if($jamaahHajiKhusus->nomor_porsi)
                                                    <code>{{ $jamaahHajiKhusus->nomor_porsi }}</code>
                                                    @if($jamaahHajiKhusus->tahun_pendaftaran)
                                                        <br><small class="text-muted">Tahun: {{ $jamaahHajiKhusus->tahun_pendaftaran }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Bukti Setor Bank</h6>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>Status:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                @if($jamaahHajiKhusus->bukti_setor_bank)
                                                    <span class="badge {{ $jamaahHajiKhusus->getBuktiSetorStatusBadgeClass() }}">
                                                        {{ $jamaahHajiKhusus->getBuktiSetorStatusText() }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Belum Upload</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <strong>File:</strong>
                                            </div>
                                            <div class="col-sm-8">
                                                @if($jamaahHajiKhusus->bukti_setor_bank)
                                                    <a href="{{ Storage::url($jamaahHajiKhusus->bukti_setor_bank) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-download me-1"></i>
                                                        Download Bukti Setor
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

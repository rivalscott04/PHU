@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Detail Pengaduan</h4>
                    <p class="text-muted mb-0 small">Informasi lengkap pengaduan</p>
                </div>
                <a href="{{ route('pengaduan') }}" class="btn btn-sm btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Data Pengaduan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pengadu</label>
                            <input type="text" class="form-control" value="{{ $pengaduan->nama_pengadu }}" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Travel</label>
                            <input type="text" class="form-control" value="{{ $pengaduan->travel->Penyelenggara }}"
                                readonly>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Hal yang Diadukan</label>
                            <textarea class="form-control" rows="4" readonly>{{ $pengaduan->hal_aduan }}</textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Berkas Pendukung</label>
                            @if ($pengaduan->berkas_aduan)
                                <div>
                                    <a href="{{ route('pengaduan.download-berkas', $pengaduan->id) }}" class="btn btn-sm btn-info"
                                        target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-download"></i> Lihat Berkas
                                    </a>
                                </div>
                            @else
                                <p class="text-muted mb-0">Tidak ada berkas pendukung</p>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pengaduan</label>
                            <input type="text" class="form-control"
                                value="{{ $pengaduan->created_at->format('d/m/Y H:i') }}" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $pengaduan->status }}" readonly>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

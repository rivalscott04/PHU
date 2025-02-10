@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pengaduan</h5>
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
                                    <a href="{{ asset('storage/' . $pengaduan->berkas_aduan) }}" class="btn btn-sm btn-info"
                                        target="_blank">
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

                    <div class="mt-3">
                        <a href="{{ route('pengaduan') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

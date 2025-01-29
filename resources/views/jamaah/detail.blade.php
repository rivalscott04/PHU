@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Jamaah</h5>
                    <div>
                        <a href="{{ route('jamaah.edit', $jamaah->id) }}" class="btn btn-primary">Edit</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" value="{{ $jamaah->nik }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" value="{{ $jamaah->nama }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" rows="3" readonly>{{ $jamaah->alamat }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nomor_hp" class="form-label">Nomor HP</label>
                            <input type="text" class="form-control" id="nomor_hp" value="{{ $jamaah->nomor_hp }}"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

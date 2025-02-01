@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pengaduan</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pengadu" class="form-label">Nama Pengadu</label>
                                <input type="text" class="form-control" id="nama_pengadu" name="nama_pengadu"
                                    value="{{ old('nama_pengadu') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="travels_id" class="form-label">Travel</label>
                                <select class="form-control" id="travels_id" name="travels_id" required>
                                    <option value="">-- Pilih Travel --</option>
                                    @foreach ($travels as $travel)
                                        <option value="{{ $travel->id }}">{{ $travel->Penyelenggara }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-12 mb-3">
                                <label for="hal_aduan" class="form-label">Hal yang Diadukan</label>
                                <textarea class="form-control" id="hal_aduan" name="hal_aduan" rows="4" required>{{ old('hal_aduan') }}</textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="berkas_aduan" class="form-label">Berkas Pendukung</label>
                                <input type="file" class="form-control" id="berkas_aduan" name="berkas_aduan">
                                <small class="text-muted">File maksimal 2MB</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
                            <a href="{{ route('pengaduan') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

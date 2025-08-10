@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Cabang Travel</h5>
                    <a href="{{ route('cabang.travel') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cabang.travel.update', $cabangTravel->id_cabang) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Penyelenggara" class="form-label">Penyelenggara</label>
                                <input type="text" class="form-control" id="Penyelenggara" name="Penyelenggara"
                                    value="{{ old('Penyelenggara', $cabangTravel->Penyelenggara) }}" required>
                                @error('Penyelenggara')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control" id="kabupaten" name="kabupaten"
                                    value="{{ old('kabupaten', $cabangTravel->kabupaten) }}" required>
                                @error('kabupaten')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pusat" class="form-label">Pusat</label>
                                <input type="text" class="form-control" id="pusat" name="pusat"
                                    value="{{ old('pusat', $cabangTravel->pusat) }}">
                                @error('pusat')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pimpinan_pusat" class="form-label">Pimpinan Pusat</label>
                                <input type="text" class="form-control" id="pimpinan_pusat" name="pimpinan_pusat"
                                    value="{{ old('pimpinan_pusat', $cabangTravel->pimpinan_pusat) }}" required>
                                @error('pimpinan_pusat')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat_pusat" class="form-label">Alamat Kantor Pusat</label>
                                <textarea class="form-control" id="alamat_pusat" name="alamat_pusat" required>{{ old('alamat_pusat', $cabangTravel->alamat_pusat) }}</textarea>
                                @error('alamat_pusat')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="SK_BA" class="form-label">No SK / BA</label>
                                <input type="text" class="form-control" id="SK_BA" name="SK_BA"
                                    value="{{ old('SK_BA', $cabangTravel->SK_BA) }}">
                                @error('SK_BA')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ old('tanggal', $cabangTravel->tanggal && $cabangTravel->tanggal instanceof \Carbon\Carbon ? $cabangTravel->tanggal->format('Y-m-d') : $cabangTravel->tanggal) }}">
                                @error('tanggal')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pimpinan_cabang" class="form-label">Pimpinan Cabang</label>
                                <input type="text" class="form-control" id="pimpinan_cabang" name="pimpinan_cabang"
                                    value="{{ old('pimpinan_cabang', $cabangTravel->pimpinan_cabang) }}" required>
                                @error('pimpinan_cabang')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat_cabang" class="form-label">Alamat Kantor Cabang</label>
                                <textarea class="form-control" id="alamat_cabang" name="alamat_cabang" required>{{ old('alamat_cabang', $cabangTravel->alamat_cabang) }}</textarea>
                                @error('alamat_cabang')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon"
                                    value="{{ old('telepon', $cabangTravel->telepon) }}" required>
                                @error('telepon')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Data
                            </button>
                            <a href="{{ route('cabang.travel') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

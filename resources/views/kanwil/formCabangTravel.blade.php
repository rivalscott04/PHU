@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Data Cabang Travel</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('post.cabang_travel') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="travel_pusat" class="form-label">Travel Pusat</label>
                                <select class="form-control" id="travel_pusat" name="travel_pusat" required>
                                    @foreach ($travels as $travel)
                                        <option value="{{ $travel->id }}">{{ $travel->Penyelenggara }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sk_ba" class="form-label">No SK / BA</label>
                                <input type="text" class="form-control" id="sk_ba" name="sk_ba" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pimpinan_cabang" class="form-label">Pimpinan Cabang</label>
                                <input type="text" class="form-control" id="pimpinan_cabang" name="pimpinan_cabang"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="form-label">Alamat Kantor Cabang</label>
                                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

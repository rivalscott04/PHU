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


    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pengunduran Diri Travel</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pengunduran.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Travel</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->username }}" readonly>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="berkas_pengunduran" class="form-label">Berkas Pengunduran Diri</label>
                                <input type="file" class="form-control" id="berkas_pengunduran" name="berkas_pengunduran"
                                    required>
                                <small class="text-muted">File maksimal 2MB</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Kirim Berkas</button>
                            <a href="{{ route('home') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

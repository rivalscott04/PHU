@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Data Cabang Travel</h5>
                    <div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Existing Manual Input Form -->
                    <form method="POST" action="{{ route('post.cabang_travel') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="travel_id" class="form-label">Travel Pusat</label>
                                <select class="form-control" id="travel_id" name="travel_id" required>
                                    <option value="">Pilih Travel</option>
                                    @foreach ($travels as $travel)
                                        <option value="{{ $travel->id }}">{{ $travel->Penyelenggara }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kabupaten" class="form-label">Kabupaten</label>
                                <input type="text" class="form-control" id="kabupaten" name="kabupaten" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pusat" class="form-label">Pusat</label>
                                <input type="text" class="form-control" id="pusat" name="pusat" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pimpinan_pusat" class="form-label">Pimpinan Pusat</label>
                                <input type="text" class="form-control" id="pimpinan_pusat" name="pimpinan_pusat"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat_pusat" class="form-label">Alamat Kantor Pusat</label>
                                <textarea class="form-control" id="alamat_pusat" name="alamat_pusat" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="SK_BA" class="form-label">No SK / BA</label>
                                <input type="text" class="form-control" id="SK_BA" name="SK_BA" required>
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
                                <label for="alamat_cabang" class="form-label">Alamat Kantor Cabang</label>
                                <textarea class="form-control" id="alamat_cabang" name="alamat_cabang" required></textarea>
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

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data Cabang Travel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import.cabang_travel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept=".xlsx, .xls" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('download.template.cabang_travel') }}" class="text-sm">
                                <i class="bx bx-download"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Tambah Cabang Travel</h4>
                    <p class="text-muted mb-0 small">Input data cabang PPIU baru</p>
                </div>
                <a href="{{ route('cabang.travel') }}" class="btn btn-sm btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Data Cabang Travel</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>

                    <!-- Existing Manual Input Form -->
                    <form method="POST" action="{{ route('post.cabang_travel') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="travel_id" class="form-label">Travel Pusat</label>
                                <select class="form-select" id="travel_id" name="travel_id">
                                    <option value="">Tidak terhubung (data lama)</option>
                                    @foreach ($travels as $travel)
                                        <option value="{{ $travel->id }}"
                                            data-nama="{{ $travel->Penyelenggara }}"
                                            data-sk="{{ $travel->Pusat }}"
                                            data-pimpinan="{{ $travel->Pimpinan }}"
                                            data-alamat="{{ $travel->alamat_kantor_baru ?: $travel->alamat_kantor_lama }}"
                                            @selected(old('travel_id') == $travel->id)>
                                            {{ $travel->Penyelenggara }} ({{ $travel->kab_kota }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih untuk mengisi otomatis data pusat di bawah</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="Penyelenggara" class="form-label">Penyelenggara @include('partials.required-star')</label>
                                <input type="text" class="form-control" id="Penyelenggara" name="Penyelenggara" value="{{ old('Penyelenggara') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kabupaten" class="form-label">Kabupaten @include('partials.required-star')</label>
                                <select class="form-select @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten" required>
                                    <option value="">Pilih kabupaten/kota</option>
                                    @foreach ($kabupatens as $kabupaten)
                                        <option value="{{ $kabupaten }}" @selected(old('kabupaten') === $kabupaten)>{{ $kabupaten }}</option>
                                    @endforeach
                                </select>
                                @error('kabupaten')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pusat" class="form-label">No. SK Pusat</label>
                                <input type="text" class="form-control" id="pusat" name="pusat" value="{{ old('pusat') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pimpinan_pusat" class="form-label">Pimpinan Pusat @include('partials.required-star')</label>
                                <input type="text" class="form-control" id="pimpinan_pusat" name="pimpinan_pusat"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat_pusat" class="form-label">Alamat Kantor Pusat @include('partials.required-star')</label>
                                <textarea class="form-control" id="alamat_pusat" name="alamat_pusat" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="SK_BA" class="form-label">No SK / BA</label>
                                <input type="text" class="form-control" id="SK_BA" name="SK_BA">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pimpinan_cabang" class="form-label">Pimpinan Cabang @include('partials.required-star')</label>
                                <input type="text" class="form-control" id="pimpinan_cabang" name="pimpinan_cabang"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat_cabang" class="form-label">Alamat Kantor Cabang @include('partials.required-star')</label>
                                <textarea class="form-control" id="alamat_cabang" name="alamat_cabang" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon @include('partials.required-star')</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" maxlength="16" inputmode="numeric" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cabang.travel') }}" class="btn btn-sm btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('travel_id')?.addEventListener('change', function () {
            const data = this.selectedOptions[0]?.dataset;

            if (!this.value || !data) {
                return;
            }

            document.getElementById('Penyelenggara').value = data.nama || '';
            document.getElementById('pusat').value = data.sk || '';
            document.getElementById('pimpinan_pusat').value = data.pimpinan || '';
            document.getElementById('alamat_pusat').value = data.alamat || '';
        });
    </script>

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

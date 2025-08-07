@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail BAP</h5>
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'user')
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#uploadPDFModal">Upload Surat Pernytaan PDF</button>
                    @endif
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            @if ($data->pdf_file_path)
                                <div class="col-md-6 mb-3">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="name" class="form-label">Nama</label>
                                            <input type="text" class="form-control" id="name"
                                                value="{{ $data->name }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="jabatan" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan"
                                                value="{{ $data->jabatan }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="ppiuname" class="form-label">PPIU</label>
                                            <input type="text" class="form-control" id="ppiuname"
                                                value="{{ $data->ppiuname }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="address_phone" class="form-label">Alamat</label>
                                            <input type="text" class="form-control" id="address_phone"
                                                value="{{ $data->address_phone }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="kab_kota" class="form-label">Kab/Kota</label>
                                            <input type="text" class="form-control" id="kab_kota"
                                                value="{{ $data->kab_kota }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="people" class="form-label">Orang</label>
                                            <input type="text" class="form-control" id="people"
                                                value="{{ $data->people }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="package" class="form-label">Paket</label>
                                            <input type="text" class="form-control" id="package"
                                                value="{{ $data->package }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="price" class="form-label">Harga</label>
                                            <input type="text" class="form-control" id="price"
                                                value="{{ $data->price }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="datetime" class="form-label">Tanggal Keberangkatan</label>
                                            <input type="text" class="form-control" id="datetime"
                                                value="{{ $data->datetime }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="airlines" class="form-label">Maskapai Keberangkatan</label>
                                            <input type="text" class="form-control" id="airlines"
                                                value="{{ $data->airlines }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="returndate" class="form-label">Tanggal Kepulangan</label>
                                            <input type="text" class="form-control" id="returndate"
                                                value="{{ $data->returndate }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="airlines2" class="form-label">Maskapai Kepulangan</label>
                                            <input type="text" class="form-control" id="airlines2"
                                                value="{{ $data->airlines2 }}" disabled>
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h5 class="mb-3">Pernyataan yang diupload</h5>
                                    <iframe src="{{ asset('storage/' . $data->pdf_file_path) }}" width="100%"
                                        height="500px"></iframe>
                                </div>
                            @else
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Nama</label>
                                            <input type="text" class="form-control" id="name"
                                                value="{{ $data->name }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="jabatan" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan"
                                                value="{{ $data->jabatan }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="ppiuname" class="form-label">PPIU</label>
                                            <input type="text" class="form-control" id="ppiuname"
                                                value="{{ $data->ppiuname }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="address_phone" class="form-label">Alamat</label>
                                            <input type="text" class="form-control" id="address_phone"
                                                value="{{ $data->address_phone }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="kab_kota" class="form-label">Kab/Kota</label>
                                            <input type="text" class="form-control" id="kab_kota"
                                                value="{{ $data->kab_kota }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="people" class="form-label">Orang</label>
                                            <input type="text" class="form-control" id="people"
                                                value="{{ $data->people }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="package" class="form-label">Paket</label>
                                            <input type="text" class="form-control" id="package"
                                                value="{{ $data->package }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="price" class="form-label">Harga</label>
                                            <input type="text" class="form-control" id="price"
                                                value="{{ $data->price }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="datetime" class="form-label">Tanggal Keberangkatan</label>
                                            <input type="text" class="form-control" id="datetime"
                                                value="{{ $data->datetime }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="airlines" class="form-label">Maskapai Keberangkatan</label>
                                            <input type="text" class="form-control" id="airlines"
                                                value="{{ $data->airlines }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="returndate" class="form-label">Tanggal Kepulangan</label>
                                            <input type="text" class="form-control" id="returndate"
                                                value="{{ $data->returndate }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="airlines2" class="form-label">Maskapai Kepulangan</label>
                                            <input type="text" class="form-control" id="airlines2"
                                                value="{{ $data->airlines2 }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    @if ($data->pdf_file_path && auth()->user()->role === 'user')
                        <form action="{{ route('bap.ajukan', ['id' => $data->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary mt-2">Ajukan</button>
                        </form>
                    @endif
                    @if ($data->pdf_file_path && (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten'))
                        <form action="{{ route('bap.updateStatus', $data->id) }}" method="POST">
                            <div class="col-md-2">
                                @csrf
                                <select name="status"
                                    class="form-select mt-1 {{ $data->status == 'diajukan' ? 'bg-primary text-white fw-semibold' : '' }}
                                                            {{ $data->status == 'diproses' ? 'bg-warning text-dark fw-semibold' : '' }}
                                                            {{ $data->status == 'diterima' ? 'bg-success text-white fw-semibold' : '' }}"
                                    onchange="this.form.submit()">
                                    <option value="pending" {{ $data->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="diajukan" {{ $data->status == 'diajukan' ? 'selected' : '' }}>Diajukan
                                    </option>
                                    <option value="diproses" {{ $data->status == 'diproses' ? 'selected' : '' }}>Diproses
                                    </option>
                                    <option value="diterima" {{ $data->status == 'diterima' ? 'selected' : '' }}>Diterima
                                    </option>
                                </select>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- Modal for Uploading PDF -->
<div id="uploadPDFModal" class="modal fade" tabindex="-1" aria-labelledby="uploadPDFModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPDFModalLabel">Upload PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('bap.upload', ['id' => $data->id]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="pdf_file" class="form-label">Upload PDF file</label>
                        <input type="file" class="form-control" id="pdf_file" name="pdf_file"
                            accept="application/pdf" required>
                    </div>
                    <!-- Button container -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

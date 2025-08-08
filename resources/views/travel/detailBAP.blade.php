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
                        <form action="{{ route('bap.updateStatus', $data->id) }}" method="POST" id="statusFormDetail">
                            <div class="row">
                                <div class="col-md-3">
                                    @csrf
                                    <select name="status"
                                        class="form-select mt-1 {{ $data->status == 'diajukan' ? 'bg-primary text-white fw-semibold' : '' }}
                                                                {{ $data->status == 'diproses' ? 'bg-warning text-dark fw-semibold' : '' }}
                                                                {{ $data->status == 'diterima' ? 'bg-success text-white fw-semibold' : '' }}"
                                        onchange="handleStatusChangeDetail(this.value)">
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
                                <div class="col-md-6">
                                    <div id="nomorSuratContainerDetail" 
                                         class="mt-1" 
                                         style="display: {{ $data->status == 'diproses' ? 'block' : 'none' }};">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="number" 
                                                       name="nomor_surat" 
                                                       class="form-control" 
                                                       placeholder="Nomor (001)"
                                                       min="1" max="999">
                                                <small class="text-muted">Nomor (1-999)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" 
                                                       name="bulan_surat" 
                                                       class="form-control" 
                                                       placeholder="Bulan (08)"
                                                       min="1" max="12"
                                                       value="{{ date('m') }}">
                                                <small class="text-muted">Bulan (01-12)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" 
                                                       name="tahun_surat" 
                                                       class="form-control" 
                                                       placeholder="Tahun (2025)"
                                                       min="2020" max="2030"
                                                       value="{{ date('Y') }}">
                                                <small class="text-muted">Tahun</small>
                                            </div>
                                        </div>
                                        <small class="text-muted">Format: B-xxx/Kw.18.04/2/Hj.00/xx/xxxx</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                    
                    @if($data->status === 'diterima')
                        <div class="mt-3">
                            <a href="{{ route('cetak.bap', $data->id) }}" class="btn btn-success">
                                <i class="bx bx-printer me-2"></i>Cetak BAP
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function handleStatusChangeDetail(status) {
        const container = document.getElementById('nomorSuratContainerDetail');
        const form = document.getElementById('statusFormDetail');
        
        if (status === 'diproses') {
            container.style.display = 'block';
            // Tampilkan modal untuk input nomor surat
            Swal.fire({
                title: 'Input Nomor Surat',
                html: `
                    <div class="row">
                        <div class="col-md-4">
                            <input type="number" id="nomorSuratInput" class="form-control" 
                                   placeholder="Nomor (001)" min="1" max="999">
                            <small class="text-muted">Nomor (1-999)</small>
                        </div>
                        <div class="col-md-4">
                            <input type="number" id="bulanSuratInput" class="form-control" 
                                   placeholder="Bulan (08)" min="1" max="12" value="${new Date().getMonth() + 1}">
                            <small class="text-muted">Bulan (01-12)</small>
                        </div>
                        <div class="col-md-4">
                            <input type="number" id="tahunSuratInput" class="form-control" 
                                   placeholder="Tahun (2025)" min="2020" max="2030" value="${new Date().getFullYear()}">
                            <small class="text-muted">Tahun</small>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Format: B-xxx/Kw.18.04/2/Hj.00/xx/xxxx</small>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const nomor = document.getElementById('nomorSuratInput').value;
                    const bulan = document.getElementById('bulanSuratInput').value;
                    const tahun = document.getElementById('tahunSuratInput').value;
                    
                    if (!nomor || !bulan || !tahun) {
                        Swal.showValidationMessage('Semua field harus diisi');
                        return false;
                    }
                    
                    if (nomor < 1 || nomor > 999) {
                        Swal.showValidationMessage('Nomor harus antara 1-999');
                        return false;
                    }
                    
                    if (bulan < 1 || bulan > 12) {
                        Swal.showValidationMessage('Bulan harus antara 1-12');
                        return false;
                    }
                    
                    if (tahun < 2020 || tahun > 2030) {
                        Swal.showValidationMessage('Tahun harus antara 2020-2030');
                        return false;
                    }
                    
                    return { nomor, bulan, tahun };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set nilai ke input form
                    const nomorInput = form.querySelector('input[name="nomor_surat"]');
                    const bulanInput = form.querySelector('input[name="bulan_surat"]');
                    const tahunInput = form.querySelector('input[name="tahun_surat"]');
                    
                    nomorInput.value = result.value.nomor;
                    bulanInput.value = result.value.bulan;
                    tahunInput.value = result.value.tahun;
                    
                    // Submit form
                    form.submit();
                } else {
                    // Reset dropdown ke nilai sebelumnya
                    const select = form.querySelector('select[name="status"]');
                    select.value = '{{ $data->status ?? "pending" }}';
                    container.style.display = 'none';
                }
            });
        } else if (status === 'diterima') {
            // Cek apakah sudah ada nomor surat di database (untuk data yang sudah diproses)
            const currentStatus = '{{ $data->status ?? "pending" }}';
            const hasNomorSurat = '{{ $data->nomor_surat ?? "" }}' !== '';
            
            if (currentStatus !== 'diproses' && !hasNomorSurat) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Status harus diubah ke "diproses" terlebih dahulu dan nomor surat harus diisi sebelum bisa diubah menjadi "diterima".',
                    confirmButtonText: 'Tutup'
                });
                
                // Reset dropdown ke nilai sebelumnya
                const select = form.querySelector('select[name="status"]');
                select.value = currentStatus;
                return;
            }
            
            // Jika sudah diproses dan ada nomor surat, submit form
            form.submit();
        } else {
            container.style.display = 'none';
            // Submit form langsung untuk status selain diproses dan diterima
            form.submit();
        }
    }
</script>
@endpush

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

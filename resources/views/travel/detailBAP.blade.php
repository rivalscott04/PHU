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
                            </div>
                        </form>
                    @endif
                    
                    @if($data->status === 'diterima')
                        <div class="mt-3">
                            <a href="{{ route('cetak.bap', $data->id) }}" target="_blank" class="btn btn-success">
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
        const form = document.getElementById('statusFormDetail');
        
        if (status === 'diproses') {
            // Tampilkan modal untuk input nomor surat
            $('#statusModal').modal('show');
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
            // Submit form langsung untuk status selain diproses dan diterima
            form.submit();
        }
    }

    function submitStatusWithNomorSurat() {
        const form = document.getElementById('statusFormDetail');
        const nomorSurat = document.getElementById('nomor_surat').value;
        const bulanSurat = document.getElementById('bulan_surat').value;
        const tahunSurat = document.getElementById('tahun_surat').value;
        
        if (!nomorSurat) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Nomor surat harus diisi!',
                confirmButtonText: 'Tutup'
            });
            return;
        }
        
        // Tambahkan input fields ke form
        const nomorInput = document.createElement('input');
        nomorInput.type = 'hidden';
        nomorInput.name = 'nomor_surat';
        nomorInput.value = nomorSurat;
        form.appendChild(nomorInput);
        
        const bulanInput = document.createElement('input');
        bulanInput.type = 'hidden';
        bulanInput.name = 'bulan_surat';
        bulanInput.value = bulanSurat;
        form.appendChild(bulanInput);
        
        const tahunInput = document.createElement('input');
        tahunInput.type = 'hidden';
        tahunInput.name = 'tahun_surat';
        tahunInput.value = tahunSurat;
        form.appendChild(tahunInput);
        
        // Submit form
        form.submit();
    }

    // Update preview when input values change
    document.addEventListener('DOMContentLoaded', function() {
        const nomorInput = document.getElementById('nomor_surat');
        const bulanSelect = document.getElementById('bulan_surat');
        const tahunInput = document.getElementById('tahun_surat');
        
        function updatePreview() {
            const nomor = nomorInput.value || '001';
            const bulan = bulanSelect.value || '01';
            const tahun = tahunInput.value || '{{ date('Y') }}';
            
            document.getElementById('preview_nomor').textContent = nomor.padStart(3, '0');
            document.getElementById('preview_bulan').textContent = bulan;
            document.getElementById('preview_tahun').textContent = tahun;
        }
        
        nomorInput.addEventListener('input', updatePreview);
        bulanSelect.addEventListener('change', updatePreview);
        tahunInput.addEventListener('input', updatePreview);
        
        // Set default values
        bulanSelect.value = '{{ date('m') }}';
        updatePreview();
    });
</script>
@endpush

<!-- Modal for Status Update with Nomor Surat -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="statusModalLabel" style="font-size: 14px;">Update Status BAP</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" style="font-size: 14px;">Untuk mengubah status menjadi "Diproses", silakan isi nomor surat tugas:</p>
                <div class="row">
                    <div class="col-md-4">
                        <label for="nomor_surat" class="form-label" style="font-size: 14px; font-weight: 500;">Nomor Surat *</label>
                        <input type="number" class="form-control" id="nomor_surat" name="nomor_surat" 
                               placeholder="001" min="1" max="999" required style="font-size: 14px;">
                        <small class="text-muted" style="font-size: 12px;">Contoh: 001, 002, dst</small>
                    </div>
                    <div class="col-md-4">
                        <label for="bulan_surat" class="form-label" style="font-size: 14px; font-weight: 500;">Bulan</label>
                        <select class="form-select" id="bulan_surat" name="bulan_surat" style="font-size: 14px;">
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tahun_surat" class="form-label" style="font-size: 14px; font-weight: 500;">Tahun</label>
                        <input type="number" class="form-control" id="tahun_surat" name="tahun_surat" 
                               value="{{ date('Y') }}" min="2020" max="2030" required style="font-size: 14px;">
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted" style="font-size: 13px;">
                        Format nomor surat akan menjadi: B-<span id="preview_nomor">001</span>/Kw.18.04/2/Hj.00/<span id="preview_bulan">01</span>/<span id="preview_tahun">{{ date('Y') }}</span>
                    </small>
                </div>
            </div>
            <div class="modal-footer py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 14px;">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusWithNomorSurat()" style="font-size: 14px;">Update Status</button>
            </div>
        </div>
    </div>
</div>

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

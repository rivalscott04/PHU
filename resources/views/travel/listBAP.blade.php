@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Pengajuan</h6>
                    <div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                            <a href="{{ route('verify-e-sign') }}" class="btn btn-info me-2">
                                <i class="bx bx-qr-scan me-1"></i>Verifikasi E-Sign
                            </a>
                        @endif
                        <a href="{{ route('form.bap') }}" onclick="return checkJamaah({{ $jamaahCount }});"
                            class="btn btn-primary">
                            Tambah
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>PPIU</th>
                                    <th>Alamat & Hp</th>
                                    <th>Kab/Kota</th>
                                    <th>Jumlah Jamaah</th>
                                    <th>Paket</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td>{{ $item->ppiuname }}</td>
                                        <td>{{ $item->address_phone }}</td>
                                        <td>{{ $item->kab_kota }}</td>
                                        <td>{{ $item->people }}</td>
                                        <td>{{ $item->package }}</td>
                                        <td><span>Rp.
                                            </span>{{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td>
                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                                                <form action="{{ route('bap.updateStatus', $item->id) }}" method="POST" id="statusForm{{ $item->id }}">
                                                    @csrf
                                                    <div class="d-flex flex-column gap-1">
                                                        <select name="status" 
                                                            class="form-select {{ $item->status == 'diajukan' ? 'bg-primary text-white fw-semibold' : '' }}
                                                                {{ $item->status == 'diproses' ? 'bg-warning text-dark fw-semibold' : '' }}
                                                                {{ $item->status == 'diterima' ? 'bg-success text-white fw-semibold' : '' }}"
                                                            onchange="handleStatusChange({{ $item->id }}, this.value)">
                                                            <option value="pending"
                                                                {{ $item->status == 'pending' ? 'selected' : '' }}>Pending
                                                            </option>
                                                            <option value="diajukan"
                                                                {{ $item->status == 'diajukan' ? 'selected' : '' }}>Diajukan
                                                            </option>
                                                            <option value="diproses"
                                                                {{ $item->status == 'diproses' ? 'selected' : '' }}>Diproses
                                                            </option>
                                                            <option value="diterima"
                                                                {{ $item->status == 'diterima' ? 'selected' : '' }}>Diterima
                                                            </option>
                                                        </select>

                                                    </div>
                                                </form>
                                            @else
                                                <div>
                                                    <div>{{ ucfirst($item->status) }}</div>
                                                    @if($item->nomor_surat)
                                                        <small class="text-muted">{{ $item->nomor_surat }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fs-4 font-weight-bold">
                                            @if($item->status === 'diterima')
                                                <a href="{{ route('cetak.bap', $item->id) }}" target="_blank" title="Cetak BAP"><i
                                                        class="bx bx-printer ms-2 text-success"></i></a>
                                            @endif
                                            <a href="{{ route('detail.bap', $item->id) }}" title="Detail"><i
                                                    class="bx bx-info-circle"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentItemId = null;
        
        function checkJamaah(jamaahCount) {
            if (jamaahCount == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Maaf terjadi Kesalahan',
                    text: 'Silahkan mengisi menu jamaah terlebih dahulu.',
                    confirmButtonText: 'Tutup'
                });
                return false;
            }
            return true;
        }

        function handleStatusChange(itemId, status) {
            const form = document.getElementById('statusForm' + itemId);
            
            if (status === 'diproses') {
                // Tampilkan modal untuk input nomor surat
                currentItemId = itemId;
                $('#statusModal').modal('show');
            } else if (status === 'diterima') {
                // Cek apakah sudah ada nomor surat di database (untuk item yang sudah diproses)
                const currentStatus = '{{ $item->status ?? "pending" }}';
                const hasNomorSurat = '{{ $item->nomor_surat ?? "" }}' !== '';
                
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
            const form = document.getElementById('statusForm' + currentItemId);
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
            
            if (nomorInput && bulanSelect && tahunInput) {
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
            }
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

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
                        <table class="table align-items-center mb-0" style="font-size: 12px;">
                            <thead>
                                <tr class="text-center">
                                    <th style="font-size: 12px;">No.</th>
                                    <th style="font-size: 12px;">Nama</th>
                                    <th style="font-size: 12px;">Jabatan</th>
                                    <th style="font-size: 12px;">PPIU</th>
                                    <th style="font-size: 12px;">Alamat & Hp</th>
                                    <th style="font-size: 12px;">Kab/Kota</th>
                                    <th style="font-size: 12px;">Jumlah Jamaah</th>
                                    <th style="font-size: 12px;">Paket</th>
                                    <th style="font-size: 12px;">Harga</th>
                                    <th style="font-size: 12px;">Status</th>
                                    <th style="font-size: 12px;">Aksi</th>

                                </tr>
                            </thead>
                            <tbody style="font-size: 12px;">
                                @foreach ($data as $item)
                                    <tr class="text-center" style="font-size: 12px;">
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
                                                            style="font-size: 11px;"
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
                                                        @if($item->nomor_surat)
                                                            <small class="text-muted" style="font-size: 9px;">{{ $item->nomor_surat }}</small>
                                                        @endif
                                                    </div>
                                                </form>
                                            @else
                                                <div>
                                                    <div style="font-size: 11px;">{{ ucfirst($item->status) }}</div>
                                                    @if($item->nomor_surat)
                                                        <small class="text-muted" style="font-size: 9px;">{{ $item->nomor_surat }}</small>
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
            
            // Submit form langsung untuk semua status
            form.submit();
        }


    </script>
@endpush



@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Pengajuan</h6>
                    <a href="{{ route('form.bap') }}" onclick="return checkJamaah({{ $jamaahCount }});"
                        class="btn btn-primary">
                        Tambah
                    </a>

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
                                                <form action="{{ route('bap.updateStatus', $item->id) }}" method="POST">
                                                    @csrf
                                                    <select name="status"
                                                        class="form-select {{ $item->status == 'diajukan' ? 'bg-primary text-white fw-semibold' : '' }}
                                                            {{ $item->status == 'diproses' ? 'bg-warning text-dark fw-semibold' : '' }}
                                                            {{ $item->status == 'diterima' ? 'bg-success text-white fw-semibold' : '' }}"
                                                        onchange="this.form.submit()">
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
                                                </form>
                                            @else
                                                {{ ucfirst($item->status) }}
                                            @endif
                                        </td>
                                        <td class="fs-4 font-weight-bold">
                                            <a href="{{ route('cetak.bap', $item->id) }}"><i
                                                    class="bx bx-printer ms-2"></i></a>
                                            <a href="{{ route('detail.bap', $item->id) }}"><i
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
                    title: 'Oops...',
                    text: 'Tidak bisa menambahkan pengajuan, karena data Jamaah masih kosong.',
                    confirmButtonText: 'Tutup'
                });
                return false;
            }
            return true;
        }
    </script>
@endpush

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Pengajuan</h6>
                    <a href="{{ route('form.bap') }}" class="btn btn-primary">Tambah</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No.</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jabatan
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">PPIU
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Alamat &
                                        Hp</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kab/Kota
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah
                                        Jamaah</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Paket
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Harga
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold">{{ $item->name }}</td>
                                        <td class="font-weight-bold">{{ $item->jabatan }}</td>
                                        <td class="font-weight-bold">{{ $item->ppiuname }}</td>
                                        <td class="font-weight-bold">{{ $item->address_phone }}</td>
                                        <td class="font-weight-bold">{{ $item->kab_kota }}</td>
                                        <td class="font-weight-bold">{{ $item->people }}</td>
                                        <td class="font-weight-bold">{{ $item->package }}</td>
                                        <td class="font-weight-bold"><span>Rp.
                                            </span>{{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td class="font-weight-bold">
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

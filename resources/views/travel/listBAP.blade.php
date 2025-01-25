@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pengajuan'])

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Data Pengajuan</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            No.
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Jabatan
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            PPIU
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Alamat & Hp
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Kab/Kota
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Jumlah Jamaah
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Paket
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Harga
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->name }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->jabatan }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->ppiuname }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->address_phone }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->kab_kota }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->people }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->package }}</td>
                                            <td class="text-sm font-weight-bold"><span>Rp. </span>
                                                {{ number_format($item->price, 2, ',', '.') }}</td>
                                            <td class="text-lg font-weight-bold">
                                                <a href="{{ route('cetak.bap', $item->id) }}">
                                                    <i class="fa-solid fa-print ms-2"></i>
                                                </a>
                                                <i class="fa-solid fa-circle-info"></i>
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
    </div>

    @include('layouts.footers.auth.footer')
@endsection

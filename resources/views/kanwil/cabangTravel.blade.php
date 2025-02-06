@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Cabang Travel</h6>
                    <a href="{{ route('form.cabang_travel') }}" class="btn btn-primary">Tambah</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 5%">
                                        No.
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Travel
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Kabupaten
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Pusat
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Pimpinan Pusat
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Alamat Pusat
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        No SK / BA
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Pimpinan Cabang
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Alamat Cabang
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Telepon
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Penyelenggara }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->kabupaten }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->pusat }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->pimpinan_pusat }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_pusat }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->SK_BA }}</td>
                                        <td class="text-sm font-weight-bold">{{ date('Y-m-d', strtotime($item->tanggal)) }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->pimpinan_cabang }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_cabang }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->telepon }}</td>
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

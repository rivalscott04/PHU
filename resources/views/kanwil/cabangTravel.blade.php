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
                                        rowspan="2" style="width: 5%">
                                        <div class="vertical-text">No.</div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        No SK / BA
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        colspan="4">
                                        Pusat
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Pimpinan Cabang
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Alamat Kantor Cabang
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Telepon
                                    </th>
                                </tr>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Penyelenggara
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jml Akre
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Lembaga Akred
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->SK_BA }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->travel->Penyelenggara }}</td>
                                        <td class="text-sm font-weight-bold">{{ date('Y-m-d', strtotime($item->Tanggal)) }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->travel->Jml_Akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->travel->lembaga_akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->pimpinan_cabang }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat }}</td>
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

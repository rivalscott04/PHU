@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pengajuan'])

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Data Pengajuan</h6>
                        <a href="{{ route('form.travel') }}" class="btn btn-primary">Tambah</a>
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
                                            Penyelenggara
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            colspan="6">
                                            Nomor SK
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            rowspan="2" style="width: 10%">
                                            Pimpinan
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            rowspan="2" style="width: 15%">
                                            Alamat Kantor Lama
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            rowspan="2" style="width: 15%">
                                            Alamat Kantor Baru
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            rowspan="2" style="width: 10%">
                                            Telepon
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            rowspan="2" style="width: 5%">
                                            Status
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            rowspan="2" style="width: 10%">
                                            Kab/Kota
                                        </th>
                                    </tr>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Pusat
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Tanggal
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Jml Akre
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Tanggal Akredi
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Lembaga Akred
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            -
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->penyelenggara }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->nomor_sk }}</td>
                                            <td class="text-sm font-weight-bold">
                                                {{ date('Y-m-d', strtotime($item->tanggal_sk)) }}</td>
                                            <td class="text-sm font-weight-bold">test</td>
                                            <td class="text-sm font-weight-bold">
                                                {{ date('Y-m-d', strtotime($item->tanggal_akreditasi)) }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->lembaga_akreditasi }}</td>
                                            <td class="text-sm font-weight-bold">-</td>
                                            <td class="text-sm font-weight-bold">{{ $item->pimpinan }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_lama }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_baru }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->telepon }}</td>
                                            <td class="text-sm font-weight-bold">
                                                <form action="{{ route('update.status', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="form-select form-select-sm text-center"
                                                        onchange="this.form.submit()">
                                                        <option value="diajukan"
                                                            {{ $item->status === 'diajukan' ? 'selected' : '' }}>Diajukan
                                                        </option>
                                                        <option value="diproses"
                                                            {{ $item->status === 'diproses' ? 'selected' : '' }}>Diproses
                                                        </option>
                                                        <option value="diterima"
                                                            {{ $item->status === 'diterima' ? 'selected' : '' }}>Diterima
                                                        </option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="text-sm font-weight-bold">{{ $item->kab_kota }}</td>
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

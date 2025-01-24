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
                                            Penyelenggara</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nomor
                                            SK</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Pimpinan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Kab/Kota</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $item->penyelenggara }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->nomor_sk }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->pimpinan }}</td>
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
                                            <td class="text-sm font-weight-bold">
                                                <a href="" class="btn btn-sm btn-success mt-2">Edit</a>
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

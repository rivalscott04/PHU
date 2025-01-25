@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Jamaah'])

    <div class="container-fluid py-4 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Data Jamaah</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Alamat</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            No HP</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jamaah as $item)
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $item->nama }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->alamat }}</td>
                                            <td class="text-sm font-weight-bold">{{ $item->nomor_hp }}</td>
                                            <td class="text-lg font-weight-bold">
                                                <a href="" class="mt-2">
                                                    <i class="fa-solid fa-circle-info me-2"></i>
                                                    <i class="fa-solid fa-pen-to-square text-success"></i>
                                                </a>
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

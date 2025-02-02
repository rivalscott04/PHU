@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Travel</h6>
                    <div>
                        <a href="{{ route('form.travel') }}" class="btn btn-primary me-2">Tambah</a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                    </div>
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
                                        <td class="text-sm font-weight-bold">{{ $item->Penyelenggara }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Pusat }}</td>
                                        <td class="text-sm font-weight-bold">
                                            {{ date('Y-m-d', strtotime($item->tanggal_sk)) }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Jml_Akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">
                                            {{ date('Y-m-d', strtotime($item->tanggal_akreditasi)) }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->lembaga_akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">-</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Pimpinan }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_lama }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_baru }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Telepon }}</td>
                                        <td class="text-sm font-weight-bold text-center">
                                            {{ $item->Status }}
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

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import.data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls"
                                required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('travel.template') }}" class="text-sm">
                                <i class="bx bx-download"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

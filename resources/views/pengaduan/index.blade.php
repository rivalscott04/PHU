@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 d-flex justify-content-between align-items-center">
                    <h6>Data Pengaduan</h6>
                    <div>
                        <a href="{{ route('pengaduan.create') }}" class="btn btn-primary btn-md">
                            <i class="bx bx-plus me-1"></i> Tambah Pengaduan
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Pengadu</th>
                                    <th>Travel</th>
                                    <th>Hal Aduan</th>
                                    <th>Berkas</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengaduan as $key => $item)
                                    <tr class="text-center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->nama_pengadu }}</td>
                                        <td>{{ $item->travel->Penyelenggara }}</td>
                                        <td>{{ Str::limit($item->hal_aduan, 50) }}</td>
                                        <td>
                                            @if ($item->berkas_aduan)
                                                <a href="{{ Storage::url($item->berkas_aduan) }}" target="_blank">
                                                    <i class="bx bx-file"></i>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $item->status == 'pending' ? 'warning' : ($item->status == 'proses' ? 'info' : 'success') }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('pengaduan.show', $item->id) }}">
                                                <i class="bx bx-info-circle me-2"></i>
                                            </a>
                                            @if ($item->status == 'pending')
                                                <a href="">
                                                    <i class="bx bx-edit text-success"></i>
                                                </a>
                                            @endif
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

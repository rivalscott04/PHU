@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 d-flex justify-content-between align-items-center">
                    <h6>Data Pengunduran</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Berkas</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengunduran as $key => $item)
                                    <tr class="text-center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->user->username }}</td>
                                        <td>
                                            @if ($item->berkas_pengunduran)
                                                @if (Str::endsWith($item->berkas_pengunduran, '.pdf'))
                                                    <a href="javascript:void(0)" onclick="openPdfPreview('{{ \App\Helpers\StorageHelper::publicUrl($item->berkas_pengunduran) }}', 'Berkas Pengunduran - {{ $item->user->username }}')">
                                                        <i class="bx bx-file"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ \App\Helpers\StorageHelper::publicUrl($item->berkas_pengunduran) }}" target="_blank">
                                                        <i class="bx bx-file"></i>
                                                    </a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($item->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($item->status == 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($item->status == 'rejected')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array(auth()->user()->role, ['admin', 'kabupaten']) && $item->status === 'pending')
                                                <form action="{{ route('pengunduran.update-status', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-link p-0 text-success" title="Setujui">
                                                        <i class="bx bx-check-circle"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('pengunduran.update-status', $item->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-link p-0 text-danger" title="Tolak">
                                                        <i class="bx bx-x-circle"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
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

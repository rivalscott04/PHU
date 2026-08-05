@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Pengunduran</h4>
                    <p class="text-muted mb-0 small">Kelola pengajuan pengunduran jamaah di wilayah Anda</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Data Pengunduran</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
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
                            @forelse ($pengunduran as $key => $item)
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
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($item->status == 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (in_array(auth()->user()->role, ['admin', 'kabupaten']) && $item->status === 'pending')
                                            <div class="d-flex gap-1 justify-content-center">
                                                <form action="{{ route('pengunduran.update-status', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="bx bx-check-circle"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('pengunduran.update-status', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                        <i class="bx bx-x-circle"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada data pengunduran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

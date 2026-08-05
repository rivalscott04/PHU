@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Pengaduan</h4>
                    <p class="text-muted mb-0 small">Kelola dan pantau pengaduan masyarakat di wilayah Anda</p>
                </div>
                <a href="{{ route('pengaduan.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Pengaduan
                </a>
            </div>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('pengaduan'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pengaduan</p>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Belum Diproses</p>
                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Sedang Diproses</p>
                    <h4 class="mb-0">{{ $stats['in_progress'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Selesai</p>
                    <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Data Pengaduan</h5>
                    <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="in_progress">Sedang Diproses</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No</th>
                                <th>Travel yang diadukan</th>
                                <th>Hal Aduan</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengaduan as $item)
                                <tr class="text-center">
                                    <td>{{ $pengaduan->firstItem() + $loop->index }}</td>
                                    <td>{{ $item->travel->Penyelenggara }}</td>
                                    <td class="text-start" style="max-width: 300px;">{{ $item->hal_aduan }}</td>
                                    <td>
                                        @if ($item->berkas_aduan)
                                            <a href="{{ route('pengaduan.download-berkas', $item->id) }}" target="_blank" rel="noopener noreferrer">
                                                <i class="bx bx-file"></i>
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-dropdown"
                                                data-id="{{ $item->id }}"
                                                data-current-status="{{ $item->status }}"
                                                style="width: auto; min-width: 140px;">
                                            <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="in_progress" {{ $item->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                            <option value="completed" {{ $item->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                                            <a href="{{ route('pengaduan.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-info-circle me-1"></i> Detail
                                            </a>
                                            @if($item->status === 'completed' && $item->pdf_output)
                                                <a href="{{ $item->getPublicDownloadUrl() }}" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="bx bx-download me-1"></i> PDF
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($pengaduan->hasPages())
                    <div class="px-3 py-2 border-top">
                        {{ $pengaduan->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('status-dropdown')) {
        const id = e.target.dataset.id;
        const newStatus = e.target.value;
        const currentStatus = e.target.dataset.currentStatus;

        if (newStatus !== currentStatus) {
            confirmAction({
                title: 'Yakin ingin mengubah status pengaduan ini?',
                icon: 'question',
                confirmText: 'Ya, ubah',
            }).then((result) => {
                if (!result.isConfirmed) {
                    e.target.value = currentStatus;
                    return;
                }

                fetch(`/pengaduan/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        admin_notes: ''
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        e.target.dataset.currentStatus = newStatus;
                        location.reload();
                    } else {
                        Swal.fire({ title: 'Gagal', text: 'Error: ' + data.message, icon: 'error', confirmButtonColor: '#556ee6' });
                        e.target.value = currentStatus;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan saat update status', icon: 'error', confirmButtonColor: '#556ee6' });
                    e.target.value = currentStatus;
                });
            });
        }
    }
});

document.getElementById('statusFilter')?.addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const statusDropdown = row.querySelector('.status-dropdown');
        const status = statusDropdown ? statusDropdown.value : '';

        row.style.display = (!filterValue || status === filterValue) ? '' : 'none';
    });
});
</script>
@endpush

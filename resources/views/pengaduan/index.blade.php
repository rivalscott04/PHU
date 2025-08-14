@extends('layouts.app')

@section('content')
    <!-- Statistik Pengaduan Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; border-left: 4px solid #2563eb;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.8rem;">Total Pengaduan</p>
                                <h2 class="font-weight-bolder mb-0 text-primary" style="font-size: 2.5rem;">
                                    {{ $stats['total'] }}
                                </h2>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Keseluruhan data</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-primary bg-opacity-10 text-center border-radius-md" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bx bx-collection text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; border-left: 4px solid #f59e0b;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.8rem;">Belum Diproses</p>
                                <h2 class="font-weight-bolder mb-0 text-warning" style="font-size: 2.5rem;">
                                    {{ $stats['pending'] }}
                                </h2>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Menunggu tindakan</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-warning bg-opacity-10 text-center border-radius-md" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bx bx-time text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; border-left: 4px solid #06b6d4;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.8rem;">Sedang Diproses</p>
                                <h2 class="font-weight-bolder mb-0 text-info" style="font-size: 2.5rem;">
                                    {{ $stats['in_progress'] }}
                                </h2>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Dalam penanganan</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-info bg-opacity-10 text-center border-radius-md" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bx bx-cog text-info" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.8rem;">Selesai</p>
                                <h2 class="font-weight-bolder mb-0 text-success" style="font-size: 2.5rem;">
                                    {{ $stats['completed'] }}
                                </h2>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Telah diselesaikan</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-success bg-opacity-10 text-center border-radius-md" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bx bx-check-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 d-flex justify-content-between align-items-center">
                    <h6>Data Pengaduan</h6>
                    <div class="d-flex gap-2">
                        <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Semua Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="in_progress">Sedang Diproses</option>
                            <option value="completed">Selesai</option>
                        </select>
                        <a href="{{ route('pengaduan.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i> Tambah Pengaduan
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead class="bg-light">
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
                                @foreach ($pengaduan as $key => $item)
                                    <tr class="text-center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->travel->Penyelenggara }}</td>
                                        <td style="max-width: 300px; word-wrap: break-word; text-align: left;">{{ $item->hal_aduan }}</td>
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
                                            <select class="form-select form-select-sm status-dropdown" 
                                                    data-id="{{ $item->id }}" 
                                                    data-current-status="{{ $item->status }}"
                                                    style="width: auto; min-width: 120px;">
                                                <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                                <option value="in_progress" {{ $item->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                                <option value="completed" {{ $item->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('pengaduan.show', $item->id) }}" class="btn btn-sm btn-primary rounded-pill">
                                                <i class="bx bx-info-circle me-1"></i> Detail
                                            </a>
                                            @if($item->status === 'completed' && $item->pdf_output)
                                                <a href="{{ route('pengaduan.download-pdf.public', $item->id) }}" class="btn btn-sm btn-success rounded-pill" target="_blank">
                                                    <i class="bx bx-download me-1"></i> PDF
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

<script>
// Status dropdown change handler
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('status-dropdown')) {
        const id = e.target.dataset.id;
        const newStatus = e.target.value;
        const currentStatus = e.target.dataset.currentStatus;
        
        if (newStatus !== currentStatus) {
            if (confirm('Yakin ingin mengubah status pengaduan ini?')) {
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
                        alert('Error: ' + data.message);
                        e.target.value = currentStatus;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat update status');
                    e.target.value = currentStatus;
                });
            } else {
                e.target.value = currentStatus;
            }
        }
    }
});

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const statusDropdown = row.querySelector('.status-dropdown');
        const status = statusDropdown ? statusDropdown.value : '';
        
        if (!filterValue || status === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

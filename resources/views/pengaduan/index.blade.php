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
                                            <span class="badge {{ $item->getStatusBadgeClass() }}">
                                                {{ $item->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('pengaduan.show', $item->id) }}" class="btn btn-sm btn-info rounded-pill"
                                               style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); border: none; box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);">
                                                <i class="bx bx-info-circle me-1"></i> Detail
                                            </a>
                                                                                    <button type="button" class="btn btn-sm btn-warning rounded-pill" 
                                                onclick="openStatusModal({{ $item->id }}, '{{ $item->status }}', '{{ $item->admin_notes }}')"
                                                style="background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%); border: none; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);">
                                            <i class="bx bx-edit me-1"></i> Update Status
                                        </button>
                                            @if($item->status === 'completed' && $item->pdf_output)
                                                <a href="{{ route('pengaduan.download-pdf', $item->id) }}" class="btn btn-sm btn-success rounded-pill"
                                                   style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);">
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

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="statusModalLabel">
                        <i class="bx bx-edit me-2"></i>Update Status Pengaduan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                <i class="bx bx-shield me-2"></i>Status
                            </label>
                            <select class="form-select rounded-pill" id="status" name="status" required style="border: 2px solid #e9ecef;">
                                <option value="pending">Menunggu</option>
                                <option value="in_progress">Sedang Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label fw-bold">
                                <i class="bx bx-note me-2"></i>Catatan Admin
                            </label>
                            <textarea class="form-control rounded" id="admin_notes" name="admin_notes" rows="3" 
                                      placeholder="Tambahkan catatan jika diperlukan" style="border: 2px solid #e9ecef;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill" 
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                            <i class="bx bx-check me-1"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
function openStatusModal(id, currentStatus, adminNotes) {
    document.getElementById('statusForm').action = `/pengaduan/${id}/status`;
    document.getElementById('status').value = currentStatus;
    document.getElementById('admin_notes').value = adminNotes || '';
    
    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

// Auto refresh after status update
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat update status');
    });
});
</script>

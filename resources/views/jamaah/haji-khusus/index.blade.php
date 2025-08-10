@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Jamaah Haji Khusus</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('jamaah.haji-khusus.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>
                            Tambah Jamaah
                        </a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bx bx-download me-1"></i>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('jamaah.haji-khusus.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, KTP, paspor..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('jamaah.haji-khusus.index') }}" class="btn btn-secondary">
                            <i class="bx bx-refresh me-1"></i>
                            Reset Filter
                        </a>
                    </div>
                </div>

                <!-- Data Table -->
                @if(auth()->user()->role === 'admin' && $groupedJamaahHajiKhusus)
                    <!-- Admin View: Grouped by Travel -->
                    @foreach($groupedJamaahHajiKhusus as $travelId => $jamaahGroup)
                        @php
                            $travel = $jamaahGroup->first()->travel;
                            $totalJamaah = $jamaahGroup->count();
                        @endphp
                        <div class="mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bx bx-building me-2"></i>
                                    <strong>{{ $travel->Penyelenggara ?? 'Travel Tidak Diketahui' }}</strong>
                                    <span class="badge bg-primary ms-2">{{ $totalJamaah }} Jamaah</span>
                                    <span class="badge bg-info ms-1">{{ $travel->kab_kota ?? 'Kabupaten Tidak Diketahui' }}</span>
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>No. KTP</th>
                                            <th>Usia</th>
                                            <th>No. Paspor</th>
                                            <th>No. SPPH</th>
                                            <th>Status</th>
                                            <th>Bukti Setor</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jamaahGroup as $index => $jamaah)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-0">{{ $jamaah->nama_lengkap }}</h6>
                                                    <small class="text-muted">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $jamaah->no_ktp }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $jamaah->getAge() }} tahun</span>
                                            </td>
                                            <td>
                                                @if($jamaah->no_paspor)
                                                    <code>{{ $jamaah->no_paspor }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($jamaah->nomor_porsi)
                                                    <code>{{ $jamaah->nomor_porsi }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $jamaah->getStatusBadgeClass() }}">
                                                    {{ $jamaah->getStatusText() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($jamaah->bukti_setor_bank)
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge {{ $jamaah->getBuktiSetorStatusBadgeClass() }}">
                                                            {{ $jamaah->getBuktiSetorStatusText() }}
                                                        </span>
                                                        @if(in_array(auth()->user()->role, ['admin', 'kabupaten']))
                                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                    onclick="verifyBuktiSetor({{ $jamaah->id }}, '{{ $jamaah->nama_lengkap }}')"
                                                                    title="Verifikasi Bukti Setor">
                                                                <i class="bx bx-check-circle me-1"></i>
                                                                Verifikasi
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Belum Upload</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('jamaah.haji-khusus.show', $jamaah->id) }}" 
                                                       class="btn btn-info btn-sm" title="Detail">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    <a href="{{ route('jamaah.haji-khusus.edit', $jamaah->id) }}" 
                                                       class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="confirmDelete({{ $jamaah->id }})" title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Regular View: Normal Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. KTP</th>
                                    <th>Usia</th>
                                    <th>No. Paspor</th>
                                    <th>No. SPPH</th>
                                    <th>Status</th>
                                    <th>Bukti Setor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jamaahHajiKhusus as $index => $jamaah)
                                <tr>
                                    <td>{{ $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jamaahHajiKhusus->firstItem() + $index : $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $jamaah->nama_lengkap }}</h6>
                                            <small class="text-muted">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $jamaah->no_ktp }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $jamaah->getAge() }} tahun</span>
                                    </td>
                                    <td>
                                        @if($jamaah->no_paspor)
                                            <code>{{ $jamaah->no_paspor }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($jamaah->nomor_porsi)
                                            <code>{{ $jamaah->nomor_porsi }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $jamaah->getStatusBadgeClass() }}">
                                            {{ $jamaah->getStatusText() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($jamaah->bukti_setor_bank)
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge {{ $jamaah->getBuktiSetorStatusBadgeClass() }}">
                                                    {{ $jamaah->getBuktiSetorStatusText() }}
                                                </span>
                                                @if(in_array(auth()->user()->role, ['admin', 'kabupaten']))
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="verifyBuktiSetor({{ $jamaah->id }}, '{{ $jamaah->nama_lengkap }}')"
                                                            title="Verifikasi Bukti Setor">
                                                        <i class="bx bx-check-circle me-1"></i>
                                                        Verifikasi
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Belum Upload</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('jamaah.haji-khusus.show', $jamaah->id) }}" 
                                               class="btn btn-info btn-sm" title="Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('jamaah.haji-khusus.edit', $jamaah->id) }}" 
                                               class="btn btn-primary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete({{ $jamaah->id }})" title="Hapus">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data jamaah haji khusus</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center mt-3">
                            {{ $jamaahHajiKhusus->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Jamaah Haji Khusus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Export Global</h6>
                        <p class="text-muted small">Export semua data jamaah dari semua PIHK</p>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" onclick="exportGlobal('excel')">
                                <i class="bx bx-file me-1"></i> Export Excel Global
                            </button>
                            <button type="button" class="btn btn-danger" onclick="exportGlobal('pdf')">
                                <i class="bx bx-file-pdf me-1"></i> Export PDF Global
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Export Per PIHK</h6>
                        <p class="text-muted small">Export data jamaah dari PIHK tertentu</p>
                        <select id="travelSelect" class="form-select mb-2">
                            <option value="">Pilih PIHK...</option>
                            @foreach($travelCompanies ?? [] as $travel)
                                @if($travel->Status === 'PIHK')
                                    <option value="{{ $travel->id }}">{{ $travel->Penyelenggara }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" onclick="exportByTravel('excel')">
                                <i class="bx bx-file me-1"></i> Export Excel PIHK
                            </button>
                            <button type="button" class="btn btn-danger" onclick="exportByTravel('pdf')">
                                <i class="bx bx-file-pdf me-1"></i> Export PDF PIHK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function exportGlobal(format) {
    const url = format === 'excel' 
        ? '{{ route("jamaah.haji-khusus.export") }}'
        : '{{ route("jamaah.haji-khusus.export-pdf") }}';
    
    window.open(url, '_blank');
    $('#exportModal').modal('hide');
}

function exportByTravel(format) {
    const travelId = document.getElementById('travelSelect').value;
    if (!travelId) {
        alert('Silakan pilih PIHK terlebih dahulu');
        return;
    }
    
    const url = format === 'excel' 
        ? `{{ route("jamaah.haji-khusus.export") }}?travel_id=${travelId}`
        : `{{ route("jamaah.haji-khusus.export-pdf") }}?travel_id=${travelId}`;
    
    window.open(url, '_blank');
    $('#exportModal').modal('hide');
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus jamaah ini?')) {
        window.location.href = `{{ url('jamaah/haji-khusus') }}/${id}/delete`;
    }
}

function verifyBuktiSetor(id, nama) {
    const status = prompt(`Verifikasi bukti setor untuk ${nama}:\nMasukkan status (verified/rejected):`);
    if (status && ['verified', 'rejected'].includes(status.toLowerCase())) {
        const notes = prompt('Masukkan catatan (opsional):');
        
        fetch(`{{ url('jamaah/haji-khusus') }}/${id}/verify-bukti-setor`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status.toLowerCase(),
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Verifikasi berhasil!');
                location.reload();
            } else {
                alert('Verifikasi gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat verifikasi');
        });
    }
}
</script>
@endpush 
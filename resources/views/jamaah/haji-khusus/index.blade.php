@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Data Jamaah Haji Khusus</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jamaah Haji Khusus</li>
                </ol>
            </div>
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
                        <a href="{{ route('jamaah.haji-khusus.export') }}" class="btn btn-success">
                            <i class="bx bx-download me-1"></i>
                            Export Data
                        </a>
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bx bx-user font-size-24"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-1">{{ $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jamaahHajiKhusus->total() : $jamaahHajiKhusus->count() }}</h4>
                                        <p class="mb-0">Total Jamaah</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bx bx-time font-size-24"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-1">{{ $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jamaahHajiKhusus->where('status_pendaftaran', 'pending')->count() : $jamaahHajiKhusus->where('status_pendaftaran', 'pending')->count() }}</h4>
                                        <p class="mb-0">Menunggu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bx bx-check-circle font-size-24"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-1">{{ $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jamaahHajiKhusus->where('status_pendaftaran', 'approved')->count() : $jamaahHajiKhusus->where('status_pendaftaran', 'approved')->count() }}</h4>
                                        <p class="mb-0">Disetujui</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bx bx-check-double font-size-24"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-1">{{ $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jamaahHajiKhusus->where('status_pendaftaran', 'completed')->count() : $jamaahHajiKhusus->where('status_pendaftaran', 'completed')->count() }}</h4>
                                        <p class="mb-0">Selesai</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                            <th>Dokumen</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jamaahGroup as $index => $jamaah)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-3">
                                                        <span class="avatar-title rounded-circle bg-primary">
                                                            {{ strtoupper(substr($jamaah->nama_lengkap, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $jamaah->nama_lengkap }}</h6>
                                                        <small class="text-muted">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                                    </div>
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
                                                    @if($jamaah->tanggal_berlaku_paspor)
                                                        <br><small class="text-muted">Berlaku: {{ $jamaah->tanggal_berlaku_paspor->format('d/m/Y') }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($jamaah->nomor_porsi)
                                                    <code>{{ $jamaah->nomor_porsi }}</code>
                                                    @if($jamaah->tahun_pendaftaran)
                                                        <br><small class="text-muted">Tahun: {{ $jamaah->tahun_pendaftaran }}</small>
                                                    @endif
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
                                                <div class="d-flex flex-column gap-1">
                                                    @php
                                                        $completion = $jamaah->getDocumentCompletionPercentage();
                                                    @endphp
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar {{ $completion == 100 ? 'bg-success' : ($completion >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                             style="width: {{ $completion }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $completion }}% lengkap</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('jamaah.haji-khusus.show', $jamaah->id) }}" 
                                                       class="btn btn-info btn-sm" title="Detail">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    <a href="{{ route('jamaah.haji-khusus.edit', $jamaah) }}" 
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
                                    <th>Dokumen</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jamaahHajiKhusus as $index => $jamaah)
                                <tr>
                                    <td>{{ $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $jamaahHajiKhusus->firstItem() + $index : $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    {{ strtoupper(substr($jamaah->nama_lengkap, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $jamaah->nama_lengkap }}</h6>
                                                <small class="text-muted">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                            </div>
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
                                            @if($jamaah->tanggal_berlaku_paspor)
                                                <br><small class="text-muted">Berlaku: {{ $jamaah->tanggal_berlaku_paspor->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($jamaah->nomor_porsi)
                                            <code>{{ $jamaah->nomor_porsi }}</code>
                                            @if($jamaah->tahun_pendaftaran)
                                                <br><small class="text-muted">Tahun: {{ $jamaah->tahun_pendaftaran }}</small>
                                            @endif
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
                                        <div class="d-flex flex-column gap-1">
                                            @php
                                                $completion = $jamaah->getDocumentCompletionPercentage();
                                            @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $completion == 100 ? 'bg-success' : ($completion >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                     style="width: {{ $completion }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $completion }}% lengkap</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('jamaah.haji-khusus.show', $jamaah->id) }}" 
                                               class="btn btn-info btn-sm" title="Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('jamaah.haji-khusus.edit', $jamaah) }}" 
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
                                    <td colspan="9" class="text-center">
                                        <div class="text-muted">
                                            <i class="bx bx-user font-size-24 mb-2"></i>
                                            <p>Belum ada data jamaah haji khusus</p>
                                            <a href="{{ route('jamaah.haji-khusus.create') }}" class="btn btn-primary">
                                                <i class="bx bx-plus me-1"></i>
                                                Tambah Jamaah Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Pagination -->
                @if(auth()->user()->role !== 'admin' && $jamaahHajiKhusus instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="d-flex justify-content-center">
                        {{ $jamaahHajiKhusus->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Verifikasi Bukti Setor Modal -->
<div class="modal fade" id="verifyBuktiSetorModal" tabindex="-1" aria-labelledby="verifyBuktiSetorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyBuktiSetorModalLabel">Verifikasi Bukti Setor Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyBuktiSetorForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jamaah</label>
                        <input type="text" class="form-control" id="jamaahName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="status_verifikasi_bukti" class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_verifikasi_bukti" name="status_verifikasi_bukti" required>
                            <option value="">Pilih Status</option>
                            <option value="verified">Terverifikasi</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_verifikasi" class="form-label">Catatan Verifikasi</label>
                        <textarea class="form-control" id="catatan_verifikasi" name="catatan_verifikasi" rows="3" 
                                  placeholder="Masukkan catatan verifikasi (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check-circle me-1"></i>
                        Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Porsi Modal -->
<div class="modal fade" id="assignPorsiModal" tabindex="-1" aria-labelledby="assignPorsiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                                    <h5 class="modal-title" id="assignPorsiModalLabel">Tetapkan Nomor SPPH</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignPorsiForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jamaah</label>
                        <input type="text" class="form-control" id="jamaahNamePorsi" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_porsi" class="form-label">Nomor SPPH <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nomor_porsi" name="nomor_porsi" required>
                    </div>
                    <div class="mb-3">
                        <label for="tahun_pendaftaran" class="form-label">Tahun Pendaftaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tahun_pendaftaran" name="tahun_pendaftaran" maxlength="4" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>
                        Tetapkan Porsi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data jamaah haji khusus akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/jamaah/haji-khusus/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Verify Bukti Setor
function verifyBuktiSetor(jamaahId, jamaahName) {
    document.getElementById('jamaahName').value = jamaahName;
    document.getElementById('verifyBuktiSetorForm').action = `/jamaah/haji-khusus/${jamaahId}/verify-bukti-setor`;
    $('#verifyBuktiSetorModal').modal('show');
}

// Assign Porsi
function assignPorsi(jamaahId, jamaahName) {
    document.getElementById('jamaahNamePorsi').value = jamaahName;
    document.getElementById('assignPorsiForm').action = `/jamaah/haji-khusus/${jamaahId}/assign-porsi`;
    $('#assignPorsiModal').modal('show');
}

// Handle verify bukti setor form submission
document.getElementById('verifyBuktiSetorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat verifikasi bukti setor',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

// Handle assign porsi form submission
document.getElementById('assignPorsiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
                            text: 'Terjadi kesalahan saat menetapkan nomor SPPH',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});
</script>
@endpush 
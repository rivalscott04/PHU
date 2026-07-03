@extends('layouts.app')

@section('content')
@php
    $statusLabels = [
        'SUBMITTED' => 'Diajukan',
        'PENDING' => 'Menunggu',
        'REVISION_REQUIRED' => 'Perlu Revisi',
        'VERIFIED' => 'Terverifikasi',
        'REJECTED' => 'Ditolak',
        'CLOSED' => 'Selesai',
    ];
    $statusBadges = [
        'SUBMITTED' => 'info',
        'PENDING' => 'warning',
        'REVISION_REQUIRED' => 'danger',
        'VERIFIED' => 'success',
        'REJECTED' => 'dark',
        'CLOSED' => 'secondary',
    ];
    $canVerify = in_array(auth()->user()->role, ['admin', 'pengawas'], true);
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-semibold">Tindak Lanjut Temuan</h4>
                <p class="text-muted mb-0 small">Bagian dari alur <strong>BA Pemeriksaan</strong>, respons travel atas temuan inspeksi pengawasan.
                    @if($canVerify)
                        Kelola dan verifikasi bukti tindak lanjut hasil pengawasan
                    @else
                        Riwayat bukti tindak lanjut perusahaan Anda
                    @endif
                </p>
            </div>
            <a href="{{ route('v2.monitoring.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-radar me-1"></i> Monitoring
            </a>
        </div>
    </div>

    @include('v2.partials.wilayah-scope')

    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_followup'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    @include('v2.partials.kpi-cards', ['cards' => $cards, 'id' => 'followup-kpi'])

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold">Daftar Tindak Lanjut</h5>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm" style="min-width:180px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Travel</th>
                            <th>Kabupaten</th>
                            <th>Temuan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($followups as $followup)
                            @php
                                $status = $followup->status?->value ?? $followup->status;
                            @endphp
                            <tr>
                                <td class="ps-3 fw-medium">{{ $followup->finding?->inspection?->travel?->Penyelenggara }}</td>
                                <td class="text-muted">{{ $followup->finding?->inspection?->travel?->kab_kota }}</td>
                                <td>{{ $followup->finding?->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusBadges[$status] ?? 'secondary' }}">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ optional($followup->submitted_at)->format('d M Y, H:i') ?? 'Tidak ada' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('v2.followup.show', $followup) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bx bx-task d-block mb-2" style="font-size:2rem;"></i>
                                    Belum ada tindak lanjut{{ auth()->user()->role === 'kabupaten' ? ' di wilayah Anda' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($followups->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $followups->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

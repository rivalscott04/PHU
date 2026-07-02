@extends('layouts.app')

@section('content')
@php
    $inspection = $followup->finding?->inspection;
    $travel = $inspection?->travel;
    $status = $followup->status?->value ?? $followup->status;
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
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('v2.followup.index') }}">Tindak Lanjut</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-semibold">{{ $followup->finding?->title }}</h4>
                    <p class="text-muted mb-0">
                        {{ $travel?->Penyelenggara }}
                        <span class="mx-1">·</span>
                        {{ $travel?->kab_kota }}
                    </p>
                </div>
                <a href="{{ route('v2.followup.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-semibold">Informasi Pengawasan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">No. Pengawasan</p>
                            <p class="mb-0 fw-medium">{{ $inspection?->inspection_no ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Tanggal Pengawasan</p>
                            <p class="mb-0 fw-medium">{{ optional($inspection?->inspection_date)->format('d M Y') ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Travel</p>
                            <p class="mb-0 fw-medium">{{ $travel?->Penyelenggara ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Kabupaten</p>
                            <p class="mb-0 fw-medium">{{ $travel?->kab_kota ?? '—' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Rekomendasi</p>
                            <p class="mb-0 text-body-secondary">{{ $followup->finding?->recommendation ?? '—' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Deskripsi Tindak Lanjut</p>
                            <p class="mb-0">{{ $followup->description }}</p>
                        </div>
                        @if ($followup->attachment)
                            <div class="col-12">
                                <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Lampiran</p>
                                <a href="{{ route('v2.followup.attachment', $followup) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-download me-1"></i> Unduh Bukti
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">Status</p>
                    <span class="badge bg-{{ $statusBadges[$status] ?? 'secondary' }} fs-6 px-3 py-2 mb-3">
                        {{ $statusLabels[$status] ?? $status }}
                    </span>
                    @if($followup->submitted_at)
                        <p class="text-muted small mb-0">Diajukan {{ $followup->submitted_at->format('d M Y, H:i') }}</p>
                    @endif
                    @if($followup->verified_at)
                        <p class="text-muted small mb-0 mt-1">Diverifikasi {{ $followup->verified_at->format('d M Y, H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @can('approve', $followup)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="mb-0 fw-semibold">Verifikasi Pengawas</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('v2.followup.approve', $followup) }}" class="mb-3">
                @csrf
                <label class="form-label">Catatan persetujuan (opsional)</label>
                <input type="text" name="remarks" class="form-control mb-2" placeholder="Contoh: Bukti sudah sesuai rekomendasi">
                <button class="btn btn-success btn-sm">
                    <i class="bx bx-check me-1"></i> Setujui
                </button>
            </form>
            <hr>
            <form method="POST" action="{{ route('v2.followup.revision', $followup) }}">
                @csrf
                <label class="form-label">Alasan revisi <span class="text-danger">*</span></label>
                <textarea name="remarks" class="form-control mb-2" placeholder="Jelaskan bagian yang perlu diperbaiki (min. 10 karakter)" required minlength="10"></textarea>
                <button class="btn btn-warning btn-sm">
                    <i class="bx bx-revision me-1"></i> Minta Revisi
                </button>
            </form>
        </div>
    </div>
    @endcan

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="mb-0 fw-semibold">Timeline</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                @forelse ($followup->logs as $log)
                    <li class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="avatar-xs rounded-circle bg-light d-flex align-items-center justify-content-center">
                                <i class="bx bx-time-five text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <strong>{{ $statusLabels[$log->status] ?? $log->status }}</strong>
                            <p class="mb-1 text-body-secondary">{{ $log->description }}</p>
                            <small class="text-muted">{{ optional($log->created_at)->format('d M Y, H:i') }}</small>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-muted py-4">Belum ada histori.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

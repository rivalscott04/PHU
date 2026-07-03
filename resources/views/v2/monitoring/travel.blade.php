@extends('layouts.app')

@section('content')
@php
    $riskBadges = [
        'LOW' => 'success',
        'MEDIUM' => 'info',
        'HIGH' => 'warning',
        'CRITICAL' => 'danger',
    ];
    $riskLabels = [
        'LOW' => 'Rendah',
        'MEDIUM' => 'Sedang',
        'HIGH' => 'Tinggi',
        'CRITICAL' => 'Kritis',
    ];
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('v2.monitoring.index') }}">Monitoring</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Travel</li>
                    </ol>
                </nav>
                <h4 class="mb-1 fw-semibold">Data Travel</h4>
                <p class="text-muted mb-0">Daftar lengkap travel beserta aktivitas pengawasan dan risiko</p>
            </div>
            <a href="{{ route('v2.monitoring.index') }}" class="btn btn-sm btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Semua Travel</h5>
            <span class="badge bg-light text-dark border">{{ $travels->total() }} travel</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Travel</th>
                            <th>Kabupaten</th>
                            <th>Jenis</th>
                            <th>Pengawasan</th>
                            <th>Pengaduan</th>
                            <th class="pe-3">Risiko</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($travels as $travel)
                            @php
                                $risk = $travel->riskScore?->risk_level?->value ?? $travel->riskScore?->risk_level;
                            @endphp
                            <tr>
                                <td class="ps-3 fw-medium">{{ $travel->Penyelenggara }}</td>
                                <td class="text-muted">{{ $travel->kab_kota }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $travel->Status }}</span></td>
                                <td>{{ number_format($travel->inspections_count) }}</td>
                                <td>{{ number_format($travel->pengaduan_count) }}</td>
                                <td class="pe-3">
                                    @if($risk)
                                        <span class="badge bg-{{ $riskBadges[$risk] ?? 'secondary' }}">
                                            {{ $riskLabels[$risk] ?? $risk }}
                                        </span>
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada data travel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($travels->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $travels->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@php
    $risk = $statistics['risk_score'] ?? null;
    $riskLevel = $risk?->risk_level?->value ?? $risk?->risk_level ?? null;
    $riskBadge = match ($riskLevel) {
        'CRITICAL' => 'danger',
        'HIGH' => 'warning',
        'MEDIUM' => 'info',
        'LOW' => 'success',
        default => 'secondary',
    };
    $riskLabel = match ($riskLevel) {
        'CRITICAL' => 'Kritis',
        'HIGH' => 'Tinggi',
        'MEDIUM' => 'Sedang',
        'LOW' => 'Rendah',
        default => 'Belum dihitung',
    };

    $kpiCards = [
        ['key' => 'total_pengawasan', 'label' => 'Pengawasan', 'icon' => 'bx-search-alt', 'color' => '#556ee6'],
        ['key' => 'temuan_aktif', 'label' => 'Temuan Aktif', 'icon' => 'bx-error-circle', 'color' => '#f46a6a'],
        ['key' => 'total_pengaduan', 'label' => 'Pengaduan', 'icon' => 'bx-message-square-dots', 'color' => '#f1b44c'],
        ['key' => 'total_jamaah', 'label' => 'Jamaah', 'icon' => 'bx-group', 'color' => '#34c38f'],
        ['key' => 'total_bap', 'label' => 'BAP', 'icon' => 'bx-file', 'color' => '#50a5f1'],
        ['key' => 'total_sertifikat', 'label' => 'Sertifikat', 'icon' => 'bx-award', 'color' => '#74788d'],
    ];

    $licenseExpired = $travel->isLicenseExpired();
    $licenseStatus = $travel->getLicenseStatus();
    $licenseBadge = match ($licenseStatus) {
        'Active' => 'success',
        'Expired' => 'danger',
        default => 'secondary',
    };
    $licenseLabel = match ($licenseStatus) {
        'Active' => 'Aktif',
        'Expired' => 'Kedaluwarsa',
        default => 'Tidak tersedia',
    };
@endphp

<div class="container-fluid">
    {{-- Breadcrumb & header --}}
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('v2.compliance.index') }}">Profil Kepatuhan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $travel->Penyelenggara }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-semibold">{{ $travel->Penyelenggara }}</h4>
                    <p class="text-muted mb-0">
                        {{ $statistics['kabupaten'] ?? $travel->kab_kota }}
                        <span class="mx-1">·</span>
                        {{ $statistics['travel_type'] ?? $travel->Status }}
                    </p>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    @if($risk && \App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.risk.show'))
                        <a href="{{ route('v2.risk.show', $travel) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-shield-quarter me-1"></i> Detail Risiko
                        </a>
                    @endif
                    <a href="{{ route('v2.compliance.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk score + travel info --}}
    <div class="row mb-3">
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.05em;">Skor Risiko</p>
                    <h1 class="display-4 fw-bold mb-2">{{ number_format($risk?->total_score ?? 0, 0) }}</h1>
                    <span class="badge bg-{{ $riskBadge }} fs-6 px-3 py-2">{{ $riskLabel }}</span>
                    @if($risk?->last_calculated_at)
                        <p class="text-muted small mt-3 mb-0">
                            Terakhir dihitung {{ $risk->last_calculated_at->format('d M Y, H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-semibold">Informasi Travel</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Pimpinan</p>
                            <p class="mb-0 fw-medium">{{ $travel->Pimpinan ?: 'Tidak ada' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Telepon</p>
                            <p class="mb-0 fw-medium">{{ $travel->Telepon ?: 'Tidak ada' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Jenis Layanan</p>
                            <p class="mb-0">
                                @forelse ($travel->getAvailableServices() as $service)
                                    <span class="badge bg-light text-dark border me-1">{{ $service }}</span>
                                @empty
                                    <span class="text-muted">Tidak ada</span>
                                @endforelse
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Status Izin</p>
                            <p class="mb-0">
                                <span class="badge bg-{{ $licenseBadge }}">{{ $licenseLabel }}</span>
                                @if($travel->license_expiry)
                                    <small class="text-muted ms-1">berlaku s/d {{ $travel->license_expiry->format('d M Y') }}</small>
                                @endif
                            </p>
                        </div>
                        @if($travel->alamat_kantor_baru || $travel->alamat_kantor_lama)
                            <div class="col-12">
                                <p class="text-muted text-uppercase mb-1" style="font-size:0.7rem; letter-spacing:0.04em;">Alamat Kantor</p>
                                <p class="mb-0 text-body-secondary">{{ $travel->alamat_kantor_baru ?: $travel->alamat_kantor_lama }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="row mb-3">
        @foreach ($kpiCards as $card)
            @php $value = $statistics[$card['key']] ?? 0; @endphp
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 3px solid {{ $card['color'] }} !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size:0.65rem; letter-spacing:0.04em;">{{ $card['label'] }}</p>
                                <h4 class="mb-0 fw-semibold">{{ number_format($value) }}</h4>
                            </div>
                            <div class="avatar-xs rounded-circle d-flex align-items-center justify-content-center" style="background: {{ $card['color'] }}15;">
                                <i class="bx {{ $card['icon'] }}" style="color: {{ $card['color'] }}; font-size:1.1rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        {{-- Inspection history --}}
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">Riwayat Pengawasan</h5>
                    <span class="badge bg-light text-dark border">{{ $inspection_history->total() }} entri</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Pengawasan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inspection_history as $item)
                                    @php
                                        $statusEnum = $item->status instanceof \App\Enums\InspectionStatus
                                            ? $item->status
                                            : \App\Enums\InspectionStatus::tryFrom((string) ($item->status?->value ?? $item->status));
                                        $statusText = $statusEnum?->label() ?? ($item->status?->value ?? $item->status);
                                        $statusColor = $statusEnum?->badgeColor() ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $item->inspection_no }}</td>
                                        <td class="text-muted">{{ optional($item->inspection_date)->format('d M Y') ?? 'Tidak ada' }}</td>
                                        <td><span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span></td>
                                        <td class="text-end pe-3">
                                            @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.pengawasan.show', ['pengawasan' => $item]))
                                                <a href="{{ route('v2.pengawasan.show', $item) }}" class="btn btn-sm btn-link text-primary p-0">
                                                    Lihat detail <i class="bx bx-chevron-right"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bx bx-folder-open d-block mb-2" style="font-size:2rem;"></i>
                                            Belum ada riwayat pengawasan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($inspection_history->hasPages())
                        <div class="px-3 py-2 border-top">
                            {{ $inspection_history->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recommendations --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-semibold">Rekomendasi</h5>
                </div>
                <div class="card-body">
                    @if(count($recommendations) > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach ($recommendations as $item)
                                <li class="d-flex gap-2 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <span class="flex-shrink-0 mt-1">
                                        <i class="bx bx-info-circle text-warning" style="font-size:1.25rem;"></i>
                                    </span>
                                    <span class="text-body-secondary" style="line-height:1.6;">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-check-circle text-success d-block mb-2" style="font-size:2.5rem;"></i>
                            <p class="text-muted mb-0">Tidak ada rekomendasi saat ini.<br>Profil kepatuhan dalam kondisi baik.</p>
                        </div>
                    @endif

                    @if($licenseExpired)
                        <div class="alert alert-danger mb-0 mt-3 py-2 px-3" role="alert">
                            <small><i class="bx bx-error me-1"></i> Izin operasional telah kedaluwarsa.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@php
    use App\Support\RouteAccess;
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
                <h4 class="mb-1 fw-semibold">Monitoring</h4>
                <p class="text-muted mb-0">Ringkasan operasional travel, pengawasan, dan risiko</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
                @if(RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.travel'))
                    <a href="{{ route('v2.monitoring.travel') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-list-ul me-1"></i> Data Travel
                    </a>
                @endif
                @if(RouteAccess::canAccessRoute(auth()->user(), 'v2.dashboard'))
                    <a href="{{ route('v2.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-line-chart me-1"></i> Dashboard
                    </a>
                @endif
                @if(RouteAccess::canAccessRoute(auth()->user(), 'v2.export.monitoring'))
                    <a href="{{ route('v2.export.monitoring', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-success">
                        <i class="bx bx-spreadsheet me-1"></i> Excel
                    </a>
                    <a href="{{ route('v2.export.monitoring', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-download me-1"></i> CSV
                    </a>
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-refresh-kpi">
                    <i class="bx bx-refresh me-1"></i> Refresh KPI
                </button>
            </div>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_monitoring'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    @include('v2.partials.kpi-cards', ['cards' => $cards, 'id' => 'monitoring-kpi'])

    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-semibold">Data Travel Terbaru</h5>
                        <small class="text-muted">Snapshot monitoring per penyelenggara</small>
                    </div>
                    @if(RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.travel'))
                        <a href="{{ route('v2.monitoring.travel') }}" class="btn btn-sm btn-link text-primary p-0">
                            Lihat semua <i class="bx bx-chevron-right"></i>
                        </a>
                    @endif
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
                                        <td>@include('v2.partials.pengaduan-count', ['travel' => $travel])</td>
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
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bx bx-buildings d-block mb-2" style="font-size:2rem;"></i>
                                            Belum ada data travel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            @include('v2.partials.quick-access')

            @php
                $alertCount = ($cards['temuan_aktif']['value'] ?? 0) + ($cards['travel_risiko_tinggi']['value'] ?? 0);
            @endphp
            @if($alertCount > 0)
                <div class="alert alert-warning border-0 shadow-sm mt-3 mb-0" role="alert">
                    <div class="d-flex gap-2">
                        <i class="bx bx-error-circle fs-4"></i>
                        <div>
                            <strong>Perlu perhatian</strong>
                            <p class="mb-0 small">
                                Terdapat {{ number_format($cards['temuan_aktif']['value'] ?? 0) }} temuan aktif
                                dan {{ number_format($cards['travel_risiko_tinggi']['value'] ?? 0) }} travel berisiko tinggi.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('v2.partials.pengaduan-offcanvas')
@endsection

@push('js')
<script>
document.getElementById('btn-refresh-kpi')?.addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;

    fetch('{{ route('v2.monitoring.statistics') }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) return;
        Object.entries(res.data).forEach(([key, value]) => {
            const el = document.querySelector(`[data-kpi="${key}"]`);
            if (el) el.textContent = new Intl.NumberFormat('id-ID').format(value);
        });
    })
    .finally(() => {
        btn.disabled = false;
    });
});
</script>
@endpush

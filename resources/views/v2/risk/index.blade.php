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
    $isReadOnly = auth()->user()->role !== 'admin';
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-semibold">Risk Score</h4>
                <p class="text-muted mb-0">
                    @if(auth()->user()->role === 'kabupaten')
                        Prioritas risiko travel di wilayah {{ auth()->user()->getKabupaten() }} (baca saja)
                    @elseif(auth()->user()->role === 'user')
                        Skor risiko perusahaan Anda
                    @else
                        Prioritas pengawasan berbasis risiko seluruh NTB
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('v2.risk.recalculate') }}">
                        @csrf
                        <button class="btn btn-sm btn-warning">
                            <i class="bx bx-refresh me-1"></i> Hitung Ulang
                        </button>
                    </form>
                @endif
                <a href="{{ route('v2.monitoring.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-radar me-1"></i> Monitoring
                </a>
            </div>
        </div>
    </div>

    @include('v2.partials.wilayah-scope')

    @include('v2.partials.kpi-cards', ['cards' => $cards, 'id' => 'risk-kpi'])

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold">Ranking Risk Score</h5>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="risk_level" class="form-select form-select-sm" style="min-width:160px;" onchange="this.form.submit()">
                    <option value="">Semua Level</option>
                    @foreach ($riskLabels as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['risk_level'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Travel</th>
                            <th>Kabupaten</th>
                            <th>Skor</th>
                            <th>Level</th>
                            <th>Terakhir Dihitung</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($risks as $index => $risk)
                            @php
                                $level = $risk->risk_level?->value ?? $risk->risk_level;
                            @endphp
                            <tr>
                                <td class="ps-3 text-muted">{{ $risks->firstItem() + $index }}</td>
                                <td class="fw-medium">{{ $risk->travel?->Penyelenggara }}</td>
                                <td class="text-muted">{{ $risk->travel?->kab_kota }}</td>
                                <td><strong>{{ number_format($risk->total_score, 0) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $riskBadges[$level] ?? 'secondary' }}">
                                        {{ $riskLabels[$level] ?? $level }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ optional($risk->last_calculated_at)->format('d M Y, H:i') ?? '—' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('v2.risk.show', $risk->travel_id) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bx bx-shield-quarter d-block mb-2" style="font-size:2rem;"></i>
                                    Belum ada data risk score{{ auth()->user()->role === 'kabupaten' ? ' di wilayah Anda' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($risks->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $risks->links() }}
                </div>
            @endif
        </div>
    </div>

    @if($isReadOnly)
        <p class="text-muted small mt-2 mb-0">
            <i class="bx bx-info-circle me-1"></i>
            Risk score dihitung otomatis oleh sistem dan tidak dapat diubah manual.
        </p>
    @endif
</div>
@endsection

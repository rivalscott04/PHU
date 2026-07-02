@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">Risk: {{ $travel->Penyelenggara }}</h4>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('v2.risk.recalculate.travel', $travel) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">Recalculate</button>
                    </form>
                @endif
                <a href="{{ route('v2.risk.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    @php
                        $level = $breakdown['risk_level'] ?? ($risk->risk_level?->value ?? 'LOW');
                        $badgeClass = match($level) {
                            'CRITICAL' => 'danger',
                            'HIGH' => 'warning',
                            'MEDIUM' => 'info',
                            default => 'success',
                        };
                    @endphp
                    <p class="text-muted mb-1">Total Risk Score</p>
                    <h1 class="display-4 mb-2">{{ number_format($breakdown['total_score'] ?? $risk?->total_score ?? 0, 0) }}</h1>
                    <span class="badge bg-{{ $badgeClass }} fs-6">{{ $level }}</span>
                    @if($risk?->last_calculated_at)
                        <p class="text-muted small mt-3 mb-0">Terakhir dihitung: {{ $risk->last_calculated_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Rekomendasi</h5></div>
                <div class="card-body">
                    <p class="mb-3">{{ $breakdown['recommendation'] ?? '-' }}</p>
                    <ul class="mb-0">
                        @foreach ($breakdown['indicators'] ?? [] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Breakdown Indikator</h5></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Indikator</th>
                        <th>Bobot Maks</th>
                        <th>Skor</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $weights = [
                            'complaint_score' => ['label' => 'Pengaduan', 'max' => 30],
                            'inspection_score' => ['label' => 'Temuan Pengawasan', 'max' => 25],
                            'followup_score' => ['label' => 'Tindak Lanjut', 'max' => 15],
                            'bap_score' => ['label' => 'BAP', 'max' => 10],
                            'certificate_score' => ['label' => 'Sertifikat / Izin', 'max' => 10],
                            'activity_score' => ['label' => 'Aktivitas Travel', 'max' => 10],
                        ];
                        $scores = $breakdown['scores'] ?? [];
                    @endphp
                    @foreach ($weights as $key => $meta)
                        @php
                            $val = $scores[$key] ?? ($risk?->$key ?? 0);
                            $pct = $meta['max'] > 0 ? min(100, ($val / $meta['max']) * 100) : 0;
                        @endphp
                        <tr>
                            <td>{{ $meta['label'] }}</td>
                            <td>{{ $meta['max'] }}</td>
                            <td><strong>{{ $val }}</strong></td>
                            <td style="min-width:160px;">
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar bg-{{ $pct >= 75 ? 'danger' : ($pct >= 50 ? 'warning' : 'success') }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Distribusi Skor</h5></div>
        <div class="card-body">
            <div id="risk-breakdown-chart" style="min-height:280px;"></div>
        </div>
    </div>
</div>
@endsection

@push('js')
@php
    $chartScores = array_values($breakdown['scores'] ?? []);
    $chartLabels = ['Pengaduan', 'Temuan', 'Follow Up', 'BAP', 'Sertifikat', 'Aktivitas'];
@endphp
<script>
(function () {
    const scores = @json($chartScores);
    const labels = @json($chartLabels);
    const el = document.querySelector('#risk-breakdown-chart');
    if (!el || typeof ApexCharts === 'undefined') return;
    new ApexCharts(el, {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        series: [{ name: 'Skor', data: scores }],
        xaxis: { categories: labels },
        colors: ['#556ee6'],
    }).render();
})();
</script>
@endpush

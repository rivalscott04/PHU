@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">Dashboard Pengawasan V2</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('v2.export.dashboard', request()->query()) }}" class="btn btn-sm btn-outline-danger">
                    <i class="bx bxs-file-pdf me-1"></i> Unduh PDF
                </a>
                <button type="button" class="btn btn-sm btn-primary" id="btn-apply-filter">
                    <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_dashboard'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form id="dashboard-filter-form" class="row g-2">
                @if(auth()->user()->role === 'admin')
                <div class="col-md-3">
                    <label class="form-label">Kabupaten</label>
                    <select name="kabupaten" class="form-select form-select-sm">
                        <option value="">Semua Kabupaten</option>
                        @foreach ($filters['kabupaten_options'] ?? [] as $kab)
                            <option value="{{ $kab }}" @selected(request('kabupaten') === $kab)>{{ $kab }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" @selected(($filters['tahun'] ?? now()->year) == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(($filters['bulan'] ?? now()->month) == $m)>{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jenis Travel</label>
                    <select name="jenis_travel" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="PPIU" @selected(request('jenis_travel') === 'PPIU')>PPIU</option>
                        <option value="PIHK" @selected(request('jenis_travel') === 'PIHK')>PIHK</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Risk Level</label>
                    <select name="risk_level" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach (['LOW','MEDIUM','HIGH','CRITICAL'] as $level)
                            <option value="{{ $level }}" @selected(request('risk_level') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @include('v2.dashboard.partials.warning')
    @include('v2.dashboard.partials.cards')
    @include('v2.dashboard.partials.heatmap')
    @include('v2.dashboard.partials.chart')
    @include('v2.dashboard.partials.ranking')
    @include('v2.dashboard.partials.timeline')
</div>
@endsection

@push('js')
<script>
    window.__dashboardHeatmapData = @json($heatmap ?? []);
</script>
<script>
(function () {
    const chartData = @json($charts ?? []);
    let chartInstances = {};

    function renderLineChart(elId, labels, series, name) {
        const el = document.querySelector(elId);
        if (!el || typeof ApexCharts === 'undefined') return;
        if (chartInstances[elId]) chartInstances[elId].destroy();
        chartInstances[elId] = new ApexCharts(el, {
            chart: { type: 'line', height: 280, toolbar: { show: false } },
            series: [{ name, data: series }],
            xaxis: { categories: labels },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#556ee6'],
        });
        chartInstances[elId].render();
    }

    function renderBarChart(elId, labels, series, horizontal) {
        const el = document.querySelector(elId);
        if (!el || typeof ApexCharts === 'undefined') return;
        if (chartInstances[elId]) chartInstances[elId].destroy();
        chartInstances[elId] = new ApexCharts(el, {
            chart: { type: 'bar', height: horizontal ? 300 : 280, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: !!horizontal, borderRadius: 4 } },
            series: [{ name: 'Jumlah', data: series }],
            xaxis: { categories: labels },
            colors: ['#34c38f'],
        });
        chartInstances[elId].render();
    }

    function renderPieChart(elId, labels, series) {
        const el = document.querySelector(elId);
        if (!el || typeof ApexCharts === 'undefined') return;
        if (chartInstances[elId]) chartInstances[elId].destroy();
        chartInstances[elId] = new ApexCharts(el, {
            chart: { type: 'pie', height: 280 },
            labels,
            series,
            legend: { position: 'bottom' },
        });
        chartInstances[elId].render();
    }

    function initCharts(data) {
        renderLineChart('#chart-jamaah-monthly', data.jamaah_monthly?.labels || [], data.jamaah_monthly?.series || [], 'Jamaah');
        renderBarChart('#chart-keberangkatan-monthly', data.keberangkatan_monthly?.labels || [], data.keberangkatan_monthly?.series || [], false);
        renderPieChart('#chart-pengaduan-category', data.pengaduan_category?.labels || [], data.pengaduan_category?.series || []);
        renderPieChart('#chart-risk-distribution', Object.keys(data.risk_distribution || {}), Object.values(data.risk_distribution || {}));
        renderBarChart('#chart-temuan-severity', data.temuan_severity?.labels || [], data.temuan_severity?.series || [], false);
        renderBarChart('#chart-pengawasan-kabupaten', data.pengawasan_kabupaten?.labels || [], data.pengawasan_kabupaten?.series || [], true);
    }

    function queryString() {
        const form = document.getElementById('dashboard-filter-form');
        return new URLSearchParams(new FormData(form)).toString();
    }

    function fetchJson(url) {
        return fetch(url + (url.includes('?') ? '&' : '?') + queryString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json());
    }

    document.getElementById('btn-apply-filter')?.addEventListener('click', function () {
        Promise.all([
            fetchJson('{{ route('v2.dashboard.statistics') }}'),
            fetchJson('{{ route('v2.dashboard.charts') }}'),
            fetchJson('{{ route('v2.dashboard.warning') }}'),
            fetchJson('{{ route('v2.dashboard.heatmap') }}'),
        ]).then(([statsRes, chartsRes, warnRes, heatmapRes]) => {
            if (statsRes.success) {
                Object.entries(statsRes.data).forEach(([key, card]) => {
                    const el = document.querySelector(`[data-kpi="${key}"]`);
                    if (el) el.textContent = new Intl.NumberFormat('id-ID').format(card.value);
                });
            }
            if (chartsRes.success) initCharts(chartsRes.data);
            if (warnRes.success) {
                const box = document.getElementById('warning-list');
                if (!box) return;
                if (!warnRes.data.length) {
                    box.innerHTML = '<p class="text-muted mb-0">Tidak ada peringatan saat ini.</p>';
                    return;
                }
                box.innerHTML = warnRes.data.map(w => `<div class="alert alert-${w.level === 'critical' ? 'danger' : (w.level === 'warning' ? 'warning' : 'info')} mb-2 py-2"><span class="me-2">${w.icon}</span>${w.message}</div>`).join('');
            }
            if (heatmapRes.success && window.DashboardHeatmap) {
                window.DashboardHeatmap.render(heatmapRes.data);
            }
        });
    });

    document.getElementById('btn-refresh-timeline')?.addEventListener('click', function () {
        fetchJson('{{ route('v2.dashboard.timeline') }}').then(res => {
            if (!res.success) return;
            const list = document.getElementById('timeline-list');
            if (!list) return;
            list.innerHTML = res.data.length
                ? res.data.map(e => `<li class="border-bottom pb-3 mb-3"><h6 class="mb-1">${e.title}</h6><p class="text-muted mb-1">${e.description}</p><small class="text-muted">${e.relative || ''}</small></li>`).join('')
                : '<li class="text-muted text-center">Belum ada aktivitas.</li>';
        });
    });

    initCharts(chartData);
})();
</script>
@endpush

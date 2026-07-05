@extends('layouts.app')

@php
    use App\Enums\UserRole;
    use App\Support\RouteAccess;

    $isPimpinan = auth()->user()->role === UserRole::Pimpinan->value;
@endphp

@section('content')
<div class="container-fluid {{ $isPimpinan ? 'dashboard-pimpinan' : '' }}">
    <div class="{{ $isPimpinan ? 'dashboard-pimpinan-sticky' : '' }}">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">Dashboard Pengawasan</h4>
                    @if($isPimpinan)
                        <p class="text-muted mb-0 small">Ringkasan eksekutif seluruh NTB</p>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    @if(RouteAccess::canAccessRoute(auth()->user(), 'v2.export.dashboard'))
                        <a href="{{ route('v2.export.dashboard', request()->query()) }}" class="btn btn-sm btn-outline-danger">
                            <i class="bx bxs-file-pdf me-1"></i> Unduh PDF
                        </a>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary" id="btn-apply-filter">
                        <i class="bx bx-filter-alt me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        @if($guide = \App\Support\RoleWorkflowGuide::for('v2_dashboard'))
            @include('partials.workflow-guide', [
                'guide' => $guide,
                'expanded' => ! $isPimpinan,
            ])
        @endif

        <div class="card mb-3 {{ $isPimpinan ? 'border-0 shadow-sm' : '' }}">
            <div class="card-body">
                <form id="dashboard-filter-form" class="row g-2">
                    @if(auth()->user()->role === UserRole::Admin->value)
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
                        <label class="form-label">Tingkat Risiko</label>
                        <select name="risk_level" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach (\App\Enums\RiskLevel::cases() as $level)
                                <option value="{{ $level->value }}" @selected(request('risk_level') === $level->value)>{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($isPimpinan)
        @include('v2.dashboard.partials.pimpinan-tabs')
    @else
        @include('v2.dashboard.partials.warning')
        @include('v2.dashboard.partials.cards')
        @include('v2.dashboard.partials.heatmap')
        @include('v2.dashboard.partials.chart')
        @include('v2.dashboard.partials.ranking')
        @include('v2.dashboard.partials.timeline')
    @endif
</div>

@include('v2.partials.pengaduan-offcanvas')
@endsection

@if($isPimpinan)
    @push('styles')
        <style>
            .dashboard-pimpinan-sticky {
                position: sticky;
                top: 70px;
                z-index: 1020;
                background: var(--bs-body-bg, #f8f8fb);
                padding-bottom: 0.25rem;
            }
            .dashboard-pimpinan #pane-ringkasan .card,
            .dashboard-pimpinan #pane-visualisasi .card,
            .dashboard-pimpinan #pane-kinerja .card,
            .dashboard-pimpinan #pane-detail .card {
                border: 0;
                box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
            }
        </style>
    @endpush
@endif

@push('js')
<script>
(function () {
    const chartData = @json($charts ?? []);
    const isPimpinan = @json($isPimpinan);
    const urgencyLabels = { critical: 'Segera', high: 'Prioritas', medium: 'Perlu Perhatian' };
    const urgencyBadges = { critical: 'danger', high: 'warning', medium: 'warning' };
    let chartInstances = {};
    let cachedChartData = chartData;

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
        if (!document.getElementById('chart-jamaah-monthly')) return;

        renderLineChart('#chart-jamaah-monthly', data.jamaah_monthly?.labels || [], data.jamaah_monthly?.series || [], 'Jamaah');
        renderBarChart('#chart-keberangkatan-monthly', data.keberangkatan_monthly?.labels || [], data.keberangkatan_monthly?.series || [], false);
        renderPieChart('#chart-pengaduan-category', data.pengaduan_category?.labels || [], data.pengaduan_category?.series || []);
        renderPieChart('#chart-risk-distribution', Object.keys(data.risk_distribution || {}), Object.values(data.risk_distribution || {}));
        renderBarChart('#chart-temuan-severity', data.temuan_severity?.labels || [], data.temuan_severity?.series || [], false);
        renderBarChart('#chart-pengawasan-kabupaten', data.pengawasan_kabupaten?.labels || [], data.pengawasan_kabupaten?.series || [], true);
    }

    function isVisualisasiTabActive() {
        return document.getElementById('pane-visualisasi')?.classList.contains('active');
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

    const pointToneClasses = {
        danger: 'text-danger',
        warning: 'text-warning',
        success: 'text-success',
        info: 'text-primary',
        default: 'text-muted',
    };

    function renderExecutive(data) {
        const periodEl = document.getElementById('executive-summary-period');
        const listEl = document.getElementById('executive-summary-list');
        const summary = data.summary || {};

        if (periodEl && summary.period) {
            periodEl.textContent = summary.period;
        }

        if (listEl) {
            const points = summary.points || [];
            listEl.innerHTML = points.length
                ? points.map(point => {
                    const tone = pointToneClasses[point.tone] || pointToneClasses.default;
                    return `<li class="d-flex align-items-start gap-2 mb-2 executive-summary-point">
                        <span class="mt-2 flex-shrink-0 ${tone}" style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
                        <span><span class="fw-semibold text-body">${point.label}:</span> <span class="${tone}">${point.text}</span></span>
                    </li>`;
                }).join('')
                : '<li class="text-muted">Tidak ada ringkasan untuk filter ini.</li>';
        }

        Object.entries(data.completion_rates || {}).forEach(([key, rate]) => {
            const valueEl = document.querySelector(`.completion-rate-value[data-rate="${key}"]`);
            const detailEl = document.querySelector(`.completion-rate-detail[data-rate="${key}"]`);
            if (valueEl) valueEl.textContent = `${Number(rate.percent || 0).toFixed(1)}%`;
            if (detailEl) {
                if ((rate.total || 0) > 0) {
                    detailEl.textContent = `${Number(rate.selesai || 0).toLocaleString('id-ID')} dari ${Number(rate.total).toLocaleString('id-ID')}`;
                } else {
                    detailEl.textContent = 'Belum ada data';
                }
            }
        });

        const prioritiesBody = document.getElementById('intervention-priorities-body');
        if (prioritiesBody) {
            const rows = data.intervention_priorities || [];
            prioritiesBody.innerHTML = rows.length
                ? rows.map(row => `<tr>
                    <td class="ps-3"><span class="badge bg-${urgencyBadges[row.urgency] || 'warning'}">${urgencyLabels[row.urgency] || 'Perlu Perhatian'}</span></td>
                    <td class="fw-medium">${row.travel || '-'}</td>
                    <td>${row.kabupaten || '-'}</td>
                    <td>${row.issue || '-'}</td>
                </tr>`).join('')
                : '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada penyelenggara yang memerlukan intervensi saat ini.</td></tr>';
        }

        const scorecardBody = document.getElementById('kabupaten-scorecard-body');
        if (scorecardBody) {
            const rows = data.kabupaten_scorecard || [];
            scorecardBody.innerHTML = rows.length
                ? rows.map(row => `<tr>
                    <td class="ps-3 fw-medium">${row.kabupaten || '-'}</td>
                    <td>${row.total_travel || 0}</td>
                    <td>${row.pengawasan || 0}</td>
                    <td>${(row.temuan_aktif || 0) > 0 ? `<span class="badge bg-warning text-dark">${row.temuan_aktif}</span>` : (row.temuan_aktif || 0)}</td>
                    <td>${row.pengaduan || 0}</td>
                    <td>${row.avg_risk || 0}</td>
                    <td>${row.bap_pending || 0}</td>
                </tr>`).join('')
                : '<tr><td colspan="7" class="text-center text-muted py-4">Belum ada data rekap wilayah untuk filter ini.</td></tr>';
        }

        const gapsBody = document.getElementById('coverage-gaps-body');
        if (gapsBody) {
            const rows = data.coverage_gaps || [];
            gapsBody.innerHTML = rows.length
                ? rows.map(row => `<tr>
                    <td class="ps-3 fw-medium">${row.travel || '-'}</td>
                    <td>${row.kabupaten || '-'}</td>
                    <td>${row.last_inspection || 'Belum pernah'}</td>
                    <td>${row.last_inspection
                        ? `<span class="text-warning">${row.months_ago ?? '-'} bulan lalu</span>`
                        : '<span class="badge bg-danger">Belum pernah diawasi</span>'}</td>
                </tr>`).join('')
                : '<tr><td colspan="4" class="text-center text-muted py-4">Semua travel telah diawasi dalam 12 bulan terakhir.</td></tr>';
        }
    }

    function applyDashboardFilters() {
        const requests = [
            fetchJson('{{ route('v2.dashboard.statistics') }}'),
            fetchJson('{{ route('v2.dashboard.charts') }}'),
            fetchJson('{{ route('v2.dashboard.warning') }}'),
            fetchJson('{{ route('v2.dashboard.heatmap') }}'),
        ];

        if (isPimpinan) {
            requests.push(fetchJson('{{ route('v2.dashboard.executive') }}'));
        }

        return Promise.all(requests).then((results) => {
            const [statsRes, chartsRes, warnRes, heatmapRes, executiveRes] = results;
            if (statsRes.success) {
                Object.entries(statsRes.data).forEach(([key, card]) => {
                    const el = document.querySelector(`[data-kpi="${key}"]`);
                    if (el) el.textContent = new Intl.NumberFormat('id-ID').format(card.value);
                });
            }
            if (chartsRes.success) {
                cachedChartData = chartsRes.data;
                if (!isPimpinan || isVisualisasiTabActive()) {
                    initCharts(chartsRes.data);
                }
            }
            if (warnRes.success) {
                const box = document.getElementById('warning-list');
                if (box) {
                    if (!warnRes.data.length) {
                        box.innerHTML = '<p class="text-muted mb-0">Tidak ada peringatan saat ini.</p>';
                    } else {
                        box.innerHTML = warnRes.data.map(w => `<div class="alert alert-${w.level === 'critical' ? 'danger' : (w.level === 'warning' ? 'warning' : 'info')} mb-2 py-2"><span class="me-2">${w.icon}</span>${w.message}</div>`).join('');
                    }
                }
            }
            if (heatmapRes.success && window.DashboardHeatmap) {
                window.DashboardHeatmap.render(heatmapRes.data);
            }
            if (executiveRes?.success) {
                renderExecutive(executiveRes.data);
            }
        });
    }

    document.getElementById('btn-apply-filter')?.addEventListener('click', applyDashboardFilters);
    document.getElementById('dashboard-filter-form')?.addEventListener('change', applyDashboardFilters);

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

    document.getElementById('tab-visualisasi')?.addEventListener('shown.bs.tab', function () {
        setTimeout(function () {
            window.DashboardHeatmap?.resetView?.();
            initCharts(cachedChartData);
        }, 150);
    });

    if (!isPimpinan) {
        initCharts(chartData);
    }
})();
</script>
@endpush

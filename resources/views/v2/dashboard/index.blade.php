@extends('layouts.app')

@php
    use App\Enums\UserRole;
    use App\Support\RouteAccess;

    $isPimpinan = auth()->user()->role === UserRole::Pimpinan->value;
    $canKabupatenPengaduanDrilldown = RouteAccess::canAccessRoute(
        auth()->user(),
        'v2.monitoring.kabupaten.pengaduan',
        ['kabupaten' => 'Lombok Barat'],
    );
@endphp

@section('content')
<div class="container-fluid {{ $isPimpinan ? 'dashboard-pimpinan' : '' }}">
    <div class="{{ $isPimpinan ? 'dashboard-pimpinan-sticky' : '' }}">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-0">Dashboard Eksekutif</h4>
                    @if($isPimpinan)
                        <p class="text-muted mb-0 small">
                            Ringkasan pengawasan
                            @if(request('kabupaten'))
                                {{ request('kabupaten') }}
                            @else
                                seluruh NTB
                            @endif
                        </p>
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
                    @if(in_array(auth()->user()->role, [UserRole::Admin->value, UserRole::Pimpinan->value], true))
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
        <div id="pimpinan-command-traffic">
            @include('partials.home-traffic-light', [
                'status' => $trafficLight ?? [],
                'queues' => $actionQueues ?? [],
            ])
        </div>

        <div id="pimpinan-command-queues">
            @include('partials.home-queue-cards', [
                'queues' => $actionQueues ?? [],
                'status' => $trafficLight ?? [],
                'title' => 'Indikator Prioritas NTB',
                'subtitle' => 'Klik kartu untuk membuka monitoring atau data terkait',
            ])
        </div>

        <div id="pimpinan-command-alerts">
            @include('partials.home-travel-alerts', ['alerts' => $alerts ?? []])
        </div>
    @endif

    @if($isPimpinan)
        @include('v2.dashboard.partials.pimpinan-tabs')
    @else
        @include('v2.dashboard.partials.warning')
        @include('v2.dashboard.partials.cards', ['kpiLayout' => $kpiLayout ?? []])
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
            .intervention-priority-row,
            .kabupaten-filter-row {
                cursor: pointer;
            }
            .intervention-priority-row:hover,
            .kabupaten-filter-row:hover {
                background-color: rgba(85, 110, 230, 0.04);
            }
        </style>
    @endpush
@endif

@push('js')
<script>
(function () {
    const chartData = @json($charts ?? []);
    const isPimpinan = @json($isPimpinan);
    const canKabupatenPengaduanDrilldown = @json($canKabupatenPengaduanDrilldown);
    const canMonitoringTravel = @json(RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.index'));
    const travelPengaduanUrl = @json(url('/v2/monitoring/travel'));
    const urgencyLabels = { critical: 'Segera', high: 'Prioritas', medium: 'Perlu Perhatian' };
    const urgencyBadges = {
        critical: 'light text-danger border border-danger',
        high: 'light text-body border',
        medium: 'light text-muted border',
    };

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        })[char]);
    }

    function renderKabupatenPengaduanCell(kabupaten, count) {
        const total = Number(count || 0);
        if (!canKabupatenPengaduanDrilldown || total <= 0 || !kabupaten) {
            return String(total);
        }

        return `<button type="button" class="btn btn-link btn-sm p-0 text-primary text-decoration-none pengaduan-kabupaten-drilldown fw-semibold" data-kabupaten="${escapeHtml(kabupaten)}" title="Lihat daftar pengaduan">${total.toLocaleString('id-ID')}</button>`;
    }

    function warningIconClass(level) {
        return level === 'critical' ? 'bx-error-circle text-danger' : 'bx-info-circle text-muted';
    }

    function completionRateStatus(percent) {
        if (percent >= 75) {
            return { cardClass: '', badgeClass: '', badgeLabel: '' };
        }
        if (percent >= 50) {
            return {
                cardClass: '',
                badgeClass: 'badge bg-light text-muted border',
                badgeLabel: 'Perlu ditingkatkan',
            };
        }
        return {
            cardClass: 'border-start border-danger border-2',
            badgeClass: 'badge bg-light text-danger border border-danger',
            badgeLabel: 'Rendah',
        };
    }

    function executivePointHtml(point) {
        const tone = point.tone || 'default';
        const dotClass = tone === 'danger' ? 'bg-danger' : 'bg-secondary opacity-50';
        const badge = tone === 'danger'
            ? '<span class="badge bg-light text-danger border border-danger ms-1">Segera</span>'
            : '';

        return `<li class="d-flex align-items-start gap-2 mb-2 executive-summary-point">
            <span class="mt-2 flex-shrink-0 rounded-circle ${dotClass}" style="width:6px;height:6px;"></span>
            <span><span class="fw-semibold text-body">${escapeHtml(point.label)}:</span> <span class="text-muted">${escapeHtml(point.text)}</span>${badge}</span>
        </li>`;
    }

    function updateCompletionRateCard(key, rate) {
        const percent = Number(rate.percent || 0);
        const status = completionRateStatus(percent);
        const cardEl = document.querySelector(`.completion-rate-card[data-rate="${key}"]`);
        const valueEl = document.querySelector(`.completion-rate-value[data-rate="${key}"]`);
        const detailEl = document.querySelector(`.completion-rate-detail[data-rate="${key}"]`);
        const badgeEl = document.querySelector(`.completion-rate-badge[data-rate="${key}"]`);

        if (cardEl) {
            cardEl.className = `card border-0 shadow-sm h-100 completion-rate-card ${status.cardClass}`.trim();
            cardEl.dataset.rate = key;
        }
        if (valueEl) {
            valueEl.textContent = `${percent.toFixed(1)}%`;
        }
        if (detailEl) {
            detailEl.textContent = (rate.total || 0) > 0
                ? `${Number(rate.selesai || 0).toLocaleString('id-ID')} dari ${Number(rate.total).toLocaleString('id-ID')}`
                : 'Belum ada data';
        }
        if (badgeEl) {
            if (status.badgeLabel) {
                badgeEl.className = `completion-rate-badge ${status.badgeClass}`;
                badgeEl.textContent = status.badgeLabel;
                badgeEl.classList.remove('d-none');
            } else {
                badgeEl.className = 'completion-rate-badge d-none';
                badgeEl.textContent = '';
            }
        }
    }

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
            chart: { type: 'bar', height: horizontal ? Math.max(300, labels.length * 36) : 280, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: !!horizontal, borderRadius: 4 } },
            series: [{ name: 'Jumlah', data: series }],
            xaxis: { categories: labels },
            colors: ['#34c38f'],
        });
        chartInstances[elId].render();
    }

    function renderPengawasanKabupatenChart(labels, series) {
        const elId = '#chart-pengawasan-kabupaten';
        const el = document.querySelector('#chart-pengawasan-kabupaten');
        if (!el || typeof ApexCharts === 'undefined') return;
        if (chartInstances[elId]) chartInstances[elId].destroy();

        const values = series.map(v => Number(v) || 0);
        const maxValue = Math.max(1, ...values);

        chartInstances[elId] = new ApexCharts(el, {
            chart: {
                type: 'bar',
                height: Math.max(360, labels.length * 42),
                toolbar: { show: false },
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    barHeight: '65%',
                    distributed: true,
                    dataLabels: { position: 'center' },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => String(val),
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                    colors: ['#495057'],
                },
            },
            series: [{ name: 'Pengawasan', data: values }],
            xaxis: {
                categories: labels,
                min: 0,
                max: maxValue,
                tickAmount: maxValue,
                labels: { formatter: (val) => Number.isInteger(Number(val)) ? String(Number(val)) : val },
            },
            yaxis: {
                labels: {
                    minWidth: 120,
                    style: { fontSize: '12px' },
                },
            },
            colors: values.map(val => (val > 0 ? '#34c38f' : '#e2e8f0')),
            legend: { show: false },
            grid: { padding: { left: 8, right: 16 } },
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
        renderPengawasanKabupatenChart(
            data.pengawasan_kabupaten?.labels || [],
            data.pengawasan_kabupaten?.series || []
        );
    }

    function isVisualisasiTabActive() {
        return document.getElementById('pane-visualisasi')?.classList.contains('active');
    }

    function queryString() {
        const form = document.getElementById('dashboard-filter-form');
        return new URLSearchParams(new FormData(form)).toString();
    }

    function applyKabupatenFilter(kabupaten) {
        const select = document.querySelector('#dashboard-filter-form select[name="kabupaten"]');
        if (!select) {
            return;
        }

        select.value = kabupaten || '';
        applyDashboardFilters();
        document.getElementById('tab-ringkasan')?.click();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.addEventListener('click', function (event) {
        const row = event.target.closest('[data-filter-kabupaten]');
        if (!row || event.target.closest('a, button')) {
            return;
        }

        applyKabupatenFilter(row.dataset.filterKabupaten || '');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        const row = event.target.closest('[data-filter-kabupaten]');
        if (!row) {
            return;
        }

        event.preventDefault();
        applyKabupatenFilter(row.dataset.filterKabupaten || '');
    });

    function fetchJson(url) {
        return fetch(url + (url.includes('?') ? '&' : '?') + queryString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json());
    }

    function renderCommandCenter(commandCenter) {
        if (!commandCenter) {
            return;
        }

        const status = commandCenter.trafficLight || {};
        const queues = commandCenter.actionQueues || [];
        const alerts = commandCenter.alerts || [];
        const level = status.level || 'ok';
        const lightClass = {
            critical: 'bg-danger',
            warning: 'border border-secondary bg-transparent',
            ok: 'bg-secondary opacity-25',
        }[level] || 'bg-secondary opacity-25';
        const statusBadgeClass = status.badge_class || 'bg-secondary';

        const trafficEl = document.getElementById('pimpinan-command-traffic');
        if (trafficEl) {
            const badges = queues
                .filter(queue => Number(queue.count || 0) > 0)
                .map(queue => `<span class="badge bg-light text-dark border">${escapeHtml(queue.label)}: ${Number(queue.count).toLocaleString('id-ID')}</span>`)
                .join('');

            trafficEl.innerHTML = `<div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle ${lightClass}" style="width: 14px; height: 14px;"></span>
                            <h5 class="mb-0">${escapeHtml(status.label || 'Kondisi Umum')}</h5>
                        </div>
                        <span class="badge ${statusBadgeClass}">${escapeHtml(String(level).toUpperCase())}</span>
                        <p class="text-muted mb-0 small flex-grow-1">${escapeHtml(status.message || '')}</p>
                    </div>
                    ${badges ? `<div class="d-flex flex-wrap gap-2 mt-3">${badges}</div>` : ''}
                </div>
            </div>`;
        }

        const queuesEl = document.getElementById('pimpinan-command-queues');
        if (queuesEl) {
            const cards = queues.map(queue => {
                const count = Number(queue.count || 0);
                const url = queue.url || '#';
                const cardInner = `<div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase">${escapeHtml(queue.label)}</p>
                                <h3 class="mb-1 fw-semibold text-body">${count.toLocaleString('id-ID')}</h3>
                                <small class="text-muted">${escapeHtml(queue.hint || '')}</small>
                            </div>
                            <div class="avatar-sm rounded-circle bg-light text-muted d-flex align-items-center justify-content-center">
                                <i class="bx ${queue.icon} fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>`;

                return `<div class="col-xl-3 col-md-6 mb-3">${queue.url
                    ? `<a href="${escapeHtml(url)}" class="text-decoration-none text-body">${cardInner}</a>`
                    : cardInner}</div>`;
            }).join('');

            queuesEl.innerHTML = `<div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Indikator Prioritas NTB</h5>
                        <p class="text-muted mb-0 small">Klik kartu untuk membuka monitoring atau data terkait</p>
                    </div>
                    <span class="badge ${statusBadgeClass} fs-6">${escapeHtml(status.label || 'Status')}</span>
                </div>
            </div>
            ${status.message ? `<div class="alert alert-light border mb-4 text-body">${escapeHtml(status.message)}</div>` : ''}
            <div class="row mb-4">${cards}</div>`;
        }

        const alertsEl = document.getElementById('pimpinan-command-alerts');
        if (alertsEl) {
            alertsEl.innerHTML = alerts.length
                ? `<div class="row mb-4"><div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body">
                    <h5 class="mb-3">Perlu Perhatian</h5>
                    <div class="list-group list-group-flush">${alerts.map(alert => `<a href="${escapeHtml(alert.url)}" class="list-group-item list-group-item-action px-0 d-flex align-items-start gap-3">
                        <i class="bx ${alert.icon} fs-5 mt-1 text-muted"></i>
                        <div class="flex-grow-1"><div class="fw-medium">${escapeHtml(alert.label)}</div><small class="text-muted">${escapeHtml(alert.hint)}</small></div>
                        <i class="bx bx-chevron-right text-muted"></i>
                    </a>`).join('')}</div>
                </div></div></div></div>`
                : '';
        }
    }

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
                ? points.map(point => executivePointHtml(point)).join('')
                : '<li class="text-muted">Tidak ada ringkasan untuk filter ini.</li>';
        }

        Object.entries(data.completion_rates || {}).forEach(([key, rate]) => {
            updateCompletionRateCard(key, rate);
        });

        const prioritiesBody = document.getElementById('intervention-priorities-body');
        if (prioritiesBody) {
            const rows = data.intervention_priorities || [];
            prioritiesBody.innerHTML = rows.length
                ? rows.map(row => {
                    const kabupaten = escapeHtml(row.kabupaten || '');
                    const travelId = row.travel_id ? Number(row.travel_id) : null;
                    const detailBtn = travelId && canMonitoringTravel
                        ? `<a href="${travelPengaduanUrl}/${travelId}/pengaduan" class="btn btn-sm btn-light" onclick="event.stopPropagation()">Detail</a>`
                        : '<span class="btn btn-sm btn-light disabled">Filter</span>';

                    return `<tr class="intervention-priority-row" role="button" tabindex="0" data-filter-kabupaten="${kabupaten}" title="Klik untuk filter kabupaten ${kabupaten}">
                    <td class="ps-3"><span class="badge bg-${urgencyBadges[row.urgency] || 'light text-muted border'}">${urgencyLabels[row.urgency] || 'Perlu Perhatian'}</span></td>
                    <td class="fw-medium">${escapeHtml(row.travel || '-')}</td>
                    <td>${kabupaten || '-'}</td>
                    <td>${escapeHtml(row.issue || '-')}</td>
                    <td class="text-end pe-3">${detailBtn}</td>
                </tr>`;
                }).join('')
                : '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada penyelenggara yang memerlukan intervensi saat ini.</td></tr>';
        }

        const scorecardBody = document.getElementById('kabupaten-scorecard-body');
        if (scorecardBody) {
            const rows = data.kabupaten_scorecard || [];
            scorecardBody.innerHTML = rows.length
                ? rows.map(row => {
                    const kabupaten = escapeHtml(row.kabupaten || '');
                    return `<tr class="kabupaten-filter-row" role="button" tabindex="0" data-filter-kabupaten="${kabupaten}" title="Klik untuk filter ${kabupaten}">
                    <td class="ps-3 fw-medium">${kabupaten || '-'}</td>
                    <td>${row.total_travel || 0}</td>
                    <td>${row.pengawasan || 0}</td>
                    <td>${(row.temuan_aktif || 0) > 0 ? `<span class="badge bg-light text-body border">${row.temuan_aktif}</span>` : (row.temuan_aktif || 0)}</td>
                    <td>${renderKabupatenPengaduanCell(row.kabupaten, row.pengaduan)}</td>
                    <td>${row.avg_risk || 0}</td>
                    <td>${row.bap_pending || 0}</td>
                </tr>`;
                }).join('')
                : '<tr><td colspan="7" class="text-center text-muted py-4">Belum ada data rekap wilayah untuk filter ini.</td></tr>';
        }

        const gapsBody = document.getElementById('coverage-gaps-body');
        if (gapsBody) {
            const rows = data.coverage_gaps || [];
            gapsBody.innerHTML = rows.length
                ? rows.map(row => `<tr>
                    <td class="ps-3 fw-medium">${escapeHtml(row.travel || '-')}</td>
                    <td>${escapeHtml(row.kabupaten || '-')}</td>
                    <td>${escapeHtml(row.last_inspection || 'Belum pernah')}</td>
                    <td>${row.last_inspection
                        ? `<span class="text-muted">${row.months_ago ?? '-'} bulan lalu</span>`
                        : '<span class="badge bg-light text-danger border border-danger">Belum pernah diawasi</span>'}</td>
                </tr>`).join('')
                : '<tr><td colspan="4" class="text-center text-muted py-4">Semua travel telah diawasi dalam 12 bulan terakhir.</td></tr>';
        }

        renderCommandCenter(data.command_center);
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
                const formatter = new Intl.NumberFormat('id-ID');
                Object.entries(statsRes.data).forEach(([key, card]) => {
                    document.querySelectorAll(`[data-kpi="${key}"]`).forEach(el => {
                        el.textContent = formatter.format(card.value);
                    });
                });
                document.querySelectorAll('[data-kpi-composite]').forEach(el => {
                    const parts = (el.dataset.kpiParts || '').split(',').filter(Boolean);
                    const total = parts.reduce((sum, key) => sum + (Number(statsRes.data[key]?.value) || 0), 0);
                    el.textContent = formatter.format(total);
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
                        box.innerHTML = warnRes.data.map(w => {
                            const iconCls = warningIconClass(w.level);
                            return `<div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom">
                                <i class="bx ${iconCls} mt-1"></i>
                                <span class="text-body mb-0">${escapeHtml(w.message)}</span>
                            </div>`;
                        }).join('');
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
            fetchJson('{{ route('v2.dashboard.charts') }}').then(function (res) {
                if (!res.success) return;
                cachedChartData = res.data;
                initCharts(res.data);
            });
        }, 150);
    });

    if (!isPimpinan) {
        initCharts(chartData);
    }
})();
</script>
@endpush

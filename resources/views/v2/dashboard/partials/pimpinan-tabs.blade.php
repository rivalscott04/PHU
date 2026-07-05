<div class="card border-0 shadow-sm mb-3">
    <div class="card-body pb-0">
        <ul class="nav nav-tabs nav-tabs-custom" id="pimpinanDashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link active"
                    id="tab-ringkasan"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-ringkasan"
                    type="button"
                    role="tab"
                    aria-controls="pane-ringkasan"
                    aria-selected="true"
                >
                    <i class="bx bx-grid-alt me-1"></i> Ringkasan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="tab-visualisasi"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-visualisasi"
                    type="button"
                    role="tab"
                    aria-controls="pane-visualisasi"
                    aria-selected="false"
                >
                    <i class="bx bx-line-chart me-1"></i> Visualisasi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="tab-kinerja"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-kinerja"
                    type="button"
                    role="tab"
                    aria-controls="pane-kinerja"
                    aria-selected="false"
                >
                    <i class="bx bx-bar-chart-square me-1"></i> Kinerja
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="tab-detail"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-detail"
                    type="button"
                    role="tab"
                    aria-controls="pane-detail"
                    aria-selected="false"
                >
                    <i class="bx bx-list-ul me-1"></i> Detail
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body pt-3">
        <div class="tab-content" id="pimpinanDashboardTabContent">
            <div class="tab-pane fade show active" id="pane-ringkasan" role="tabpanel" aria-labelledby="tab-ringkasan">
                @include('v2.dashboard.partials.executive-summary', ['executive' => $executive ?? []])
                @include('v2.dashboard.partials.completion-rates', ['executive' => $executive ?? []])
                @include('v2.dashboard.partials.warning')
                @include('v2.dashboard.partials.intervention-priorities', ['executive' => $executive ?? []])
                @include('v2.dashboard.partials.cards', ['colClass' => 'col-xl-2 col-md-3 col-sm-6'])
            </div>
            <div class="tab-pane fade" id="pane-visualisasi" role="tabpanel" aria-labelledby="tab-visualisasi">
                @include('v2.dashboard.partials.heatmap', ['compact' => true])
                @include('v2.dashboard.partials.chart')
            </div>
            <div class="tab-pane fade" id="pane-kinerja" role="tabpanel" aria-labelledby="tab-kinerja">
                @include('v2.dashboard.partials.kabupaten-scorecard', ['executive' => $executive ?? []])
                @include('v2.dashboard.partials.coverage-gaps', ['executive' => $executive ?? []])
            </div>
            <div class="tab-pane fade" id="pane-detail" role="tabpanel" aria-labelledby="tab-detail">
                @include('v2.dashboard.partials.ranking')
                @include('v2.dashboard.partials.timeline')
            </div>
        </div>
    </div>
</div>

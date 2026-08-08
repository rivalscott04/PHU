@if (! empty($kpiLayout['sections'] ?? []))
    @include('v2.dashboard.partials.kpi-cards-grouped', ['kpiLayout' => $kpiLayout])
@else
    <div class="row" id="v2-dashboard-cards">
        @include('v2.partials.kpi-cards', ['cards' => $stats, 'colClass' => $colClass ?? null])
    </div>
@endif

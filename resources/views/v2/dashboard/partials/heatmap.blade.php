<div class="row" id="v2-dashboard-heatmap">
    <div class="col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-semibold">Heatmap Kabupaten/Kota NTB</h5>
                    <small class="text-muted">Intensitas berdasarkan jumlah pengawasan periode terpilih</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border" id="heatmap-legend-label">Rendah → Tinggi</span>
                </div>
            </div>
            <div class="card-body p-0 position-relative">
                <div id="dashboard-kabupaten-heatmap" class="leaflet-map" style="height:{{ ($compact ?? false) ? 300 : 420 }}px;width:100%;border-radius:0 0 .25rem .25rem;"></div>
                <div id="heatmap-legend" class="position-absolute bg-white border rounded shadow-sm p-2" style="bottom:24px;left:24px;z-index:1000;font-size:12px;line-height:1.4;pointer-events:none;">
                    <div class="fw-semibold mb-1">Pengawasan</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#F5EDD6;border-radius:50%;margin-right:4px;border:1px solid #e2a71233;"></span> 0</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#E8C547;border-radius:50%;margin-right:4px;border:1px solid #e2a71244;"></span> 1 sampai 2</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#C9A635;border-radius:50%;margin-right:4px;border:1px solid #e2a71255;"></span> 3 sampai 5</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#e2a712;border-radius:50%;margin-right:4px;border:1px solid #c8940e;"></span> 6 sampai 10</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#a88a2b;border-radius:50%;margin-right:4px;border:1px solid #3a2a06;"></span> &gt; 10</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="{{ asset('libs/leaflet/leaflet.css') }}" rel="stylesheet" type="text/css" />
<style>
    .pantau-map-marker-wrap {
        background: transparent;
        border: none;
    }

    .pantau-map-marker {
        display: block;
        filter: drop-shadow(0 2px 5px rgba(58, 42, 6, 0.22));
    }

    .pantau-map-marker svg {
        display: block;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('libs/leaflet/leaflet.js') }}"></script>
<script>
    window.__dashboardHeatmapConfig = {
        logoUrl: @json(asset('images/logo_web.png')),
    };
    window.__dashboardHeatmapData = @json($heatmap ?? []);
</script>
<script src="{{ asset('js/pages/dashboard-heatmap.init.js') }}"></script>
@endpush

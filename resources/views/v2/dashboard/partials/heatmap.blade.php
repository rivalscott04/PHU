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
                <div id="dashboard-kabupaten-heatmap" class="leaflet-map" style="height:420px;border-radius:0 0 .25rem .25rem;"></div>
                <div id="heatmap-legend" class="position-absolute bg-white border rounded shadow-sm p-2" style="bottom:24px;left:24px;z-index:1000;font-size:12px;line-height:1.4;">
                    <div class="fw-semibold mb-1">Pengawasan</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#c0c9f6;border-radius:50%;margin-right:4px;"></span> 0</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#9cabf0;border-radius:50%;margin-right:4px;"></span> 1 sampai 2</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#798ceb;border-radius:50%;margin-right:4px;"></span> 3 sampai 5</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#556ee6;border-radius:50%;margin-right:4px;"></span> 6 sampai 10</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#435fe3;border-radius:50%;margin-right:4px;"></span> &gt; 10</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="{{ asset('libs/leaflet/leaflet.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('libs/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('js/pages/dashboard-heatmap.init.js') }}"></script>
@endpush

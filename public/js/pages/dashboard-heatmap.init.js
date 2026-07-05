(function () {
    const mapEl = document.getElementById('dashboard-kabupaten-heatmap');
    if (!mapEl || typeof L === 'undefined') {
        return;
    }

    const BRAND = {
        accent: '#e2a712',
        accentHover: '#c8940e',
        accentDark: '#a88a2b',
        gold: '#C9A635',
        cream: '#FAFAF8',
        creamDeep: '#F5EDD6',
        text: '#333333',
        white: '#ffffff',
    };

    // Batas provinsi NTB: Lombok (barat) + Sumbawa (timur)
    const NTB_BOUNDS = L.latLngBounds(
        [-9.05, 115.82],
        [-8.08, 119.05]
    );
    const NTB_DEFAULT_CENTER = [-8.55, 117.38];

    let map = null;
    let layerGroup = null;
    let lastRegions = [];

    function getDefaultZoom() {
        const width = mapEl.offsetWidth || 800;
        return width >= 640 ? 8 : 7;
    }

    function isKabupatenFiltered() {
        const form = document.getElementById('dashboard-filter-form');
        if (!form) {
            return false;
        }

        return !!new FormData(form).get('kabupaten');
    }

    function fitDefaultView() {
        if (!map) {
            return;
        }

        map.setView(NTB_DEFAULT_CENTER, getDefaultZoom(), { animate: false });
    }

    function fitMapView(regions) {
        if (!map) {
            return;
        }

        if (isKabupatenFiltered() && regions.length === 1) {
            map.setView([regions[0].lat, regions[0].lng], 10, { animate: false });
            return;
        }

        fitDefaultView();
    }

    function refreshMapLayout(regions) {
        if (!map) {
            return;
        }

        map.invalidateSize();
        fitMapView(regions || []);
    }

    function getColor(value) {
        if (value > 10) return BRAND.accentDark;
        if (value > 5) return BRAND.accent;
        if (value > 2) return BRAND.gold;
        if (value > 0) return '#E8C547';
        return BRAND.creamDeep;
    }

    function getMarkerSize(value) {
        if (value > 10) return 46;
        if (value > 5) return 42;
        if (value > 2) return 38;
        if (value > 0) return 34;
        return 30;
    }

    function markerHtml(color, size) {
        const pinHeight = Math.round(size * 1.28);

        return `
            <div class="pantau-map-marker" style="width:${size}px;height:${pinHeight}px;">
                <svg viewBox="0 0 40 52" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M20 1C9.507 1 1 9.507 1 20c0 14.5 19 31 19 31s19-16.5 19-31C39 9.507 30.493 1 20 1z"
                        fill="${color}" stroke="${BRAND.white}" stroke-width="2"/>
                    <circle cx="20" cy="19" r="11.5" fill="${BRAND.white}"/>
                    <g transform="translate(20 19) scale(0.24) translate(-32 -32)">
                        <path d="M10 46V24L26 12L42 24V46H10Z" stroke="${BRAND.accentHover}" stroke-width="3.5" stroke-linejoin="round" fill="none"/>
                        <path d="M10 32H42" stroke="${BRAND.accentHover}" stroke-width="3.5"/>
                        <circle cx="48" cy="40" r="13" stroke="${BRAND.accentHover}" stroke-width="3.5" fill="none"/>
                        <path d="M57 49L62 54" stroke="${BRAND.accentHover}" stroke-width="3.5" stroke-linecap="round"/>
                    </g>
                </svg>
            </div>
        `;
    }

    function createMarkerIcon(intensity) {
        const size = getMarkerSize(intensity);
        const pinHeight = Math.round(size * 1.28);

        return L.divIcon({
            className: 'pantau-map-marker-wrap',
            html: markerHtml(getColor(intensity), size),
            iconSize: [size, pinHeight],
            iconAnchor: [size / 2, pinHeight],
            popupAnchor: [0, -pinHeight + 4],
            tooltipAnchor: [0, -pinHeight + 8],
        });
    }

    function popupHtml(region) {
        return `
            <div style="min-width:180px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <img src="${window.__dashboardHeatmapConfig?.logoUrl || '/images/logo-icon.svg'}" alt="PANTAU" width="22" height="22">
                    <strong style="color:${BRAND.text};">${region.kabupaten}</strong>
                </div>
                <hr class="my-2">
                <div>Travel: <strong>${region.travel}</strong></div>
                <div>Pengawasan: <strong style="color:${BRAND.accent};">${region.pengawasan}</strong></div>
                <div>Temuan aktif: <strong>${region.temuan_aktif}</strong></div>
                <div>Rata-rata risiko: <strong>${region.avg_risk || 0}</strong></div>
            </div>
        `;
    }

    function ensureMap() {
        if (map) {
            return;
        }

        map = L.map('dashboard-kabupaten-heatmap', {
            scrollWheelZoom: true,
            zoomControl: true,
            maxBounds: NTB_BOUNDS.pad(0.12),
            maxBoundsViscosity: 0.85,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }).addTo(map);

        layerGroup = L.layerGroup().addTo(map);

        fitMapView([]);
    }

    function renderHeatmap(regions) {
        ensureMap();

        lastRegions = Array.isArray(regions) ? regions : [];
        layerGroup.clearLayers();

        (regions || []).forEach(function (region) {
            const intensity = region.intensity ?? region.pengawasan ?? 0;

            L.marker([region.lat, region.lng], {
                icon: createMarkerIcon(intensity),
                riseOnHover: true,
            })
                .bindTooltip(
                    `<strong style="color:${BRAND.text};">${region.kabupaten}</strong><br>Pengawasan: <strong style="color:${BRAND.accent};">${intensity}</strong>`,
                    { direction: 'top', offset: [0, -6], sticky: true }
                )
                .bindPopup(popupHtml(region))
                .addTo(layerGroup);
        });

        fitMapView(lastRegions);

        setTimeout(function () {
            refreshMapLayout(lastRegions);
        }, 100);

        setTimeout(function () {
            refreshMapLayout(lastRegions);
        }, 350);
    }

    window.DashboardHeatmap = {
        render: renderHeatmap,
        resetView: function () {
            refreshMapLayout(lastRegions);
        },
        invalidateSize: function () {
            refreshMapLayout(lastRegions);
        },
        fetch: function (url, queryString) {
            const endpoint = url + (url.includes('?') ? '&' : '?') + (queryString || '');

            return fetch(endpoint, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (payload) {
                    if (payload.success) {
                        renderHeatmap(payload.data);
                    }
                });
        },
    };

    function bootstrap() {
        const pane = mapEl.closest('.tab-pane');
        const data = window.__dashboardHeatmapData;
        const regions = Array.isArray(data) ? data : [];

        if (pane && !pane.classList.contains('active')) {
            const tabTrigger = document.querySelector('[data-bs-target="#' + pane.id + '"]');
            if (tabTrigger) {
                tabTrigger.addEventListener('shown.bs.tab', function () {
                    renderHeatmap(regions);
                }, { once: true });
                return;
            }
        }

        renderHeatmap(regions);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})();

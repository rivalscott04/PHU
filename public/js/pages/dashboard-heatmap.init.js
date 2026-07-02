(function () {
    const mapEl = document.getElementById('dashboard-kabupaten-heatmap');
    if (!mapEl || typeof L === 'undefined') {
        return;
    }

    const NTB_CENTER = [-8.65, 117.36];
    const NTB_ZOOM = 8;

    let map = null;
    let layerGroup = null;

    function getColor(value) {
        if (value > 10) return '#435fe3';
        if (value > 5) return '#556ee6';
        if (value > 2) return '#798ceb';
        if (value > 0) return '#9cabf0';
        return '#c0c9f6';
    }

    function getRadius(value) {
        if (value > 10) return 22;
        if (value > 5) return 18;
        if (value > 2) return 14;
        if (value > 0) return 10;
        return 8;
    }

    function popupHtml(region) {
        return `
            <div style="min-width:180px;">
                <strong>${region.kabupaten}</strong>
                <hr class="my-2">
                <div>Travel: <strong>${region.travel}</strong></div>
                <div>Pengawasan: <strong>${region.pengawasan}</strong></div>
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
            scrollWheelZoom: false,
        }).setView(NTB_CENTER, NTB_ZOOM);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }).addTo(map);

        layerGroup = L.layerGroup().addTo(map);
    }

    function renderHeatmap(regions) {
        ensureMap();

        layerGroup.clearLayers();

        (regions || []).forEach(function (region) {
            const intensity = region.intensity ?? region.pengawasan ?? 0;

            L.circleMarker([region.lat, region.lng], {
                radius: getRadius(intensity),
                fillColor: getColor(intensity),
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.82,
            })
                .bindPopup(popupHtml(region))
                .addTo(layerGroup);
        });

        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }

    window.DashboardHeatmap = {
        render: renderHeatmap,
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

    if (Array.isArray(window.__dashboardHeatmapData)) {
        renderHeatmap(window.__dashboardHeatmapData);
    }
})();

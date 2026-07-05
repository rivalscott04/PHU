<div class="offcanvas offcanvas-end" tabindex="-1" id="pengaduanOffcanvas" aria-labelledby="pengaduanOffcanvasLabel" style="width: min(480px, 100vw);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="pengaduanOffcanvasLabel">Pengaduan</h5>
            <small class="text-muted" id="pengaduanOffcanvasSubtitle"></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body p-0" id="pengaduanOffcanvasBody">
        <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <p class="mb-0 mt-2 small">Memuat data pengaduan...</p>
        </div>
    </div>
</div>

@once
@push('js')
<script>
(function () {
    const offcanvasEl = document.getElementById('pengaduanOffcanvas');
    if (!offcanvasEl || typeof bootstrap === 'undefined') return;

    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
    const titleEl = document.getElementById('pengaduanOffcanvasLabel');
    const subtitleEl = document.getElementById('pengaduanOffcanvasSubtitle');
    const bodyEl = document.getElementById('pengaduanOffcanvasBody');
    const urlTemplate = @json(route('v2.monitoring.travel.pengaduan', ['travel' => '__ID__']));

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        })[char]);
    }

    function renderLoading() {
        bodyEl.innerHTML = `
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <p class="mb-0 mt-2 small">Memuat data pengaduan...</p>
            </div>`;
    }

    function renderError(message) {
        bodyEl.innerHTML = `<div class="alert alert-danger m-3 mb-0">${escapeHtml(message)}</div>`;
    }

    function renderEmpty() {
        bodyEl.innerHTML = `<div class="text-center text-muted py-5 px-3">Belum ada pengaduan untuk travel ini.</div>`;
    }

    function renderList(items) {
        bodyEl.innerHTML = items.map((item) => {
            const notes = item.admin_notes
                ? `<p class="mb-0 mt-2 small text-muted"><strong>Catatan penyelesaian:</strong> ${escapeHtml(item.admin_notes)}</p>`
                : '';
            const berkas = item.has_berkas
                ? `<span class="badge bg-light text-dark border ms-1">Ada lampiran</span>`
                : '';
            const processedBy = item.processed_by
                ? `<div class="small text-muted mt-1">Ditindaklanjuti: ${escapeHtml(item.processed_by)}</div>`
                : '';

            return `
                <div class="border-bottom px-3 py-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-semibold">${escapeHtml(item.nama_pengadu)}</div>
                            <div class="small text-muted">${escapeHtml(item.created_at || '-')}</div>
                        </div>
                        <span class="badge bg-${escapeHtml(item.status_badge || 'secondary')}">${escapeHtml(item.status_label || item.status)}</span>
                    </div>
                    <p class="mb-0 small">${escapeHtml(item.hal_aduan)}</p>
                    ${notes}
                    ${processedBy}
                    ${item.completed_at ? `<div class="small text-muted mt-1">Selesai: ${escapeHtml(item.completed_at)}</div>` : ''}
                    ${berkas}
                </div>`;
        }).join('');
    }

    function openPengaduanPanel(travelId, travelName) {
        titleEl.textContent = 'Pengaduan';
        subtitleEl.textContent = travelName || '';
        renderLoading();
        offcanvas.show();

        fetch(urlTemplate.replace('__ID__', travelId), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data pengaduan.');
                }

                return response.json();
            })
            .then((result) => {
                if (!result.success) {
                    throw new Error(result.message || 'Gagal memuat data pengaduan.');
                }

                const travel = result.data?.travel || {};
                titleEl.textContent = `Pengaduan (${result.data?.total ?? 0})`;
                subtitleEl.textContent = [travel.name, travel.kabupaten].filter(Boolean).join(' · ');

                const items = result.data?.pengaduan || [];
                if (!items.length) {
                    renderEmpty();
                    return;
                }

                renderList(items);
            })
            .catch((error) => {
                renderError(error.message || 'Terjadi kesalahan saat memuat pengaduan.');
            });
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.pengaduan-drilldown');
        if (!trigger) return;

        event.preventDefault();
        openPengaduanPanel(trigger.dataset.travelId, trigger.dataset.travelName);
    });
})();
</script>
@endpush
@endonce

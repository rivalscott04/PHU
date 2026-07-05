@php
    $isAdminPicker = in_array(auth()->user()->role, ['admin', 'kabupaten']);
    $selectedJamaah = collect($selectedJamaah ?? []);
    $colspan = $isAdminPicker ? 5 : 4;
@endphp

<div class="col-12 mb-3" id="bapJamaahPicker"
    data-options-url="{{ route('bap.jamaah.options') }}"
    data-ignore-bap-id="{{ $ignoreBapId ?? '' }}"
    data-is-admin="{{ $isAdminPicker ? '1' : '0' }}"
    data-total="{{ (int) ($jamaahTotalCount ?? 0) }}"
    data-initial-selected='@json($selectedJamaah->map(fn ($j) => ["id" => $j->id, "nama" => $j->nama, "nik" => $j->nik])->values())'>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <label class="form-label mb-0">Pilih Jamaah Keberangkatan <span class="text-danger">*</span></label>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-primary" id="jamaahSelectedCount">Dipilih: {{ $selectedJamaah->count() }} jamaah</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="jamaahSelectAll" disabled>
                Pilih semua tersedia
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="jamaahClearAll">
                Kosongkan
            </button>
        </div>
    </div>

    @if ($isAdminPicker)
        <small class="text-muted d-block mb-2">Pilih PPIU terlebih dahulu. Daftar jamaah dimuat bertahap saat scroll.</small>
    @else
        <small class="text-muted d-block mb-2">Centang jamaah yang berangkat. Daftar dimuat per halaman — scroll ke bawah untuk memuat lebih banyak.</small>
    @endif

    @error('jamaah_ids')
        <div class="text-danger small mb-2">{{ $message }}</div>
    @enderror

    <div id="jamaahSelectedChips" class="d-flex flex-wrap gap-1 mb-2 {{ $selectedJamaah->isEmpty() ? 'd-none' : '' }}"></div>

    <div id="jamaahIdsHidden">
        @foreach ($selectedJamaah as $jamaah)
            <input type="hidden" name="jamaah_ids[]" value="{{ $jamaah->id }}" data-id="{{ $jamaah->id }}">
        @endforeach
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <input type="search" class="form-control form-control-sm flex-grow-1" id="jamaahSearch"
            placeholder="Cari nama atau NIK jamaah..." autocomplete="off" style="min-width: 200px;">
        <small class="text-muted" id="jamaahVisibleCount"></small>
    </div>

    <div class="border rounded bap-jamaah-picker-scroll" id="jamaahPickerScroll">
        <table class="table table-sm table-hover mb-0 align-middle" id="jamaahPickerTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;"></th>
                    <th>Nama</th>
                    <th>NIK</th>
                    @if ($isAdminPicker)
                        <th>PPIU</th>
                    @endif
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="jamaahPickerBody">
                <tr id="jamaahPickerLoading">
                    <td colspan="{{ $colspan }}" class="text-center text-muted py-4">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memuat jamaah...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <input type="hidden" id="people" name="people" value="{{ $selectedJamaah->count() }}">

    <small class="text-muted d-block mt-2" id="jamaahPickerHint">
        Total {{ (int) ($jamaahTotalCount ?? 0) }} jamaah — scroll ke bawah untuk memuat halaman berikutnya.
    </small>
</div>

@once
    @push('styles')
        <style>
            .bap-jamaah-picker-scroll {
                max-height: 320px;
                overflow-y: auto;
            }
            .bap-jamaah-picker-scroll thead th {
                position: sticky;
                top: 0;
                z-index: 1;
                background: var(--bs-table-bg, #f8f9fa);
            }
            .jamaah-chip {
                font-size: 0.75rem;
            }
        </style>
    @endpush
    @push('js')
        <script>
            (function () {
                const root = document.getElementById('bapJamaahPicker');
                if (!root) {
                    return;
                }

                const optionsUrl = root.dataset.optionsUrl;
                const ignoreBapId = root.dataset.ignoreBapId || '';
                const isAdminPicker = root.dataset.isAdmin === '1';
                const colspan = isAdminPicker ? 5 : 4;
                const ppiuSelect = document.getElementById('ppiuname');
                const countBadge = document.getElementById('jamaahSelectedCount');
                const peopleInput = document.getElementById('people');
                const searchInput = document.getElementById('jamaahSearch');
                const visibleCountEl = document.getElementById('jamaahVisibleCount');
                const scrollEl = document.getElementById('jamaahPickerScroll');
                const tbody = document.getElementById('jamaahPickerBody');
                const hiddenWrap = document.getElementById('jamaahIdsHidden');
                const chipsWrap = document.getElementById('jamaahSelectedChips');
                const selectAllBtn = document.getElementById('jamaahSelectAll');
                const clearAllBtn = document.getElementById('jamaahClearAll');

                const selected = new Map();
                JSON.parse(root.dataset.initialSelected || '[]').forEach((item) => {
                    selected.set(Number(item.id), item);
                });

                let page = 1;
                let lastPage = 1;
                let loading = false;
                let searchTimer = null;
                let currentSearch = '';
                let totalRecords = Number(root.dataset.total || 0);
                const loadedIds = new Set();

                function selectedPpiu() {
                    if (!isAdminPicker || !ppiuSelect) {
                        return '';
                    }
                    return ppiuSelect.tagName === 'SELECT' ? (ppiuSelect.value || '') : (ppiuSelect.value?.trim() || '');
                }

                function canLoad() {
                    return !isAdminPicker || selectedPpiu() !== '';
                }

                function syncHiddenInputs() {
                    hiddenWrap.innerHTML = '';
                    selected.forEach((item, id) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'jamaah_ids[]';
                        input.value = id;
                        input.dataset.id = id;
                        hiddenWrap.appendChild(input);
                    });
                }

                function renderChips() {
                    chipsWrap.innerHTML = '';
                    if (selected.size === 0) {
                        chipsWrap.classList.add('d-none');
                        return;
                    }
                    chipsWrap.classList.remove('d-none');
                    selected.forEach((item) => {
                        const chip = document.createElement('span');
                        chip.className = 'badge bg-light text-dark border jamaah-chip';
                        chip.textContent = item.nama + (item.nik ? ` · ${item.nik}` : '');
                        chipsWrap.appendChild(chip);
                    });
                }

                function updateCount() {
                    const count = selected.size;
                    if (countBadge) {
                        countBadge.textContent = `Dipilih: ${count} jamaah`;
                    }
                    if (peopleInput) {
                        peopleInput.value = count;
                    }
                    if (selectAllBtn) {
                        selectAllBtn.disabled = !canLoad() || totalRecords === 0;
                    }
                    syncHiddenInputs();
                    renderChips();
                    tbody.querySelectorAll('.jamaah-checkbox').forEach((checkbox) => {
                        checkbox.checked = selected.has(Number(checkbox.value));
                    });
                }

                function buildRow(item) {
                    const tr = document.createElement('tr');
                    tr.className = 'jamaah-picker-row' + (item.is_busy ? ' table-secondary' : '');
                    tr.dataset.id = item.id;

                    const statusBadge = item.is_busy
                        ? '<span class="badge bg-warning text-dark" style="font-size:10px;">Pengajuan aktif</span>'
                        : '<span class="badge bg-success" style="font-size:10px;">Tersedia</span>';

                    const ppiuCell = isAdminPicker
                        ? `<td class="small">${item.ppiuname || '—'}</td>`
                        : '';

                    tr.innerHTML = `
                        <td>
                            <input type="checkbox" class="form-check-input jamaah-checkbox"
                                value="${item.id}" ${item.is_busy ? 'disabled' : ''}
                                ${selected.has(item.id) ? 'checked' : ''}>
                        </td>
                        <td>${item.nama}</td>
                        <td class="text-muted small">${item.nik || '—'}</td>
                        ${ppiuCell}
                        <td>${statusBadge}</td>
                    `;

                    const checkbox = tr.querySelector('.jamaah-checkbox');
                    checkbox?.addEventListener('change', function () {
                        if (this.checked) {
                            selected.set(item.id, { id: item.id, nama: item.nama, nik: item.nik });
                        } else {
                            selected.delete(item.id);
                        }
                        updateCount();
                    });

                    return tr;
                }

                function setStatusMessage(message) {
                    tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted py-4">${message}</td></tr>`;
                }

                function updateVisibleCount(meta) {
                    if (!visibleCountEl || !meta) {
                        return;
                    }
                    const loaded = tbody.querySelectorAll('.jamaah-picker-row').length;
                    if (currentSearch) {
                        visibleCountEl.textContent = `Menampilkan ${loaded} dari ${meta.total}`;
                    } else {
                        visibleCountEl.textContent = `Dimuat ${loaded} / ${meta.total}`;
                    }
                    totalRecords = meta.total;
                }

                async function fetchPage(reset = false) {
                    if (loading) {
                        return;
                    }
                    if (!canLoad()) {
                        setStatusMessage('Pilih PPIU terlebih dahulu untuk memuat daftar jamaah.');
                        return;
                    }
                    if (!reset && page > lastPage) {
                        return;
                    }

                    loading = true;
                    if (reset) {
                        page = 1;
                        loadedIds.clear();
                        tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-1"></span> Memuat jamaah...</td></tr>`;
                    } else {
                        const loader = document.createElement('tr');
                        loader.id = 'jamaahPickerLoadingMore';
                        loader.innerHTML = `<td colspan="${colspan}" class="text-center text-muted py-2"><span class="spinner-border spinner-border-sm me-1"></span> Memuat...</td>`;
                        tbody.appendChild(loader);
                    }

                    const params = new URLSearchParams({
                        page: String(page),
                        per_page: '15',
                    });
                    if (currentSearch) {
                        params.set('search', currentSearch);
                    }
                    const ppiu = selectedPpiu();
                    if (ppiu) {
                        params.set('ppiuname', ppiu);
                    }
                    if (ignoreBapId) {
                        params.set('ignore_bap_id', ignoreBapId);
                    }

                    try {
                        const response = await fetch(`${optionsUrl}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!response.ok) {
                            throw new Error('Gagal memuat data jamaah');
                        }
                        const payload = await response.json();

                        if (reset) {
                            tbody.innerHTML = '';
                        } else {
                            document.getElementById('jamaahPickerLoadingMore')?.remove();
                        }

                        if (payload.data.length === 0 && page === 1) {
                            setStatusMessage(currentSearch ? 'Tidak ada jamaah yang cocok.' : 'Belum ada data jamaah.');
                        } else {
                            payload.data.forEach((item) => {
                                if (loadedIds.has(item.id)) {
                                    return;
                                }
                                loadedIds.add(item.id);
                                tbody.appendChild(buildRow(item));
                            });
                        }

                        lastPage = payload.meta.last_page;
                        updateVisibleCount(payload.meta);
                        page += 1;
                    } catch (error) {
                        if (reset) {
                            setStatusMessage('Gagal memuat jamaah. Coba lagi.');
                        }
                    } finally {
                        loading = false;
                    }
                }

                scrollEl?.addEventListener('scroll', function () {
                    if (loading || page > lastPage) {
                        return;
                    }
                    const nearBottom = scrollEl.scrollTop + scrollEl.clientHeight >= scrollEl.scrollHeight - 48;
                    if (nearBottom) {
                        fetchPage(false);
                    }
                });

                searchInput?.addEventListener('input', function () {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        currentSearch = this.value.trim();
                        fetchPage(true);
                    }, 300);
                });

                ppiuSelect?.addEventListener('change', function () {
                    selected.clear();
                    currentSearch = '';
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    updateCount();
                    fetchPage(true);
                });

                selectAllBtn?.addEventListener('click', async function () {
                    if (!canLoad()) {
                        return;
                    }
                    selectAllBtn.disabled = true;
                    const params = new URLSearchParams({ available_only: '1' });
                    const ppiu = selectedPpiu();
                    if (ppiu) {
                        params.set('ppiuname', ppiu);
                    }
                    if (ignoreBapId) {
                        params.set('ignore_bap_id', ignoreBapId);
                    }
                    try {
                        const response = await fetch(`${optionsUrl}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        const payload = await response.json();
                        const rows = tbody.querySelectorAll('.jamaah-picker-row');
                        const rowMap = new Map();
                        rows.forEach((row) => {
                            const id = Number(row.dataset.id);
                            const nama = row.children[1]?.textContent || `Jamaah #${id}`;
                            const nik = row.children[2]?.textContent?.trim();
                            rowMap.set(id, { id, nama, nik: nik === '—' ? '' : nik });
                        });
                        (payload.ids || []).forEach((id) => {
                            const numId = Number(id);
                            const meta = rowMap.get(numId) || { id: numId, nama: `Jamaah #${numId}`, nik: '' };
                            selected.set(numId, meta);
                        });
                        updateCount();
                    } finally {
                        selectAllBtn.disabled = false;
                    }
                });

                clearAllBtn?.addEventListener('click', function () {
                    selected.clear();
                    updateCount();
                });

                updateCount();
                if (canLoad()) {
                    fetchPage(true);
                } else if (isAdminPicker) {
                    setStatusMessage('Pilih PPIU terlebih dahulu untuk memuat daftar jamaah.');
                } else {
                    fetchPage(true);
                }
            })();
        </script>
    @endpush
@endonce

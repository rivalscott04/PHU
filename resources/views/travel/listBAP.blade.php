@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            @include('partials.bap-module-info', ['variant' => 'pemberangkatan'])
            @if($guide = \App\Support\RoleWorkflowGuide::for('bap_list'))
                @include('partials.workflow-guide', ['guide' => $guide])
            @endif

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">BA Pemberangkatan</h4>
                    <p class="text-muted mb-0 small">Daftar pengajuan keberangkatan jamaah</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if (auth()->user()->role === 'admin')
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#bapSettingsModal">
                            <i class="bx bx-cog me-1"></i> Pengaturan Penandatangan
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#bapAirlinesModal">
                            <i class="bx bx-plane me-1"></i> Kelola Maskapai
                        </button>
                    @endif
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                        <a href="{{ route('verify-e-sign') }}" class="btn btn-sm btn-info">
                            <i class="bx bx-qr-scan me-1"></i> Verifikasi E Sign
                        </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['user', 'admin', 'kabupaten'], true))
                        <a href="{{ route('bap.export') }}" class="btn btn-sm btn-outline-success">
                            <i class="bx bx-export me-1"></i> Unduh Rekap
                        </a>
                    @endif
                    <a href="{{ route('form.bap') }}" onclick="return checkJamaah({{ $jamaahCount }});"
                        class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Daftar Pengajuan</h5>
                    <small class="text-muted" id="bapResultsInfoTop">Total: <strong>{{ $data->total() }}</strong> pengajuan</small>
                </div>
                <div class="p-3 border-bottom bg-light">
                    <div class="row align-items-center g-2">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bx bx-search text-muted"></i>
                                </span>
                                <input type="text"
                                    class="form-control border-start-0"
                                    id="bapSearchInput"
                                    placeholder="Cari nama, PPIU, kab/kota, nomor surat, status..."
                                    value="{{ request('search') }}"
                                    autocomplete="off">
                                <div class="bap-search-loading px-2 align-self-center" style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="bapPerPageFilter">
                                @foreach([10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }} / halaman</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>PPIU</th>
                                <th>Nomor HP</th>
                                <th>Kab/Kota</th>
                                <th>Tgl Berangkat</th>
                                <th>Jumlah Jamaah</th>
                                <th>Harga/Orang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bapTableBody">
                            @include('travel.partials.bap-table-body', compact('data'))
                        </tbody>
                    </table>
                </div>
                <div id="bapPaginationContainer">
                    @include('travel.partials.bap-pagination', compact('data'))
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'admin')
        <div class="modal fade" id="bapSettingsModal" tabindex="-1" aria-labelledby="bapSettingsModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bapSettingsModalLabel">
                            <i class="bx bx-cog text-primary"></i> Pengaturan Penandatangan BA
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">
                            Data ini muncul di blok tanda tangan Kanwil saat BA dicetak dan saat verifikasi QR.
                        </p>
                        <form id="bapSettingsForm">
                            @csrf
                            <div class="mb-3">
                                <label for="bap_nama_penandatangan" class="form-label">Nama Pejabat *</label>
                                <input type="text" class="form-control" id="bap_nama_penandatangan"
                                    name="nama_penandatangan" required
                                    placeholder="Contoh: Dr. Ahmad Hidayat, M.Ag">
                                <small class="form-text text-muted">Nama lengkap pejabat yang menandatangani BA</small>
                            </div>
                            <div class="mb-3">
                                <label for="bap_jabatan_penandatangan" class="form-label">Jabatan *</label>
                                <input type="text" class="form-control" id="bap_jabatan_penandatangan"
                                    name="jabatan_penandatangan" required
                                    value="{{ config('app.kanwil.bap_kanwil_jabatan') }}"
                                    placeholder="Contoh: Kepala Bidang Bina Haji">
                                <small class="form-text text-muted">Jabatan resmi pejabat penandatangan Kanwil</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="saveBapSettings">
                            <i class="bx bx-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->role === 'admin')
        <div class="modal fade" id="bapAirlinesModal" tabindex="-1" aria-labelledby="bapAirlinesModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bapAirlinesModalLabel">
                            <i class="bx bx-plane text-primary"></i> Kelola Maskapai BA
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">
                            Daftar maskapai yang muncul di form BA Pemberangkatan untuk travel yang sudah diverifikasi.
                        </p>
                        <form id="bapAirlineAddForm" class="row g-2 align-items-end mb-3">
                            @csrf
                            <div class="col-md-7">
                                <label for="bap_airline_name" class="form-label">Nama maskapai</label>
                                <input type="text" class="form-control" id="bap_airline_name" name="name" required
                                    placeholder="Contoh: Garuda Indonesia">
                            </div>
                            <div class="col-md-3">
                                <label for="bap_airline_sort" class="form-label">Urutan</label>
                                <input type="number" class="form-control" id="bap_airline_sort" name="sort_order" min="0" max="9999">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-plus"></i> Tambah
                                </button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th class="text-center" style="width: 90px;">Urutan</th>
                                        <th class="text-center" style="width: 90px;">Aktif</th>
                                        <th class="text-end text-nowrap" style="width: 96px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bapAirlinesTableBody">
                                    <tr>
                                        <td colspan="4" class="text-muted text-center py-3">Memuat daftar maskapai...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        function checkJamaah(jamaahCount) {
            if (jamaahCount == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Maaf terjadi Kesalahan',
                    text: 'Silahkan mengisi menu jamaah terlebih dahulu.',
                    confirmButtonText: 'Tutup'
                });
                return false;
            }
            return true;
        }

        function handleStatusChange(itemId, status) {
            const form = document.getElementById('statusForm' + itemId);

            // Submit form langsung untuk semua status
            form.submit();
        }

        function initBapListing() {
            const searchInput = document.getElementById('bapSearchInput');
            const perPageFilter = document.getElementById('bapPerPageFilter');
            const loadingEl = document.querySelector('.bap-search-loading');

            if (!searchInput) {
                return;
            }

            let searchTimeout;

            function updateBapResultsInfo(data) {
                const info = data.pagination_info;
                const top = document.getElementById('bapResultsInfoTop');
                const bottom = document.getElementById('bapResultsInfo');

                if (top) {
                    top.innerHTML = `Total: <strong>${info.total}</strong> pengajuan`;
                }
                if (bottom) {
                    bottom.textContent = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
                }
            }

            function fetchBapListing(params = {}) {
                if (loadingEl) {
                    loadingEl.style.display = 'block';
                }

                const queryParams = new URLSearchParams();
                if (searchInput.value.trim()) {
                    queryParams.append('search', searchInput.value.trim());
                }
                if (perPageFilter && perPageFilter.value) {
                    queryParams.append('per_page', perPageFilter.value);
                }
                if (params.page) {
                    queryParams.append('page', params.page);
                }

                fetch(`{{ route('bap') }}?${queryParams.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            return;
                        }

                        document.getElementById('bapTableBody').innerHTML = data.tableBody;
                        document.getElementById('bapPaginationContainer').innerHTML = data.pagination;
                        updateBapResultsInfo(data);
                        bindBapPaginationLinks();
                    })
                    .catch(error => console.error('BAP listing fetch error:', error))
                    .finally(() => {
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                    });
            }

            function bindBapPaginationLinks() {
                document.querySelectorAll('#bapPaginationContainer .pagination a.page-link').forEach(function(link) {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            fetchBapListing({ page });
                        }
                    });
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    fetchBapListing();
                }, 350);
            });

            if (perPageFilter) {
                perPageFilter.addEventListener('change', function() {
                    fetchBapListing();
                });
            }

            bindBapPaginationLinks();
        }

        document.addEventListener('DOMContentLoaded', initBapListing);

        @if (auth()->user()->role === 'admin')
        document.addEventListener('DOMContentLoaded', function() {
            const saveBtn = document.getElementById('saveBapSettings');
            const settingsModal = document.getElementById('bapSettingsModal');

            if (saveBtn) {
                saveBtn.addEventListener('click', saveBapSettings);
            }

            if (settingsModal) {
                settingsModal.addEventListener('show.bs.modal', loadBapSettings);
            }
        });

        function loadBapSettings() {
            fetch('{{ route('bap.settings') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('bap_nama_penandatangan').value = data?.nama_penandatangan || '';
                    document.getElementById('bap_jabatan_penandatangan').value =
                        data?.jabatan_penandatangan || '{{ config('app.kanwil.bap_kanwil_jabatan') }}';
                })
                .catch(error => console.error('Error loading BA settings:', error));
        }

        function saveBapSettings() {
            const nama = document.getElementById('bap_nama_penandatangan').value.trim();
            const jabatan = document.getElementById('bap_jabatan_penandatangan').value.trim();

            if (!nama || !jabatan) {
                Swal.fire({
                    title: 'Data belum lengkap',
                    text: 'Nama pejabat dan jabatan harus diisi.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const formData = new FormData(document.getElementById('bapSettingsForm'));

            fetch('{{ route('bap.settings.update') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            bootstrap.Modal.getInstance(document.getElementById('bapSettingsModal')).hide();
                        });
                    } else {
                        throw new Error(data.message || 'Gagal menyimpan');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menyimpan pengaturan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        const airlinesModal = document.getElementById('bapAirlinesModal');
        const airlineAddForm = document.getElementById('bapAirlineAddForm');

        if (airlinesModal) {
            airlinesModal.addEventListener('show.bs.modal', loadBapAirlines);
        }

        if (airlineAddForm) {
            airlineAddForm.addEventListener('submit', function (event) {
                event.preventDefault();
                addBapAirline();
            });
        }

        function loadBapAirlines() {
            fetch('{{ route('bap.airlines.index') }}')
                .then(response => response.json())
                .then(renderBapAirlines)
                .catch(error => {
                    console.error('Error loading airlines:', error);
                    document.getElementById('bapAirlinesTableBody').innerHTML =
                        '<tr><td colspan="4" class="text-danger text-center py-3">Gagal memuat daftar maskapai.</td></tr>';
                });
        }

        function renderBapAirlines(airlines) {
            const tbody = document.getElementById('bapAirlinesTableBody');

            if (!airlines.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-muted text-center py-3">Belum ada maskapai. Tambahkan di form atas.</td></tr>';
                return;
            }

            tbody.innerHTML = airlines.map(function (airline) {
                return '<tr data-airline-id="' + airline.id + '">' +
                    '<td><input type="text" class="form-control form-control-sm airline-name" value="' + escapeHtml(airline.name) + '"></td>' +
                    '<td><input type="number" class="form-control form-control-sm airline-sort text-center" min="0" max="9999" value="' + airline.sort_order + '"></td>' +
                    '<td class="text-center"><input type="checkbox" class="form-check-input airline-active" ' + (airline.is_active ? 'checked' : '') + '></td>' +
                    '<td class="text-end text-nowrap">' +
                        '<div class="btn-group btn-group-sm" role="group">' +
                            '<button type="button" class="btn btn-outline-primary save-airline" title="Simpan"><i class="bx bx-save"></i></button>' +
                            '<button type="button" class="btn btn-outline-danger delete-airline" title="Hapus"><i class="bx bx-trash"></i></button>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
            }).join('');

            tbody.querySelectorAll('.save-airline').forEach(function (button) {
                button.addEventListener('click', function () {
                    saveBapAirline(button.closest('tr'));
                });
            });

            tbody.querySelectorAll('.delete-airline').forEach(function (button) {
                button.addEventListener('click', function () {
                    deleteBapAirline(button.closest('tr'));
                });
            });
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function addBapAirline() {
            const formData = new FormData(airlineAddForm);
            formData.append('_token', csrfToken());

            fetch('{{ route('bap.airlines.store') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(function (data) {
                    if (!data.success) {
                        throw new Error(data.message || 'Gagal menambahkan maskapai');
                    }
                    airlineAddForm.reset();
                    loadBapAirlines();
                    Swal.fire({ title: 'Berhasil', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false });
                })
                .catch(function (error) {
                    Swal.fire({ title: 'Gagal', text: error.message, icon: 'error' });
                });
        }

        function saveBapAirline(row) {
            const id = row.dataset.airlineId;
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('_method', 'PUT');
            formData.append('name', row.querySelector('.airline-name').value.trim());
            formData.append('sort_order', row.querySelector('.airline-sort').value);
            formData.append('is_active', row.querySelector('.airline-active').checked ? '1' : '0');

            fetch('{{ url('/bap/airlines') }}/' + id, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(function (data) {
                    if (!data.success) {
                        throw new Error(data.message || 'Gagal menyimpan maskapai');
                    }
                    loadBapAirlines();
                    Swal.fire({ title: 'Berhasil', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false });
                })
                .catch(function (error) {
                    Swal.fire({ title: 'Gagal', text: error.message, icon: 'error' });
                });
        }

        function deleteBapAirline(row) {
            const id = row.dataset.airlineId;
            const name = row.querySelector('.airline-name').value.trim();

            Swal.fire({
                title: 'Hapus maskapai?',
                text: name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                const formData = new FormData();
                formData.append('_token', csrfToken());
                formData.append('_method', 'DELETE');

                fetch('{{ url('/bap/airlines') }}/' + id, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(function (data) {
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal menghapus maskapai');
                        }
                        loadBapAirlines();
                        Swal.fire({ title: 'Berhasil', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false });
                    })
                    .catch(function (error) {
                        Swal.fire({ title: 'Gagal', text: error.message, icon: 'error' });
                    });
            });
        }
        @endif
    </script>
@endpush

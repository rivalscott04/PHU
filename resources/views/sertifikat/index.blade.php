@extends('layouts.app')

@section('title', 'Sertifikat PPIU')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Sertifikat PPIU</h4>
                    <div class="page-title-right d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#settingsModal">
                            <i class="bx bx-cog me-1"></i> Pengaturan Penandatangan
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="checkPenandatangan()">
                            <i class="bx bx-plus me-1"></i> Buat Sertifikat
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($guide = \App\Support\RoleWorkflowGuide::for('sertifikat'))
            @include('partials.workflow-guide', ['guide' => $guide])
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="sertifikatSearchInput"
                                        placeholder="Cari PPIU, kepala, nomor surat, jenis..." value="{{ request('search') }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select id="sertifikatPerPageFilter" class="form-select form-select-sm">
                                    @foreach([10, 15, 25, 50] as $size)
                                        <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }} / halaman</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama PPIU</th>
                                        <th>Kepala</th>
                                        <th>Nomor Surat</th>
                                        <th>Jenis</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="sertifikatTableBody">
                                    @include('sertifikat.partials.table-body', compact('sertifikat'))
                                </tbody>
                            </table>
                        </div>

                        <div id="sertifikatPaginationContainer">
                            @include('sertifikat.partials.pagination', compact('sertifikat'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">
                        <i class="bx bx-cog text-primary"></i> Pengaturan Penandatangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Data ini muncul di blok tanda tangan sertifikat PPIU. Kop surat memakai branding Kementerian Haji dan Umroh (bukan Kemenag).
                    </p>
                    <form id="settingsForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_penandatangan" class="form-label">Nama Pejabat *</label>
                            <input type="text" class="form-control" id="nama_penandatangan" name="nama_penandatangan"
                                required placeholder="Contoh: Dr. Ahmad Hidayat, M.Ag">
                            <small class="form-text text-muted">Nama lengkap pejabat penandatangan sertifikat</small>
                        </div>
                        <div class="mb-3">
                            <label for="nip_penandatangan" class="form-label">NIP Pejabat *</label>
                            <input type="text" class="form-control" id="nip_penandatangan" name="nip_penandatangan"
                                required>
                            <small class="form-text text-muted">Nomor Induk Pegawai penandatangan</small>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan_penandatangan" class="form-label">Jabatan *</label>
                            <textarea class="form-control" id="jabatan_penandatangan" name="jabatan_penandatangan"
                                rows="3" required
                                placeholder="Contoh: Kepala Kantor Wilayah Kementerian Haji dan Umroh&#10;Provinsi Nusa Tenggara Barat">{{ config('app.kanwil.sertifikat_kanwil_jabatan') }}</textarea>
                            <small class="form-text text-muted">Jabatan resmi yang tercetak di atas tanda tangan. Bisa lebih dari satu baris.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="saveSettings">
                        <i class="bx bx-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Load settings when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadSettings();
            
            // Add event listener for save settings button
            document.getElementById('saveSettings').addEventListener('click', function(e) {
                e.preventDefault();
                saveSettings();
            });
            
            // Add event listener for modal show
            const settingsModal = document.getElementById('settingsModal');
            if (settingsModal) {
                settingsModal.addEventListener('show.bs.modal', function() {
                    loadSettings();
                });
            }
        });

        function checkPenandatangan() {
            fetch('{{ route('sertifikat.settings') }}')
                .then(response => response.json())
                .then(data => {
                    const incomplete = !data
                        || !data.nama_penandatangan?.trim()
                        || !data.nip_penandatangan?.trim()
                        || !data.jabatan_penandatangan?.trim();

                    if (incomplete) {
                        Swal.fire({
                            title: 'Pengaturan Belum Lengkap',
                            text: 'Isi nama pejabat, NIP, dan jabatan penandatangan terlebih dahulu sebelum membuat sertifikat.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Isi Pengaturan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                bootstrap.Modal.getOrCreateInstance(document.getElementById('settingsModal')).show();
                            }
                        });
                    } else {
                        window.location.href = '{{ route("sertifikat.create") }}';
                    }
                })
                .catch(error => {
                    console.error('Error checking penandatangan:', error);
                    // If there's an error, show warning and proceed
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Tidak dapat memverifikasi penandatangan. Lanjutkan membuat sertifikat?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("sertifikat.create") }}';
                        }
                    });
                });
        }

        function loadSettings() {
            fetch('{{ route('sertifikat.settings') }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('nama_penandatangan').value = data?.nama_penandatangan || '';
                    document.getElementById('nip_penandatangan').value = data?.nip_penandatangan || '';
                    document.getElementById('jabatan_penandatangan').value =
                        data?.jabatan_penandatangan || @json(config('app.kanwil.sertifikat_kanwil_jabatan'));
                })
                .catch(error => {
                    console.error('Error loading settings:', error);
                });
        }

        function saveSettings() {
            const namaPenandatangan = document.getElementById('nama_penandatangan').value.trim();
            const nipPenandatangan = document.getElementById('nip_penandatangan').value.trim();
            const jabatanPenandatangan = document.getElementById('jabatan_penandatangan').value.trim();

            if (!namaPenandatangan || !nipPenandatangan || !jabatanPenandatangan) {
                Swal.fire({
                    title: 'Data belum lengkap',
                    text: 'Nama pejabat, NIP, dan jabatan harus diisi.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const form = document.getElementById('settingsForm');
            const formData = new FormData(form);

            fetch('{{ route('sertifikat.settings.update') }}', {
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
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            bootstrap.Modal.getInstance(document.getElementById('settingsModal'))?.hide();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal menyimpan pengaturan',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving settings:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan pengaturan',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function generatePdf(id, namaPpiu) {
            // Show loading SweetAlert
            Swal.fire({
                title: 'Membuat Sertifikat PDF...',
                text: 'Mohon tunggu, sedang memproses sertifikat untuk ' + namaPpiu,
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request to generate PDF
            fetch('/sertifikat/' + id + '/generate', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        // Show success SweetAlert with options
                        Swal.fire({
                            title: 'Sertifikat Berhasil Dibuat!',
                            text: 'Sertifikat untuk ' + namaPpiu + ' telah berhasil dibuat.',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#28a745',
                            confirmButtonText: '<i class="fas fa-download"></i> Download',
                            cancelButtonText: '<i class="fas fa-eye"></i> Buka',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Download PDF
                                window.location.href = data.download_url;
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Open PDF in new tab
                                window.open(data.view_url, '_blank');
                            }

                            // Reload the page to update the UI
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Terjadi kesalahan saat membuat sertifikat',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error generating PDF:', error);
                    Swal.close();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat membuat sertifikat',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function initSertifikatListing() {
            const searchInput = document.getElementById('sertifikatSearchInput');
            const perPageFilter = document.getElementById('sertifikatPerPageFilter');

            if (!searchInput) {
                return;
            }

            let searchTimeout;

            function updateResultsInfo(data) {
                const info = data.pagination_info;
                const bottom = document.getElementById('sertifikatResultsInfo');
                if (bottom) {
                    bottom.textContent = `Menampilkan ${info.from || 0} sampai ${info.to || 0} dari ${info.total} data`;
                }
            }

            function fetchListing(params = {}) {
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

                fetch(`{{ route('sertifikat.index') }}?${queryParams.toString()}`, {
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

                        document.getElementById('sertifikatTableBody').innerHTML = data.tableBody;
                        document.getElementById('sertifikatPaginationContainer').innerHTML = data.pagination;
                        updateResultsInfo(data);
                        bindPaginationLinks();
                    });
            }

            function bindPaginationLinks() {
                document.querySelectorAll('#sertifikatPaginationContainer .pagination a.page-link').forEach(function(link) {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page');
                        if (page) {
                            fetchListing({ page });
                        }
                    });
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(fetchListing, 350);
            });

            if (perPageFilter) {
                perPageFilter.addEventListener('change', fetchListing);
            }

            bindPaginationLinks();
        }

        document.addEventListener('DOMContentLoaded', initSertifikatListing);

    </script>
@endpush

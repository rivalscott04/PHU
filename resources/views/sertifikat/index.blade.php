@extends('layouts.app')

@section('title', 'Sertifikat PPIU')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Sertifikat PPIU</h4>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal"
                            data-bs-target="#settingsModal">
                            <i class="bx bx-cog"></i> Pengaturan Penandatangan
                        </button>
                        <a href="{{ route('sertifikat.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Buat Sertifikat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
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
                                <tbody>
                                    @forelse($sertifikat as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_ppiu }}</td>
                                            <td>{{ $item->nama_kepala }}</td>
                                            <td>
                                                <small class="text-muted">{{ $item->nomor_surat }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $item->jenis }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $item->jenis_lokasi == 'pusat' ? 'primary' : 'warning' }}">
                                                    {{ ucfirst($item->jenis_lokasi) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $item->getStatusColor() }}">
                                                    {{ $item->getStatusText() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if ($item->sertifikat_path)
                                                        <a href="{{ route('sertifikat.download', $item->id) }}"
                                                            class="btn btn-sm btn-success" title="Download PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('sertifikat.generate', $item->id) }}"
                                                            class="btn btn-sm btn-primary" title="Generate PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('sertifikat.verifikasi', $item->uuid) }}"
                                                        class="btn btn-sm btn-info" title="Verifikasi" target="_blank">
                                                        <i class="fas fa-qrcode"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama_ppiu }}')"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('sertifikat.destroy', $item->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data sertifikat</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $sertifikat->links() }}
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
                    <form id="settingsForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_penandatangan" class="form-label">Nama Penandatangan *</label>
                            <input type="text" class="form-control" id="nama_penandatangan" name="nama_penandatangan"
                                required>
                            <small class="form-text text-muted">Nama lengkap penandatangan sertifikat</small>
                        </div>
                        <div class="mb-3">
                            <label for="nip_penandatangan" class="form-label">NIP Penandatangan *</label>
                            <input type="text" class="form-control" id="nip_penandatangan" name="nip_penandatangan"
                                required>
                            <small class="form-text text-muted">Nomor Induk Pegawai penandatangan</small>
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
        $(document).ready(function() {
            // Auto-hide success alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            });

            // Settings functionality
            const settingsModal = document.getElementById('settingsModal');
            if (settingsModal) {
                settingsModal.addEventListener('show.bs.modal', function() {
                    loadSettings();
                });
            }

            // Save settings button
            const saveSettingsBtn = document.getElementById('saveSettings');
            if (saveSettingsBtn) {
                saveSettingsBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    saveSettings();
                });
            }

            // Settings functions
            function loadSettings() {
                fetch('/sertifikat/settings')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('nama_penandatangan').value = data.nama_penandatangan || '';
                        document.getElementById('nip_penandatangan').value = data.nip_penandatangan || '';
                    })
                    .catch(error => {
                        console.error('Error loading settings:', error);
                    });
            }

            function saveSettings() {
                const namaPenandatangan = document.getElementById('nama_penandatangan').value;
                const nipPenandatangan = document.getElementById('nip_penandatangan').value;

                if (!namaPenandatangan || !nipPenandatangan) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Nama dan NIP penandatangan harus diisi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const form = document.getElementById('settingsForm');
                const formData = new FormData(form);

                fetch('/sertifikat/settings', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide modal first
                            const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
                            if (modal) {
                                modal.hide();
                            }

                            // Show success SweetAlert
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Terjadi kesalahan saat menyimpan pengaturan',
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
        });

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus sertifikat '" + name + "'",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush

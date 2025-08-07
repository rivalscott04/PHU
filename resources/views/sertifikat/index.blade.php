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
                        <button type="button" class="btn btn-primary" onclick="checkPenandatangan()">
                            <i class="fas fa-plus"></i> Buat Sertifikat
                        </button>
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
                                                    @if ($item->pdf_path)
                                                        <a href="{{ route('sertifikat.download', $item->id) }}"
                                                            class="btn btn-sm btn-success" title="Download PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                        <a href="{{ route('sertifikat.view', $item->id) }}"
                                                            class="btn btn-sm btn-info" title="Lihat PDF" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="generatePdf('{{ $item->id }}', '{{ $item->nama_ppiu }}')"
                                                            title="Generate PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('sertifikat.verifikasi', $item->uuid) }}"
                                                        class="btn btn-sm btn-warning" title="Verifikasi" target="_blank">
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
            // Check if penandatangan is set
            fetch('/sertifikat/settings')
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.nama_penandatangan || data.nama_penandatangan.trim() === '') {
                        // Show SweetAlert if penandatangan is not set
                        Swal.fire({
                            title: 'Nama Penandatangan Kosong!',
                            text: 'Silahkan isi penandatangan terlebih dahulu sebelum membuat sertifikat.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Isi Penandatangan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open settings modal
                                $('#settingsModal').modal('show');
                            }
                        });
                    } else {
                        // If penandatangan is set, proceed to create certificate
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
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#settingsModal').modal('hide');
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

        function confirmDelete(id, namaPpiu) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Sertifikat untuk ${namaPpiu} akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit delete form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/sertifikat/${id}`;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush

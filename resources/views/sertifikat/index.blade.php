@extends('layouts.app')

@section('title', 'Sertifikat PPIU')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Sertifikat PPIU</h4>
                <div class="page-title-right">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#settingsModal">
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
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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
                                        <span class="badge bg-{{ $item->jenis_lokasi == 'pusat' ? 'primary' : 'warning' }}">
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
                                            @if($item->sertifikat_path)
                                                <a href="{{ route('sertifikat.download', $item->id) }}" 
                                                   class="btn btn-sm btn-success" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('sertifikat.generate', $item->id) }}" 
                                                   class="btn btn-sm btn-primary" title="Generate">
                                                    <i class="fas fa-file-word"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('sertifikat.verifikasi', $item->uuid) }}" 
                                               class="btn btn-sm btn-info" title="Verifikasi" target="_blank">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="hapusSertifikat({{ $item->id }}, '{{ $item->nama_ppiu }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data sertifikat</td>
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
                        <input type="text" class="form-control" id="nama_penandatangan" name="nama_penandatangan" required>
                        <small class="form-text text-muted">Nama lengkap penandatangan sertifikat</small>
                    </div>
                    <div class="mb-3">
                        <label for="nip_penandatangan" class="form-label">NIP Penandatangan *</label>
                        <input type="text" class="form-control" id="nip_penandatangan" name="nip_penandatangan" required>
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

<!-- Hidden form for delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Settings functionality only
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Settings only');
    
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
        console.log('Settings modal found');
        settingsModal.addEventListener('show.bs.modal', function() {
            console.log('Settings modal opened');
            loadSettings();
        });
    } else {
        console.error('Settings modal not found');
    }

    // Save settings button
    const saveSettingsBtn = document.getElementById('saveSettings');
    if (saveSettingsBtn) {
        console.log('Save settings button found');
        saveSettingsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save settings button clicked');
            saveSettings();
        });
    } else {
        console.error('Save settings button not found');
    }
    
    // Settings functions
    function loadSettings() {
        console.log('loadSettings function called');
        fetch('/sertifikat/settings')
            .then(response => {
                console.log('Load settings response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Load settings data:', data);
                document.getElementById('nama_penandatangan').value = data.nama_penandatangan || '';
                document.getElementById('nip_penandatangan').value = data.nip_penandatangan || '';
            })
            .catch(error => {
                console.error('Error loading settings:', error);
            });
    }

    function saveSettings() {
        console.log('saveSettings function called');
        
        const namaPenandatangan = document.getElementById('nama_penandatangan').value;
        const nipPenandatangan = document.getElementById('nip_penandatangan').value;
        
        console.log('Values:', { namaPenandatangan, nipPenandatangan });
        
        if (!namaPenandatangan || !nipPenandatangan) {
            Swal.fire({
                title: 'Error!',
                text: 'Nama dan NIP penandatangan harus diisi',
                icon: 'error',
                confirmButtonColor: '#556ee6',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal2-theme-popup',
                    confirmButton: 'swal2-theme-confirm',
                    title: 'swal2-theme-title',
                    htmlContainer: 'swal2-theme-content'
                }
            });
            return;
        }
        
        // Get the form and submit it via AJAX
        const form = document.getElementById('settingsForm');
        const formData = new FormData(form);
        
        console.log('Sending request to /sertifikat/settings');
        
        fetch('/sertifikat/settings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Show success message
                const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
                modal.hide();
                
                // Show success SweetAlert
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#556ee6',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'swal2-theme-popup',
                        confirmButton: 'swal2-theme-success',
                        title: 'swal2-theme-title',
                        htmlContainer: 'swal2-theme-content'
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Terjadi kesalahan saat menyimpan pengaturan',
                    icon: 'error',
                    confirmButtonColor: '#556ee6',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'swal2-theme-popup',
                        confirmButton: 'swal2-theme-confirm',
                        title: 'swal2-theme-title',
                        htmlContainer: 'swal2-theme-content'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error saving settings:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan pengaturan: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#556ee6',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal2-theme-popup',
                    confirmButton: 'swal2-theme-confirm',
                    title: 'swal2-theme-title',
                    htmlContainer: 'swal2-theme-content'
                }
            });
        });
    }
});

// Simple delete function - GLOBAL FUNCTION
function hapusSertifikat(id, nama) {
    console.log('hapusSertifikat called:', id, nama);
    
    // Validate inputs
    if (!id || !nama) {
        console.error('Invalid parameters:', { id, nama });
        alert('Parameter tidak valid');
        return;
    }
    
    // Check if form exists
    const form = document.getElementById('deleteForm');
    if (!form) {
        console.error('Delete form not found');
        alert('Form delete tidak ditemukan');
        return;
    }
    
    // Check if SweetAlert2 is available
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 is not loaded, using fallback');
        if (confirm(`Apakah Anda yakin ingin menghapus sertifikat "${nama}"?`)) {
            console.log('Submitting delete form for ID:', id);
            form.action = `{{ url('sertifikat') }}/${id}`;
            form.submit();
        }
        return;
    }
    
    console.log('Showing SweetAlert2 confirmation...');
    
    // Use SweetAlert2 with proper layout structure
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `
            <div style="text-align: center; padding: 20px 0;">
                <div style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f39c12;"></i>
                </div>
                <div style="font-size: 16px; color: #2c3e50; margin-bottom: 15px; font-weight: 500;">
                    Anda yakin ingin menghapus sertifikat ini?
                </div>
                <div style="font-size: 14px; color: #7f8c8d; background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 4px solid #e74c3c;">
                    <strong>${nama}</strong>
                </div>
                <div style="margin-top: 15px; font-size: 13px; color: #e67e22;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i>
                    Peringatan: Tindakan ini tidak dapat dibatalkan
                </div>
            </div>
        `,
        icon: false,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash"></i> Hapus',
        cancelButtonText: '<i class="fas fa-times"></i> Batal',
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#95a5a6',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        focusConfirm: false,
        focusCancel: false,
        customClass: {
            popup: 'swal2-professional-popup',
            confirmButton: 'swal2-professional-confirm',
            cancelButton: 'swal2-professional-cancel',
            actions: 'swal2-professional-actions'
        }
    }).then((result) => {
        console.log('SweetAlert2 result:', result);
        if (result.isConfirmed) {
            console.log('User confirmed delete for ID:', id);
            
            // Show loading state
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the form
            form.action = `{{ url('sertifikat') }}/${id}`;
            form.submit();
        } else {
            console.log('User cancelled delete');
        }
    }).catch((error) => {
        console.error('SweetAlert2 error:', error);
        // Fallback to regular confirm
        if (confirm(`Apakah Anda yakin ingin menghapus sertifikat "${nama}"?`)) {
            console.log('Fallback: Submitting delete form for ID:', id);
            form.action = `{{ url('sertifikat') }}/${id}`;
            form.submit();
        }
    });
}
</script>

<style>
/* Professional SweetAlert2 Styling */
.swal2-container {
    z-index: 99999 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(0, 0, 0, 0.6) !important;
    backdrop-filter: blur(8px) !important;
}

.swal2-professional-popup {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    border: none !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
    padding: 0 !important;
    overflow: hidden !important;
    max-width: 450px !important;
    position: relative !important;
}

.swal2-professional-popup::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 4px !important;
    background: linear-gradient(90deg, #e74c3c 0%, #f39c12 50%, #3498db 100%) !important;
}

.swal2-professional-popup .swal2-title {
    color: #2c3e50 !important;
    font-weight: 700 !important;
    font-size: 20px !important;
    margin: 0 !important;
    padding: 30px 30px 0 30px !important;
    text-align: center !important;
    letter-spacing: -0.5px !important;
}

.swal2-professional-popup .swal2-html-container {
    padding: 0 30px 30px 30px !important;
    margin: 0 !important;
}

/* Professional Button Styling - Buttons di bawah */
.swal2-professional-actions {
    padding: 0 30px 30px 30px !important;
    margin: 0 !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 15px !important;
}

.swal2-professional-actions .swal2-actions {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 15px !important;
    flex-direction: row !important;
}

.swal2-professional-confirm {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 14px 28px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    min-width: 120px !important;
}

.swal2-professional-cancel {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 14px 28px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(149, 165, 166, 0.4) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    min-width: 120px !important;
}

.swal2-professional-confirm:hover {
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(231, 76, 60, 0.6) !important;
}

.swal2-professional-cancel:hover {
    background: linear-gradient(135deg, #7f8c8d 0%, #6c7b7d 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(149, 165, 166, 0.6) !important;
}

/* Button Icons */
.swal2-professional-confirm i,
.swal2-professional-cancel i {
    margin-right: 8px !important;
    font-size: 12px !important;
}

/* Backdrop */
.swal2-backdrop-show {
    background: rgba(0, 0, 0, 0.6) !important;
    backdrop-filter: blur(8px) !important;
}

.swal2-shown {
    overflow: hidden !important;
}
</style>
@endsection 
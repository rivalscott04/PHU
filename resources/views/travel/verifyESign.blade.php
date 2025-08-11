@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Verifikasi E-Sign BAP</h5>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="text-center mb-4">
                            <i class="bx bx-qr-scan" style="font-size: 3rem; color: #6c757d;"></i>
                            <h6 class="mt-2">Verifikasi Keaslian Dokumen BAP</h6>
                            <p class="text-muted">Scan QR Code atau masukkan token yang ada di dokumen BAP</p>
                        </div>
                        
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-3" id="verificationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="token-tab" data-bs-toggle="tab" data-bs-target="#token-tab-pane" type="button" role="tab">
                                    <i class="bx bx-key me-2"></i>Input Token
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="qr-tab" data-bs-toggle="tab" data-bs-target="#qr-tab-pane" type="button" role="tab">
                                    <i class="bx bx-qr-scan me-2"></i>Scan E-Sign
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="verificationTabContent">
                            <!-- Token Tab -->
                            <div class="tab-pane fade show active" id="token-tab-pane" role="tabpanel">
                                <div class="mb-3">
                                    <label for="tokenInput" class="form-label">Token Verifikasi</label>
                                    <input type="text" id="tokenInput" class="form-control form-control-lg text-center" 
                                        placeholder="Masukkan token 8 karakter" maxlength="8" style="font-size: 1.2rem; letter-spacing: 2px;"
                                        value="{{ $token ?? '' }}">
                                    <small class="text-muted">Contoh: A1B2C3D4</small>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="verifyToken()">
                                        <i class="bx bx-search me-2"></i>Verifikasi Token
                                    </button>
                                </div>
                            </div>
                            
                            <!-- QR Code Tab -->
                            <div class="tab-pane fade" id="qr-tab-pane" role="tabpanel">
                                <div class="text-center">
                                    <div id="qrScanner" style="width: 100%; height: 300px;"></div>
                                    <p class="text-muted mt-2">Arahkan kamera ke E-Sign pada dokumen BAP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk hasil verifikasi -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hasil Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalResultContent"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;
    
    // Auto-verification jika ada parameter URL
    @if(isset($verificationData) && $verificationData['success'])
        document.addEventListener('DOMContentLoaded', function() {
            showVerificationResult(@json($verificationData));
        });
    @endif

    function verifyToken() {
        const token = document.getElementById('tokenInput').value.trim().toUpperCase();
        
        if (!token) {
            Swal.fire('Error', 'Masukkan token terlebih dahulu', 'error');
            return;
        }

        if (token.length !== 8) {
            Swal.fire('Error', 'Token harus 8 karakter', 'error');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memverifikasi Token...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send verification request
        fetch('{{ route("bap.verify-qr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            showVerificationResult(data);
        })
        .catch(error => {
            Swal.fire('Error', 'Terjadi kesalahan saat verifikasi', 'error');
            console.error('Error:', error);
        });
    }

    function verifyQRCode(qrData) {
        // Show loading
        Swal.fire({
            title: 'Memverifikasi QR Code...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send verification request
        fetch('{{ route("bap.verify-qr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_data: qrData })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            showVerificationResult(data);
        })
        .catch(error => {
            Swal.fire('Error', 'Terjadi kesalahan saat verifikasi', 'error');
            console.error('Error:', error);
        });
    }

    function showVerificationResult(result) {
        const contentDiv = document.getElementById('modalResultContent');
        
        let html = `
            <div class="text-center mb-4">
                <i class="bx ${result.success ? 'bx-check-circle text-success' : 'bx-x-circle text-danger'}" style="font-size: 4rem;"></i>
                <h5 class="mt-2 ${result.success ? 'text-success' : 'text-danger'}">${result.message}</h5>
            </div>
        `;

        if (result.success && result.data) {
            html += `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Informasi Dokumen:</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Jenis Dokumen</strong></td><td>: ${result.data.jenis_dokumen || 'Berita Acara Pelaporan (BAP)'}</td></tr>
                            <tr><td><strong>Nomor Surat</strong></td><td>: ${result.data.nomor_surat}</td></tr>
                            <tr><td><strong>Nama Travel</strong></td><td>: ${result.data.nama_travel}</td></tr>
                            <tr><td><strong>Status Dokumen</strong></td><td>: ${result.data.status_dokumen}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Informasi Petugas:</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nama Petugas</strong></td><td>: ${result.data.nama_petugas}</td></tr>
                            <tr><td><strong>Jabatan</strong></td><td>: ${result.data.jabatan_petugas}</td></tr>
                            <tr><td><strong>Tanggal Dibuat</strong></td><td>: ${result.data.tanggal_dibuat}</td></tr>
                            <tr><td><strong>Token</strong></td><td>: <code class="bg-light px-2 py-1">${result.data.token}</code></td></tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="badge bg-${result.hash_valid ? 'success' : 'danger'} fs-6">
                                <i class="bx ${result.hash_valid ? 'bx-check' : 'bx-x'} me-1"></i>
                                ${result.hash_valid ? 'Tanda Tangan Valid' : 'Tanda Tangan Tidak Valid'}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <span class="badge bg-${result.dokumen_valid ? 'success' : 'warning'} fs-6">
                                <i class="bx ${result.dokumen_valid ? 'bx-check' : 'bx-error'} me-1"></i>
                                ${result.dokumen_valid ? 'Dokumen Aktif' : 'Dokumen Tidak Aktif'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="text-center">
                    <p class="text-muted">Token tidak valid atau dokumen tidak ditemukan dalam database.</p>
                </div>
            `;
        }
        
        contentDiv.innerHTML = html;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('resultModal'));
        modal.show();
    }

    // Initialize QR Scanner when QR tab is shown
    document.getElementById('qr-tab').addEventListener('shown.bs.tab', function (e) {
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qrScanner",
                { fps: 10, qrbox: { width: 250, height: 250 } },
                false
            );
            
            html5QrcodeScanner.render((decodedText) => {
                // QR Code detected
                html5QrcodeScanner.clear();
                verifyQRCode(decodedText);
            }, (error) => {
                // Handle scan error
            });
        }
    });

    // Clean up scanner when tab is hidden
    document.getElementById('token-tab').addEventListener('shown.bs.tab', function (e) {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
        }
    });

    // Enter key to submit
    document.getElementById('tokenInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            verifyToken();
        }
    });
</script>
@endpush

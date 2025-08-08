@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Verifikasi Tanda Tangan Digital BAP</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="qrInput" class="form-label">Scan QR Code atau Input Data</label>
                            <textarea id="qrInput" class="form-control" rows="4" 
                                placeholder="Paste data QR Code di sini atau scan menggunakan kamera"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="verifyQR()">
                            <i class="bx bx-qr-scan me-2"></i>Verifikasi QR Code
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="scanQR()">
                            <i class="bx bx-camera me-2"></i>Scan QR Code
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div id="verificationResult" style="display: none;">
                            <h6>Hasil Verifikasi:</h6>
                            <div id="resultContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk scan QR Code -->
<div class="modal fade" id="qrScannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qrScanner" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;

    function verifyQR() {
        const qrData = document.getElementById('qrInput').value.trim();
        
        if (!qrData) {
            Swal.fire('Error', 'Masukkan data QR Code terlebih dahulu', 'error');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memverifikasi...',
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
        const resultDiv = document.getElementById('verificationResult');
        const contentDiv = document.getElementById('resultContent');
        
        let html = `
            <div class="alert alert-${result.success ? 'success' : 'danger'}">
                <strong>${result.message}</strong>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6>Detail Tanda Tangan Digital:</h6>
                    <table class="table table-sm">
                        <tr><td>Nomor Surat</td><td>: ${result.data.nomor_surat}</td></tr>
                        <tr><td>Nama Petugas</td><td>: ${result.data.nama_petugas}</td></tr>
                        <tr><td>NIP Petugas</td><td>: ${result.data.nip_petugas}</td></tr>
                        <tr><td>Jabatan</td><td>: ${result.data.jabatan_petugas}</td></tr>
                        <tr><td>Tanggal Tanda Tangan</td><td>: ${result.data.tanggal_tanda_tangan}</td></tr>
                        <tr><td>Status Dokumen</td><td>: ${result.data.status_dokumen}</td></tr>
                        <tr><td>Verifikasi Digital</td><td>: ${result.data.verifikasi_digital ? 'Ya' : 'Tidak'}</td></tr>
                    </table>
                    ${result.hash_valid !== undefined ? `
                        <div class="mt-3">
                            <strong>Status Verifikasi:</strong><br>
                            <span class="badge bg-${result.hash_valid ? 'success' : 'danger'}">
                                ${result.hash_valid ? '✓ Tanda Tangan Valid' : '✗ Tanda Tangan Tidak Valid'}
                            </span><br>
                            <span class="badge bg-${result.dokumen_valid ? 'success' : 'warning'}">
                                ${result.dokumen_valid ? '✓ Dokumen Aktif' : '⚠ Dokumen Tidak Aktif'}
                            </span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        contentDiv.innerHTML = html;
        resultDiv.style.display = 'block';
    }

    function scanQR() {
        const modal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
        modal.show();
        
        // Initialize QR Scanner
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qrScanner",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );
        
        html5QrcodeScanner.render((decodedText) => {
            // QR Code detected
            document.getElementById('qrInput').value = decodedText;
            html5QrcodeScanner.clear();
            modal.hide();
            verifyQR();
        }, (error) => {
            // Handle scan error
        });
    }

    // Clean up scanner when modal is closed
    document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', function () {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
        }
    });
</script>
@endpush

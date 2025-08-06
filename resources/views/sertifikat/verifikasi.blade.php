<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Verifikasi Sertifikat PPIU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Sistem Verifikasi Sertifikat PPIU" name="description" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css -->
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="bx bx-certificate text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="card-title mb-1">Verifikasi Sertifikat PPIU</h4>
                                            <p class="text-muted mb-0">Detail informasi sertifikat</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $sertifikat->getStatusColor() }} fs-6">
                                                {{ $sertifikat->getStatusText() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Status Verifikasi -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            @if($sertifikat->isActive())
                                                <div class="alert alert-success border-0" role="alert">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="bx bx-check-circle text-success" style="font-size: 2rem;"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="alert-heading mb-1">Sertifikat Terverifikasi ASLI</h5>
                                                            <p class="mb-0">Sertifikat ini telah diverifikasi dan terdaftar dalam sistem resmi. Status: <strong>{{ $sertifikat->getStatusText() }}</strong></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($sertifikat->status === 'revoked')
                                                <div class="alert alert-danger border-0" role="alert">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="bx bx-x-circle text-danger" style="font-size: 2rem;"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="alert-heading mb-1">❌ Sertifikat Telah Dicabut</h5>
                                                            <p class="mb-0">Sertifikat ini telah dicabut dan tidak berlaku lagi. Status: <strong>{{ $sertifikat->getStatusText() }}</strong></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning border-0" role="alert">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="bx bx-time text-warning" style="font-size: 2rem;"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="alert-heading mb-1">⚠️ Sertifikat Kadaluarsa</h5>
                                                            <p class="mb-0">Sertifikat ini telah kadaluarsa dan tidak berlaku lagi. Status: <strong>{{ $sertifikat->getStatusText() }}</strong></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Checklist Verifikasi -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="mb-3"><i class="bx bx-list-check text-primary"></i> Checklist Verifikasi:</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-check text-success me-2"></i>
                                                        <span>UUID terdaftar dalam database</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-check text-success me-2"></i>
                                                        <span>Nomor surat valid</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-check text-success me-2"></i>
                                                        <span>Format sertifikat sesuai standar</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-check text-success me-2"></i>
                                                        <span>Data perusahaan terverifikasi</span>
                                                    </div>
                                                    @if($sertifikat->isActive())
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="bx bx-check text-success me-2"></i>
                                                            <span>Status sertifikat aktif</span>
                                                        </div>
                                                    @else
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="bx bx-x text-danger me-2"></i>
                                                            <span class="text-danger">Status sertifikat tidak aktif</span>
                                                        </div>
                                                    @endif
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bx bx-check text-success me-2"></i>
                                                        <span>Diverifikasi pada: {{ now()->format('d F Y H:i:s') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informasi Tambahan untuk Sertifikat Tidak Aktif -->
                                    @if(!$sertifikat->isActive())
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="alert alert-info border-0" role="alert">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="bx bx-info-circle text-info"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="alert-heading mb-1">Informasi Penting:</h6>
                                                            @if($sertifikat->status === 'revoked')
                                                                <p class="mb-0">Sertifikat ini telah dicabut oleh pihak berwenang. Untuk informasi lebih lanjut, silakan hubungi kantor kami.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Tombol Toggle Detail -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detailSertifikat" aria-expanded="false" aria-controls="detailSertifikat">
                                                <i class="bx bx-show"></i> Lihat Detail Sertifikat
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Detail Sertifikat (Collapsible) -->
                                    <div class="collapse" id="detailSertifikat">
                                        <div class="card card-body bg-light">
                                            <h6 class="mb-3"><i class="bx bx-info-circle text-info"></i> Detail Sertifikat:</h6>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nomor Surat</label>
                                                        <p class="form-control-plaintext">{{ $sertifikat->nomor_surat }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nomor Dokumen</label>
                                                        <p class="form-control-plaintext">{{ $sertifikat->nomor_dokumen }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nama PPIU</label>
                                                        <p class="form-control-plaintext">{{ $sertifikat->nama_ppiu }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Kepala Cabang</label>
                                                        <p class="form-control-plaintext">{{ $sertifikat->nama_kepala }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Alamat Kantor</label>
                                                <p class="form-control-plaintext">{{ $sertifikat->alamat }}</p>
                                            </div>

                                                                                <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Jenis</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge bg-info">{{ $sertifikat->jenis }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Lokasi</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge bg-{{ $sertifikat->jenis_lokasi == 'pusat' ? 'primary' : 'warning' }}">
                                                        {{ ucfirst($sertifikat->jenis_lokasi) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                            @if($sertifikat->travel)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Travel Company</label>
                                                        <p class="form-control-plaintext">{{ $sertifikat->travel->Penyelenggara }}</p>
                                                    </div>
                                                </div>
                                                @if($sertifikat->cabang)
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Cabang</label>
                                                        <p class="form-control-plaintext">{{ $sertifikat->cabang->kabupaten }}</p>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            @endif

                                            

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">UUID Verifikasi</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" value="{{ $sertifikat->uuid }}" readonly>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                                        <i class="bx bx-copy"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">UUID ini dapat digunakan untuk verifikasi keaslian sertifikat</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted">
                                            <small>
                                                <i class="bx bx-shield-check text-success"></i>
                                                Diverifikasi pada: {{ now()->format('d F Y H:i:s') }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('sertifikat.index') }}" class="btn btn-secondary">
                                                <i class="bx bx-arrow-back"></i>
                                                Kembali ke Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>

    <script>
        function copyToClipboard() {
            const uuidInput = document.querySelector('input[readonly]');
            uuidInput.select();
            uuidInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            // Show feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bx bx-check"></i>';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        }

        // Add smooth animation to toggle button
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');
            const detailSection = document.getElementById('detailSertifikat');
            
            toggleButton.addEventListener('click', function() {
                const isExpanded = detailSection.classList.contains('show');
                
                if (isExpanded) {
                    toggleButton.innerHTML = '<i class="bx bx-show"></i> Lihat Detail Sertifikat';
                } else {
                    toggleButton.innerHTML = '<i class="bx bx-hide"></i> Sembunyikan Detail';
                }
            });
        });
    </script>
</body>
</html> 
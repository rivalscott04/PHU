@extends('layouts.app')

@section('content')
    @php
        $role = auth()->user()->role;
        $isApprover = in_array($role, ['admin', 'kabupaten'], true);
        $isTravel = $role === 'user';
        $meta = \App\Support\BapWizardStatus::detailMeta($data);
        $statusOptions = $isApprover ? \App\Support\BapWizardStatus::approverStatusOptions($data->status) : [];
    @endphp
    <div class="row">
        <div class="col-12">
            @include('partials.bap-module-info', ['variant' => 'pemberangkatan'])
            @if ($guide = \App\Support\RoleWorkflowGuide::for('bap_detail', ['status' => $data->status ?? '']))
                @include('partials.workflow-guide', ['guide' => $guide])
            @endif
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h5 class="mb-0">
                                {{ $isTravel ? 'Status Pengajuan' : 'Detail BA Pemberangkatan' }}
                            </h5>
                            <span class="badge {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                        </div>
                        @if ($meta['hint'])
                            <small class="text-muted d-block">{{ $meta['hint'] }}</small>
                        @endif
                        @if ($data->status === 'diterima' && $data->nomor_surat)
                            <small class="text-muted d-block mt-1">Nomor surat: <strong>{{ $data->nomor_surat }}</strong></small>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('bap') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Kembali ke daftar
                        </a>
                        @if ($data->status === 'diterima')
                            <a href="{{ route('cetak.bap', $data->id) }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="bx bx-printer me-1"></i>Cetak BAP
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($isTravel && in_array($data->status, ['diajukan', 'diproses'], true))
                        <div class="alert alert-light border mb-4">
                            <i class="bx bx-time-five me-1 text-primary"></i>
                            Pengajuan Anda sedang diproses Kabupaten/Kanwil. Tidak perlu mengajukan ulang;
                            pantau perubahan status di halaman ini atau di daftar BA Pemberangkatan.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            @include('travel.partials.bap-detail-summary', [
                                'data' => $data,
                                'jamaahMaxHeight' => '220px',
                            ])
                        </div>
                        <div class="col-lg-6 mb-4">
                            <h6 class="text-muted text-uppercase small mb-3">Surat pernyataan</h6>
                            @if ($data->pdf_file_path)
                                <iframe src="{{ asset('storage/' . $data->pdf_file_path) }}" width="100%"
                                    height="480px" class="border rounded"></iframe>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="bx bx-error-circle me-1"></i>
                                    PDF surat pernyataan belum diunggah.
                                    @if ($isApprover && $data->status === 'pending')
                                        Minta travel melengkapi melalui wizard pengajuan.
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($isApprover)
                        <hr>
                        <div class="border rounded p-3 bg-light">
                            <h6 class="mb-2"><i class="bx bx-task me-1 text-primary"></i>Tindakan persetujuan</h6>
                            <p class="text-muted small mb-3">
                                Periksa data keberangkatan dan PDF, lalu ubah status:
                                <strong>Diajukan → Diproses → Diterima</strong>.
                            </p>

                            @if (! $data->pdf_file_path && $data->status !== 'pending')
                                <div class="alert alert-warning small py-2 mb-3">
                                    PDF belum ada. Pastikan kelengkapan dokumen sebelum menyetujui.
                                </div>
                            @endif

                            <form action="{{ route('bap.updateStatus', $data->id) }}" method="POST" id="statusFormDetail">
                                @csrf
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label for="statusSelect" class="form-label small mb-1">Ubah status</label>
                                        <select name="status" id="statusSelect"
                                            class="form-select {{ $data->status == 'diajukan' ? 'border-primary' : '' }}
                                                {{ $data->status == 'diproses' ? 'border-warning' : '' }}
                                                {{ $data->status == 'diterima' ? 'border-success' : '' }}">
                                            @foreach ($statusOptions as $status)
                                                <option value="{{ $status }}" {{ $data->status === $status ? 'selected' : '' }}>
                                                    {{ \App\Support\BapWizardStatus::approverStatusLabel($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-auto">
                                        <button type="button" class="btn btn-primary" id="btnApplyStatus">
                                            <i class="bx bx-check me-1"></i>Simpan status
                                        </button>
                                    </div>
                                    @if ($isApprover && ! $data->pdf_file_path)
                                        <div class="col-md-auto">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                                data-bs-target="#uploadPDFModal">
                                                <i class="bx bx-upload me-1"></i>Upload PDF
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($isApprover && ! $data->pdf_file_path)
        <div id="uploadPDFModal" class="modal fade" tabindex="-1" aria-labelledby="uploadPDFModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadPDFModalLabel">Upload Surat Pernyataan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('bap.upload', ['id' => $data->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="pdf_file" class="form-label">File PDF (maks. 500 KB)</label>
                                <input type="file" class="form-control" id="pdf_file" name="pdf_file"
                                    accept="application/pdf" required>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Unggah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btnApplyStatus')?.addEventListener('click', function () {
            const select = document.getElementById('statusSelect');
            const form = document.getElementById('statusFormDetail');
            const status = select.value;
            const labels = {
                pending: 'Draft',
                diajukan: 'Diajukan',
                diproses: 'Diproses',
                diterima: 'Diterima',
            };

            Swal.fire({
                title: 'Ubah status pengajuan?',
                text: `Status akan diubah menjadi "${labels[status] || status}".`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#556ee6',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush

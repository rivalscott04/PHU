@php
    use App\Enums\InspectionType;
    $inspectionStatus = $inspection->status?->value ?? $inspection->status;
    $statusLabel = $inspection->status?->label() ?? \App\Enums\InspectionStatus::labelFor($inspection->status);
    $statusColor = $inspection->status?->badgeColor() ?? \App\Enums\InspectionStatus::badgeFor($inspection->status);
    $typeLabel = $inspection->inspection_type?->label() ?? InspectionType::tryFrom((string) $inspection->inspection_type)?->label() ?? '-';
@endphp

<div class="pengawasan-wizard-panel" data-pengawasan-step="1">
    <div class="alert alert-light border mb-4">
        <div class="d-flex gap-2">
            <i class="bx bx-info-circle fs-5 text-primary flex-shrink-0 mt-1"></i>
            <div class="small mb-0">
                <strong>Pemeriksaan travel</strong> untuk mencatat kondisi PPIU/PIHK di lapangan.
                Isi pertanyaan pemeriksaan, lalu catat masalah jika ada.
                Modul ini berbeda dari persetujuan keberangkatan jamaah.
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Ringkasan Kunjungan</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Nomor Pemeriksaan</div>
                    <div class="fw-medium">{{ $inspection->inspection_no }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Travel</div>
                    <div class="fw-medium">{{ $inspection->travel?->Penyelenggara ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Tanggal</div>
                    <div class="fw-medium">{{ optional($inspection->inspection_date)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Jenis Pemeriksaan</div>
                    <div class="fw-medium">{{ $typeLabel }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Status</div>
                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Skor Kepatuhan</div>
                    <div class="fw-medium">{{ $inspection->overall_score !== null ? number_format($inspection->overall_score, 0).'%' : 'Belum dihitung' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Catatan</div>
                    <div>{{ $inspection->notes ?: '-' }}</div>
                </div>
            </div>

            @can('update', $inspection)
                @if (! in_array($inspectionStatus, ['CLOSED', 'CANCELLED'], true))
                    <div class="mt-3">
                        <a href="{{ route('v2.pengawasan.edit', $inspection) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bx bx-edit me-1"></i> Ubah Jadwal
                        </a>
                    </div>
                @endif
            @endcan
        </div>
    </div>

    <div class="pengawasan-wizard-actions">
        <a href="{{ route('v2.pengawasan.index') }}" class="btn btn-light">Kembali ke Daftar</a>
        <button type="button" class="btn btn-primary" data-pengawasan-goto="2">
            Lanjut Isi Pemeriksaan <i class="bx bx-right-arrow-alt ms-1"></i>
        </button>
    </div>
</div>

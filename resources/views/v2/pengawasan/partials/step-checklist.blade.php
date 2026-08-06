@php
    $filled = $inspection->checklists->filter(fn ($item) => filled($item->answer))->count();
    $total = $inspection->checklists->count();
@endphp

<div class="pengawasan-wizard-panel" data-pengawasan-step="2">
    <div class="alert alert-info border-0 mb-4">
        <h6 class="alert-heading mb-1"><i class="bx bx-list-check me-1"></i> Pertanyaan Pemeriksaan</h6>
        <p class="mb-0 small">Jawab sesuai kondisi travel saat kunjungan. Skor kepatuhan dihitung otomatis setelah disimpan.</p>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Checklist Lapangan</h5>
            @if ($total > 0)
                <span class="badge bg-{{ $filled === $total ? 'success' : 'secondary' }}">{{ $filled }} dari {{ $total }} terisi</span>
            @endif
        </div>
        <div class="card-body">
            @if ($canFillChecklist)
                @include('v2.pengawasan.partials.checklist-form', [
                    'submitLabel' => 'Simpan & Lanjut',
                    'showBackButton' => true,
                ])
            @else
                @include('v2.pengawasan.partials.checklist-form', [
                    'readOnly' => true,
                ])
                <div class="pengawasan-wizard-actions mt-0 border-0 pt-0">
                    <button type="button" class="btn btn-light" data-pengawasan-goto="1">
                        <i class="bx bx-left-arrow-alt me-1"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary" data-pengawasan-goto="3">
                        Lanjut ke Temuan <i class="bx bx-right-arrow-alt ms-1"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@props([
    'modalId',
    'title',
    'exportRoute',
    'groupedData' => null,
    'orgLabel' => 'PPIU',
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0 text-white"><i class="bx bx-globe me-2"></i>Unduh Semua</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Unduh seluruh data jamaah dari semua {{ $orgLabel }} dalam satu file.</p>
                                <div class="d-grid gap-2">
                                    <a href="{{ route($exportRoute, ['format' => 'excel', 'type' => 'global']) }}" class="btn btn-outline-primary" target="_blank">
                                        <i class="bx bx-file me-2"></i> Excel
                                    </a>
                                    <a href="{{ route($exportRoute, ['format' => 'pdf', 'type' => 'global']) }}" class="btn btn-outline-success" target="_blank">
                                        <i class="bx bx-file-pdf me-2"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0 text-white"><i class="bx bx-building me-2"></i>Unduh per {{ $orgLabel }}</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Unduh data jamaah dari {{ $orgLabel }} tertentu saja.</p>
                                <div class="mb-3">
                                    <label for="{{ $modalId }}TravelSelect" class="form-label">Pilih {{ $orgLabel }}</label>
                                    <select class="form-select jamaah-export-travel-select" id="{{ $modalId }}TravelSelect" data-modal="{{ $modalId }}">
                                        <option value="">Pilih {{ $orgLabel }}...</option>
                                        @if($groupedData)
                                            @foreach($groupedData as $travelId => $jamaahGroup)
                                                @php
                                                    $travel = $jamaahGroup->first()->travel;
                                                    $totalJamaah = $jamaahGroup->count();
                                                @endphp
                                                <option value="{{ $travelId }}">
                                                    {{ $travel->Penyelenggara ?? $orgLabel.' Tidak Diketahui' }}
                                                    ({{ $totalJamaah }} Jamaah)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-success jamaah-export-travel-excel" data-modal="{{ $modalId }}" data-route="{{ route($exportRoute) }}" disabled>
                                        <i class="bx bx-file me-2"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-outline-info jamaah-export-travel-pdf" data-modal="{{ $modalId }}" data-route="{{ route($exportRoute) }}" disabled>
                                        <i class="bx bx-file-pdf me-2"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="bx bx-info-circle me-2"></i>
                    Excel berformat .xlsx. PDF memuat kop surat resmi Kementerian Agama.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@once
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.jamaah-export-travel-select').forEach(function (select) {
                    const modalId = select.dataset.modal;
                    const excelBtn = document.querySelector('.jamaah-export-travel-excel[data-modal="' + modalId + '"]');
                    const pdfBtn = document.querySelector('.jamaah-export-travel-pdf[data-modal="' + modalId + '"]');

                    select.addEventListener('change', function () {
                        const enabled = this.value !== '';
                        if (excelBtn) excelBtn.disabled = !enabled;
                        if (pdfBtn) pdfBtn.disabled = !enabled;
                    });
                });

                document.querySelectorAll('.jamaah-export-travel-excel, .jamaah-export-travel-pdf').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const modalId = this.dataset.modal;
                        const select = document.getElementById(modalId + 'TravelSelect');
                        const travelId = select ? select.value : '';

                        if (!travelId) {
                            Swal.fire({
                                title: 'Perhatian',
                                text: 'Silakan pilih PPIU terlebih dahulu.',
                                icon: 'warning',
                                confirmButtonColor: '#556ee6',
                            });
                            return;
                        }

                        const format = this.classList.contains('jamaah-export-travel-pdf') ? 'pdf' : 'excel';
                        const url = this.dataset.route + '?format=' + format + '&type=travel&travel_id=' + travelId;
                        window.open(url, '_blank');
                    });
                });
            });
        </script>
    @endpush
@endonce

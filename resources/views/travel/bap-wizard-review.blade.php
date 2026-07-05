@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            @include('partials.bap-module-info', ['variant' => 'pemberangkatan'])
            <div class="card">
                <div class="card-header ps-0">
                    <h5 class="mb-0">Review & Ajukan</h5>
                    <small class="text-muted d-block">Langkah 3 dari 3 — periksa kembali data sebelum mengirim ke Kabupaten/Kanwil.</small>
                </div>
                <div class="card-body">
                    @include('travel.partials.bap-wizard-progress', ['currentStep' => 3])

                    <div class="mb-3">
                        <a href="{{ route('form.bap.edit', $data->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-edit me-1"></i>Ubah data keberangkatan
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            @include('travel.partials.bap-detail-summary', [
                                'data' => $data,
                                'jamaahMaxHeight' => '200px',
                            ])
                        </div>
                        <div class="col-lg-6 mb-4">
                            <h6 class="text-muted text-uppercase small mb-3">Surat pernyataan</h6>
                            @if ($data->pdf_file_path)
                                <iframe src="{{ asset('storage/' . $data->pdf_file_path) }}" width="100%"
                                    height="420px" class="border rounded"></iframe>
                            @else
                                <div class="alert alert-warning">
                                    PDF belum diunggah.
                                    <a href="{{ route('bap.wizard.upload', $data->id) }}">Kembali ke langkah upload</a>.
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex flex-wrap gap-2">
                        @if ($data->pdf_file_path)
                            <form action="{{ route('bap.ajukan', ['id' => $data->id]) }}" method="POST" id="ajukanForm">
                                @csrf
                                <input type="hidden" name="wizard" value="1">
                                <button type="button" class="btn btn-primary" id="btnAjukan">
                                    <i class="bx bx-send me-1"></i>Ajukan ke Kabupaten/Kanwil
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('bap.wizard.upload', $data->id) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Kembali ke Upload
                        </a>
                        <a href="{{ route('bap') }}" class="btn btn-link text-muted">Kembali ke daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btnAjukan')?.addEventListener('click', function () {
            Swal.fire({
                title: 'Ajukan BA Pemberangkatan?',
                text: 'Pastikan data dan surat pernyataan sudah benar. Setelah diajukan, data akan ditinjau Kabupaten/Kanwil.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Periksa lagi',
                confirmButtonColor: '#556ee6',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('ajukanForm').submit();
                }
            });
        });
    </script>
@endpush

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            @include('partials.bap-module-info', ['variant' => 'pemberangkatan'])
            <div class="card">
                <div class="card-header ps-0">
                    <h5 class="mb-0">Upload Surat Pernyataan</h5>
                    <small class="text-muted d-block">Langkah 2 dari 3 — lampirkan surat pernyataan dalam format PDF.</small>
                </div>
                <div class="card-body">
                    @include('travel.partials.bap-wizard-progress', ['currentStep' => 2])

                    <div class="alert alert-light border mb-4">
                        <div class="d-flex gap-2">
                            <i class="bx bx-info-circle text-primary fs-5 flex-shrink-0"></i>
                            <div class="small mb-0">
                                <strong>Petunjuk:</strong> Unggah surat pernyataan keberangkatan jamaah yang sudah ditandatangani.
                                Format file: <strong>PDF</strong>, ukuran maksimal <strong>500 KB</strong>.
                                <a href="{{ route('bap.template.surat-pernyataan') }}" class="alert-link" target="_blank">
                                    Unduh template surat pernyataan
                                </a>.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-5 mb-4">
                            <h6 class="text-muted text-uppercase small mb-3">Ringkasan data keberangkatan</h6>
                            <div class="mb-2">
                                <a href="{{ route('form.bap.edit', $data->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-edit me-1"></i>Ubah data keberangkatan
                                </a>
                            </div>
                            <dl class="row mb-0 small">
                                <dt class="col-sm-5">PPIU</dt>
                                <dd class="col-sm-7">{{ $data->ppiuname }}</dd>
                                <dt class="col-sm-5">Tanggal Berangkat</dt>
                                <dd class="col-sm-7">{{ \Carbon\Carbon::parse($data->datetime)->translatedFormat('d M Y') }}</dd>
                                <dt class="col-sm-5">Tanggal Pulang</dt>
                                <dd class="col-sm-7">{{ \Carbon\Carbon::parse($data->returndate)->translatedFormat('d M Y') }}</dd>
                                <dt class="col-sm-5">Jumlah Jamaah</dt>
                                <dd class="col-sm-7">{{ $data->people }} orang</dd>
                                <dt class="col-sm-5">Durasi</dt>
                                <dd class="col-sm-7">{{ $data->days }} hari</dd>
                                <dt class="col-sm-5">Maskapai</dt>
                                <dd class="col-sm-7">{{ $data->airlines }} / {{ $data->airlines2 }}</dd>
                            </dl>
                            @include('travel.partials.bap-jamaah-list', ['jamaah' => $data->jamaah, 'maxHeight' => '120px'])
                        </div>
                        <div class="col-lg-7">
                            @if ($data->pdf_file_path)
                                <div class="alert alert-success small mb-3">
                                    <i class="bx bx-check-circle me-1"></i>
                                    PDF sudah diunggah. Anda dapat melanjutkan ke langkah review atau mengganti file.
                                </div>
                                <iframe src="{{ asset('storage/' . $data->pdf_file_path) }}" width="100%"
                                    height="280px" class="border rounded mb-3"></iframe>
                            @endif

                            <form method="POST" action="{{ route('bap.upload', ['id' => $data->id]) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="wizard" value="1">
                                <div class="mb-3">
                                    <label for="pdf_file" class="form-label">
                                        {{ $data->pdf_file_path ? 'Ganti file PDF' : 'Pilih file PDF' }}
                                    </label>
                                    <input type="file" class="form-control @error('pdf_file') is-invalid @enderror"
                                        id="pdf_file" name="pdf_file" accept="application/pdf"
                                        {{ $data->pdf_file_path ? '' : 'required' }}>
                                    @error('pdf_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-upload me-1"></i>
                                        {{ $data->pdf_file_path ? 'Perbarui & Lanjutkan' : 'Unggah & Lanjutkan' }}
                                    </button>
                                    @if ($data->pdf_file_path)
                                        <a href="{{ route('bap.wizard.review', $data->id) }}" class="btn btn-outline-primary">
                                            Lanjut ke Review
                                        </a>
                                    @endif
                                    <a href="{{ route('bap') }}" class="btn btn-outline-secondary">Simpan dulu, lanjut nanti</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

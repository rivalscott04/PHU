@extends('layouts.app')

@section('content')
    @if($guide = \App\Support\RoleWorkflowGuide::for('jamaah_umrah'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Jamaah Umrah</h4>
                    <p class="text-muted mb-0 small">Kelola data jamaah umrah di wilayah Anda</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('jamaah.umrah.create') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah
                    </a>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bx bx-upload me-1"></i> Upload Excel
                    </button>
                    @if(in_array(auth()->user()->role, ['user', 'kabupaten'], true))
                        @include('partials.export-dropdown', [
                            'excelUrl' => route('jamaah.umrah.export', ['format' => 'excel']),
                            'pdfUrl' => route('jamaah.umrah.export', ['format' => 'pdf']),
                        ])
                    @else
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bx bx-export me-1"></i> Unduh Data
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Jamaah Umrah</h5>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @include('jamaah.partials.listing', [
                        'listingRoute' => $listingRoute,
                        'jamaah' => $jamaah,
                        'showTravelColumn' => $showTravelColumn,
                    ])
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data Jamaah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jamaah.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="jenis_jamaah" value="umrah">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Format yang didukung:</strong> .xlsx, .xls<br>
                            <strong>Download template:</strong>
                            <a href="{{ route('jamaah.template') }}" class="alert-link">Template Excel</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        @include('partials.jamaah-export-modal', [
            'modalId' => 'exportModal',
            'title' => 'Unduh Data Jamaah Umrah',
            'exportRoute' => 'jamaah.umrah.export',
            'exportTravels' => $exportTravels,
            'orgLabel' => 'PPIU',
        ])
    @endif
@endsection

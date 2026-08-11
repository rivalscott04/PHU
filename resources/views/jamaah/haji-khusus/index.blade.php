@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Jamaah Haji Khusus</h4>
                <p class="text-muted mb-0 small">Kelola data jamaah haji khusus di wilayah Anda</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('jamaah.haji-khusus.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Jamaah
                </a>
                @include('partials.export-dropdown', [
                    'excelUrl' => route('jamaah.haji-khusus.export', ['format' => 'excel']),
                    'pdfUrl' => route('jamaah.haji-khusus.export', ['format' => 'pdf']),
                    'buttonClass' => 'btn-success',
                    'followsFilter' => true,
                ])
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Jamaah Haji Khusus</h5>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                @include('jamaah.haji-khusus.partials.listing', [
                    'jamaahHajiKhusus' => $jamaahHajiKhusus,
                    'showTravelColumn' => $showTravelColumn,
                    'travelOptions' => $travelOptions,
                ])
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
@if(in_array(auth()->user()->role, ['admin', 'kabupaten'], true))
    @include('jamaah.haji-khusus.partials.spph-assign-scripts')
@endif
@endpush

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('v2.monitoring.index') }}">Monitoring</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Travel</li>
                    </ol>
                </nav>
                <h4 class="mb-1 fw-semibold">Data Travel</h4>
                <p class="text-muted mb-0">Daftar lengkap travel beserta aktivitas pengawasan dan risiko</p>
            </div>
            <a href="{{ route('v2.monitoring.index', request()->query()) }}" class="btn btn-sm btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_monitoring_travel'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    @include('v2.partials.wilayah-scope')
    @include('v2.monitoring.partials.filters')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Semua Travel</h5>
            <span class="badge bg-light text-dark border">{{ $travels->total() }} travel</span>
        </div>
        <div class="card-body p-0">
            @include('v2.monitoring.partials.travel-table', ['travels' => $travels])
            @if($travels->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $travels->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@include('v2.partials.pengaduan-offcanvas')
@endsection

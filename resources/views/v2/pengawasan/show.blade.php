@extends('layouts.app')

@push('styles')
    @include('v2.pengawasan.partials.wizard-styles')
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h4 class="mb-1">Pemeriksaan: {{ $inspection->inspection_no }}</h4>
                <p class="text-muted mb-0">{{ $inspection->travel?->Penyelenggara }}</p>
            </div>
            <a href="{{ route('v2.pengawasan.index') }}" class="btn btn-sm btn-secondary">Kembali ke Daftar</a>
        </div>
    </div>

    @include('v2.pengawasan.partials.wizard-progress')

    <div id="pengawasan-wizard">
        @include('v2.pengawasan.partials.step-info')
        @include('v2.pengawasan.partials.step-checklist')
        @include('v2.pengawasan.partials.step-temuan')
    </div>
@endsection

@push('js')
    @include('v2.pengawasan.partials.wizard-scripts')
@endpush

@extends('layouts.app')

@section('content')
@php
    use App\Enums\InspectionStatus;
    use App\Enums\InspectionType;
@endphp
<div class="container-fluid">
    @include('partials.bap-module-info', ['variant' => 'pemeriksaan'])
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">{{ $inspection ? 'Edit BA Pemeriksaan' : 'Buat BA Pemeriksaan' }}</h4>
                <small class="text-muted">Catat jadwal dan hasil pemeriksaan pengawasan PPIU</small>
            </div>
            <a href="{{ route('v2.pengawasan.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>
            <form method="POST" action="{{ $inspection ? route('v2.pengawasan.update', $inspection) : route('v2.pengawasan.store') }}">
                @csrf
                @if ($inspection) @method('PUT') @endif
                <div class="row">
                    @if ($inspection)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Pengawasan</label>
                            <input type="text" class="form-control" value="{{ $inspection->inspection_no }}" readonly disabled>
                        </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal @include('partials.required-star')</label>
                        <input type="date" name="inspection_date" class="form-control" value="{{ old('inspection_date', optional($inspection?->inspection_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Travel @include('partials.required-star')</label>
                        <select name="travel_id" class="form-select" required>
                            <option value="">Pilih Travel</option>
                            @foreach ($travels as $travel)
                                <option value="{{ $travel->id }}" @selected(old('travel_id', $inspection->travel_id ?? $preselectedTravelId ?? '') == $travel->id)>{{ $travel->Penyelenggara }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe @include('partials.required-star')</label>
                        <select name="inspection_type" class="form-select" required>
                            @foreach (InspectionType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('inspection_type', $inspection->inspection_type?->value ?? 'ROUTINE') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $inspection->notes ?? '') }}</textarea>
                    </div>
                    @if ($inspection && in_array(auth()->user()->role, ['admin', 'pengawas'], true))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach (InspectionStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $inspection->status?->value ?? $inspection->status) === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

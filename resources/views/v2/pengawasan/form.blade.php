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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <input type="date" name="inspection_date" class="form-control @error('inspection_date') is-invalid @enderror"
                            value="{{ old('inspection_date', optional($inspection?->inspection_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                        @error('inspection_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Travel @include('partials.required-star')</label>
                        <select name="travel_id" class="form-select @error('travel_id') is-invalid @enderror" required>
                            <option value="">Pilih Travel</option>
                            @foreach ($travels as $travel)
                                <option value="{{ $travel->id }}" @selected(old('travel_id', $inspection?->travel_id ?? $preselectedTravelId ?? '') == $travel->id)>{{ $travel->Penyelenggara }}</option>
                            @endforeach
                        </select>
                        @error('travel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($travels->isEmpty())
                            <div class="form-text text-danger">Tidak ada travel dalam wilayah akses Anda.</div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe @include('partials.required-star')</label>
                        <select name="inspection_type" class="form-select @error('inspection_type') is-invalid @enderror" required>
                            @foreach (InspectionType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('inspection_type', $inspection?->inspection_type?->value ?? 'ROUTINE') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('inspection_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $inspection?->notes ?? '') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if ($inspection && in_array(auth()->user()->role, ['admin', 'pengawas'], true))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach (InspectionStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $inspection->status?->value ?? $inspection->status) === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary" @disabled($travels->isEmpty() && ! $inspection)>Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $inspection ? 'Edit Pengawasan' : 'Buat Pengawasan' }}</h4>
            <a href="{{ route('v2.pengawasan.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $inspection ? route('v2.pengawasan.update', $inspection) : route('v2.pengawasan.store') }}">
                @csrf
                @if ($inspection) @method('PUT') @endif
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Pengawasan</label>
                        <input type="text" name="inspection_no" class="form-control" value="{{ old('inspection_no', $inspection->inspection_no ?? $inspectionNo) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="inspection_date" class="form-control" value="{{ old('inspection_date', optional($inspection?->inspection_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Travel</label>
                        <select name="travel_id" class="form-select" required>
                            <option value="">Pilih Travel</option>
                            @foreach ($travels as $travel)
                                <option value="{{ $travel->id }}" @selected(old('travel_id', $inspection->travel_id ?? '') == $travel->id)>{{ $travel->Penyelenggara }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="inspection_type" class="form-select" required>
                            @foreach (['ROUTINE','SPOT_CHECK','COMPLAINT_BASED','SPECIAL'] as $type)
                                <option value="{{ $type }}" @selected(old('inspection_type', $inspection->inspection_type?->value ?? 'ROUTINE') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $inspection->notes ?? '') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

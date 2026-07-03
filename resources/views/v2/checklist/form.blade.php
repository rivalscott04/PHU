@extends('layouts.app')

@section('content')
@php use App\Enums\ChecklistInputType; @endphp
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $checklist ? 'Edit Checklist' : 'Tambah Checklist' }}</h4>
            <a href="{{ route('v2.checklist.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>
            <form method="POST" action="{{ $checklist ? route('v2.checklist.update', $checklist) : route('v2.checklist.store') }}">
                @csrf
                @if ($checklist) @method('PUT') @endif
                <div class="row">
                    @if ($checklist)
                        <div class="col-12 mb-3">
                            <label class="form-label">Kode</label>
                            <input type="text" class="form-control" value="{{ $checklist->code }}" readonly disabled>
                            <div class="form-text">Kode dibuat otomatis dan tidak dapat diubah.</div>
                        </div>
                    @else
                        <div class="col-12 mb-3">
                            <div class="alert alert-light border mb-0 py-2">
                                <small class="text-muted">Kode dibuat otomatis saat disimpan, contoh: <strong>LEG{{ now()->format('Ym') }}001</strong> (kategori + bulan/tahun + urutan).</small>
                            </div>
                        </div>
                    @endif
                    <div class="col-12 mb-3">
                        <label class="form-label">Judul @include('partials.required-star')</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $checklist->title ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori @include('partials.required-star')</label>
                        <select name="category_id" class="form-select" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $checklist->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Jenis Pertanyaan @include('partials.required-star')</label>
                        <select name="input_type" class="form-select" required id="input-type-select">
                            @foreach (ChecklistInputType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('input_type', $checklist->input_type?->value ?? ChecklistInputType::Boolean->value) === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        <div class="form-text" id="input-type-hint">
                            {{ ChecklistInputType::from(old('input_type', $checklist->input_type?->value ?? ChecklistInputType::Boolean->value))->hint() }}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Bobot @include('partials.required-star')</label>
                        <input type="number" name="weight" class="form-control" value="{{ old('weight', $checklist->weight ?? 1) }}" min="1" max="100" required>
                        <div class="form-text">Semakin besar bobot, semakin berpengaruh ke skor kepatuhan.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
<script>
    const hints = @json(collect(ChecklistInputType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->hint()]));
    document.getElementById('input-type-select')?.addEventListener('change', function () {
        document.getElementById('input-type-hint').textContent = hints[this.value] ?? '';
    });
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @include('partials.bap-module-info', ['variant' => 'pemeriksaan'])
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Detail BA Pemeriksaan: {{ $inspection->inspection_no }}</h4>
                <small class="text-muted">Temuan dan tindak lanjut hasil pemeriksaan</small>
            </div>
            <div>
                @can('update', $inspection)
                    <a href="{{ route('v2.pengawasan.edit', $inspection) }}" class="btn btn-sm btn-warning">Edit Jadwal</a>
                @endcan
                <a href="{{ route('v2.pengawasan.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $inspectionStatus = $inspection->status?->value ?? $inspection->status;
        $isLocked = in_array($inspectionStatus, ['CLOSED', 'CANCELLED'], true);
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Travel:</strong> {{ $inspection->travel?->Penyelenggara }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $inspection->status?->badgeColor() ?? \App\Enums\InspectionStatus::badgeFor($inspection->status) }}">
                            {{ $inspection->status?->label() ?? \App\Enums\InspectionStatus::labelFor($inspection->status) }}
                        </span>
                    </p>
                    <p><strong>Skor Kepatuhan:</strong> {{ $inspection->overall_score !== null ? number_format($inspection->overall_score, 0).'%' : 'Belum dihitung' }}</p>
                    <p class="mb-0"><strong>Catatan:</strong> {{ $inspection->notes ?? '-' }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Periksa</h5>
                    @if ($checklistGroups->isNotEmpty())
                        @php
                            $filled = $inspection->checklists->filter(fn ($item) => filled($item->answer))->count();
                            $total = $inspection->checklists->count();
                        @endphp
                        <small class="text-muted">{{ $filled }} dari {{ $total }} terisi</small>
                    @endif
                </div>
                <div class="card-body">
                    @include('v2.pengawasan.partials.checklist-form')
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Temuan</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Judul</th><th>Tingkat</th><th>Status</th><th>Deadline</th></tr></thead>
                        <tbody>
                            @forelse ($inspection->findings as $finding)
                                <tr>
                                    <td>{{ $finding->title }}</td>
                                    <td>{{ $finding->severity?->label() ?? \App\Enums\FindingSeverity::labelFor($finding->severity) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $finding->status?->badgeColor() ?? \App\Enums\FindingStatus::badgeFor($finding->status) }}">
                                            {{ $finding->status?->label() ?? \App\Enums\FindingStatus::labelFor($finding->status) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($finding->deadline)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada temuan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @can('update', $inspection)
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Tambah Temuan</h5></div>
                <div class="card-body">
                    @if ($isLocked)
                        <div class="alert alert-warning mb-0">Pengawasan sudah ditutup atau dibatalkan. Temuan baru tidak dapat ditambahkan.</div>
                    @else
                    <form method="POST" action="{{ route('v2.pengawasan.temuan.store', $inspection) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" placeholder="Kategori" value="{{ old('category') }}" required>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                                    @foreach (\App\Enums\FindingSeverity::cases() as $severity)
                                        <option value="{{ $severity->value }}" @selected(old('severity', 'MAJOR') === $severity->value)>{{ $severity->label() }}</option>
                                    @endforeach
                                </select>
                                @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-2">
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-2">
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-2">
                                <textarea name="recommendation" class="form-control @error('recommendation') is-invalid @enderror" placeholder="Rekomendasi" required>{{ old('recommendation') }}</textarea>
                                @error('recommendation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline') }}">
                                @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Temuan</button>
                    </form>
                    @endif
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Tambah Temuan</h5></div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">Anda tidak memiliki akses untuk menambah temuan pada pengawasan ini.</div>
                </div>
            </div>
            @endcan
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Panduan</h5></div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Isi daftar periksa sesuai kondisi travel saat pengawasan dilakukan.</p>
                    <ul class="small text-muted mb-0 ps-3">
                        <li>Pertanyaan <strong>Ya / Tidak</strong> mempengaruhi skor kepatuhan.</li>
                        <li>Pertanyaan <strong>Pilihan</strong> juga mempengaruhi skor kepatuhan.</li>
                        <li>Angka, teks, dan keterangan bukti disimpan sebagai catatan lapangan.</li>
                        <li>Skor dihitung otomatis setelah Anda menyimpan daftar periksa.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

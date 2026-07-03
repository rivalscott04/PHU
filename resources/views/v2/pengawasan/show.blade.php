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

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Travel:</strong> {{ $inspection->travel?->Penyelenggara }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $inspection->status?->badgeColor() ?? 'secondary' }}">
                            {{ $inspection->status?->label() ?? $inspection->status }}
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
                                    <td>{{ $finding->severity?->label() ?? $finding->severity }}</td>
                                    <td>
                                        <span class="badge bg-{{ $finding->status?->badgeColor() ?? 'secondary' }}">
                                            {{ $finding->status?->label() ?? $finding->status }}
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
                    <form method="POST" action="{{ route('v2.pengawasan.temuan.store', $inspection) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2"><input type="text" name="category" class="form-control" placeholder="Kategori" required></div>
                            <div class="col-md-6 mb-2">
                                <select name="severity" class="form-select" required>
                                    <option value="MINOR">Ringan</option>
                                    <option value="MAJOR">Sedang</option>
                                    <option value="CRITICAL">Berat</option>
                                </select>
                            </div>
                            <div class="col-12 mb-2"><input type="text" name="title" class="form-control" placeholder="Judul" required></div>
                            <div class="col-12 mb-2"><textarea name="description" class="form-control" placeholder="Deskripsi" required></textarea></div>
                            <div class="col-12 mb-2"><textarea name="recommendation" class="form-control" placeholder="Rekomendasi" required></textarea></div>
                            <div class="col-md-6 mb-2"><input type="date" name="deadline" class="form-control"></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Temuan</button>
                    </form>
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

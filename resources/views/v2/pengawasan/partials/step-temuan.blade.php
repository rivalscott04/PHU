@php
    $inspectionStatus = $inspection->status?->value ?? $inspection->status;
    $isLocked = in_array($inspectionStatus, ['CLOSED', 'CANCELLED'], true);
    $canFinalize = auth()->user()->can('update', $inspection) && ! $isLocked && ($checklistComplete ?? false);
@endphp

<div class="pengawasan-wizard-panel" data-pengawasan-step="3">
    <div class="alert alert-success border-0 mb-4">
        <h6 class="alert-heading mb-1"><i class="bx bx-check-circle me-1"></i> Langkah Terakhir</h6>
        <p class="mb-0 small">Catat masalah yang ditemukan, atau selesaikan pemeriksaan jika kondisi travel sudah baik.</p>
    </div>

    @error('finalize')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-md-4">
                    <div class="text-muted small">Travel</div>
                    <div class="fw-medium">{{ $inspection->travel?->Penyelenggara }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Skor Kepatuhan</div>
                    <div class="fs-4 fw-semibold text-primary">{{ $inspection->overall_score !== null ? number_format($inspection->overall_score, 0).'%' : '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Jumlah Temuan</div>
                    <div class="fs-4 fw-semibold">{{ $inspection->findings->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Masalah yang Ditemukan</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tingkat</th>
                            <th>Status</th>
                            <th>Batas Waktu</th>
                        </tr>
                    </thead>
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
                                <td>{{ optional($finding->deadline)->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bx bx-check-shield d-block fs-3 mb-2"></i>
                                    Belum ada masalah yang dicatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('update', $inspection)
        @if (! $isLocked)
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Tambah Masalah</h5></div>
                <div class="card-body">
                    <p class="text-muted small">Isi hanya jika ada kondisi travel yang perlu diperbaiki.</p>
                    <form method="POST" action="{{ route('v2.pengawasan.temuan.store', $inspection) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror"
                                    placeholder="Contoh: Legalitas, Keuangan, Operasional" value="{{ old('category') }}" required>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tingkat Masalah</label>
                                <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                                    @foreach (\App\Enums\FindingSeverity::cases() as $severity)
                                        <option value="{{ $severity->value }}" @selected(old('severity', 'MAJOR') === $severity->value)>{{ $severity->label() }}</option>
                                    @endforeach
                                </select>
                                @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Ringkasan Masalah</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Contoh: Izin operasional sudah kadaluarsa" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Kondisi yang Ditemukan</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2"
                                    placeholder="Jelaskan kondisi di lapangan" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Saran Perbaikan</label>
                                <textarea name="recommendation" class="form-control @error('recommendation') is-invalid @enderror" rows="2"
                                    placeholder="Apa yang harus dilakukan travel" required>{{ old('recommendation') }}</textarea>
                                @error('recommendation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batas Waktu Perbaikan</label>
                                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline') }}">
                                @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-plus me-1"></i> Simpan Masalah
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endcan

    <div class="pengawasan-wizard-actions">
        <button type="button" class="btn btn-light" data-pengawasan-goto="2">
            <i class="bx bx-left-arrow-alt me-1"></i> Sebelumnya
        </button>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('v2.pengawasan.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar</a>

            @if ($canFinalize)
                <form method="POST" action="{{ route('v2.pengawasan.finalize', $inspection) }}" class="d-inline"
                    onsubmit="return confirm('Selesaikan pemeriksaan ini? Travel akan menerima notifikasi jika ada temuan.');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check me-1"></i>
                        {{ $inspection->findings->isEmpty() ? 'Tidak Ada Masalah, Selesai' : 'Selesaikan Pemeriksaan' }}
                    </button>
                </form>
            @elseif ($isLocked)
                <span class="btn btn-success disabled">
                    <i class="bx bx-check me-1"></i> Pemeriksaan Selesai
                </span>
            @else
                <span class="btn btn-secondary disabled" title="Lengkapi checklist terlebih dahulu">
                    Selesaikan Pemeriksaan
                </span>
            @endif
        </div>
    </div>
</div>

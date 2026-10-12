@extends('layouts.app')

@section('content')
@php
    $canVerify = in_array(auth()->user()->role, ['admin', 'pengawas'], true);
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-semibold">Tindak Lanjut Temuan</h4>
                <p class="text-muted mb-0 small">Bagian dari alur <strong>BA Pemeriksaan</strong>, respons travel atas temuan inspeksi pengawasan.
                    @if($canVerify)
                        Kelola dan verifikasi bukti tindak lanjut hasil pengawasan
                    @else
                        Riwayat bukti tindak lanjut travel Anda
                    @endif
                </p>
            </div>
            @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.index'))
                <a href="{{ route('v2.monitoring.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-radar me-1"></i> Monitoring
                </a>
            @endif
        </div>
    </div>

    @include('v2.partials.wilayah-scope')

    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_followup'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    @include('v2.partials.kpi-cards', ['cards' => $cards, 'id' => 'followup-kpi'])

    @if(! $canVerify)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0 fw-semibold">Temuan yang Perlu Ditindaklanjuti</h5>
                <p class="text-muted small mb-0">Unggah bukti perbaikan untuk setiap temuan sebelum batas waktunya.</p>
            </div>
            <div class="card-body">
                @forelse ($openFindings as $finding)
                    @php
                        $terakhir = $finding->followups->first();
                        $lewat = $finding->deadline && $finding->deadline->isPast();
                        $buka = (int) old('finding_id') === $finding->id;
                    @endphp
                    <div class="border rounded p-3 {{ $loop->last ? '' : 'mb-3' }}">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $finding->title }}</div>
                                <div class="small text-muted">
                                    {{ $finding->inspection?->inspection_no }}
                                    · Tingkat {{ $finding->severity?->label() }}
                                    · Batas waktu
                                    <span class="{{ $lewat ? 'text-danger fw-semibold' : '' }}">{{ $finding->deadline?->translatedFormat('j F Y') ?? 'tidak ditentukan' }}{{ $lewat ? ' (terlewati)' : '' }}</span>
                                </div>
                            </div>
                            <span class="badge bg-{{ $finding->status->badgeColor() }}">{{ $finding->status->label() }}</span>
                        </div>
                        @if($finding->description)
                            <p class="small mb-1 mt-2"><strong>Kondisi:</strong> {{ $finding->description }}</p>
                        @endif
                        @if($finding->recommendation)
                            <p class="small mb-1"><strong>Saran perbaikan:</strong> {{ $finding->recommendation }}</p>
                        @endif
                        @if($terakhir?->remarks && $finding->status === \App\Enums\FindingStatus::RevisionRequired)
                            <div class="alert alert-warning small py-2 mb-2 mt-2"><strong>Catatan pengawas:</strong> {{ $terakhir->remarks }}</div>
                        @endif

                        <button class="btn btn-sm btn-primary mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#tl-{{ $finding->id }}" aria-expanded="{{ $buka ? 'true' : 'false' }}" aria-controls="tl-{{ $finding->id }}">
                            <i class="bx bx-upload me-1"></i> {{ $terakhir ? 'Kirim Ulang Bukti' : 'Unggah Bukti' }}
                        </button>
                        <form id="tl-{{ $finding->id }}" class="collapse {{ $buka ? 'show' : '' }} mt-3" method="POST" action="{{ route('v2.followup.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="finding_id" value="{{ $finding->id }}">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold" for="tl-desk-{{ $finding->id }}">Penjelasan perbaikan</label>
                                <textarea id="tl-desk-{{ $finding->id }}" name="description" rows="3" minlength="20" required class="form-control form-control-sm @if($buka && $errors->has('description')) is-invalid @endif" placeholder="Jelaskan apa yang sudah diperbaiki, minimal 20 karakter.">{{ $buka ? old('description') : '' }}</textarea>
                                @if($buka) @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold" for="tl-file-{{ $finding->id }}">Bukti perbaikan</label>
                                <input id="tl-file-{{ $finding->id }}" type="file" name="attachment" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" class="form-control form-control-sm @if($buka && $errors->has('attachment')) is-invalid @endif">
                                <div class="form-text">PDF, Word, JPG, PNG, atau ZIP. Maksimal 10 MB.</div>
                                @if($buka) @error('attachment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror @endif
                            </div>
                            <button type="submit" class="btn btn-sm btn-success"><i class="bx bx-send me-1"></i> Kirim Tindak Lanjut</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted mb-0">Tidak ada temuan yang menunggu tindak lanjut.</p>
                @endforelse
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold">Daftar Tindak Lanjut</h5>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm" style="min-width:180px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\FollowupStatus::cases() as $followupStatus)
                        <option value="{{ $followupStatus->value }}" @selected(($filters['status'] ?? '') === $followupStatus->value)>{{ $followupStatus->label() }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Travel</th>
                            <th>Kabupaten</th>
                            <th>Temuan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($followups as $followup)
                            @php
                                $status = $followup->status?->value ?? $followup->status;
                            @endphp
                            <tr>
                                <td class="ps-3 fw-medium">{{ $followup->finding?->inspection?->travel?->Penyelenggara }}</td>
                                <td class="text-muted">{{ $followup->finding?->inspection?->travel?->kab_kota }}</td>
                                <td>{{ $followup->finding?->title }}</td>
                                <td>
                                    <span class="badge bg-{{ \App\Enums\FollowupStatus::badgeFor($status) }}">
                                        {{ \App\Enums\FollowupStatus::labelFor($status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ optional($followup->submitted_at)->format('d M Y, H:i') ?? 'Tidak ada' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('v2.followup.show', $followup) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bx bx-task d-block mb-2" style="font-size:2rem;"></i>
                                    Belum ada tindak lanjut{{ auth()->user()->role === 'kabupaten' ? ' di wilayah Anda' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($followups->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $followups->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

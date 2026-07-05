@extends('layouts.app')

@section('content')
@php
    use App\Enums\WorkQueueStatus;
    use App\Enums\WorkQueueType;
    use App\Support\WorkQueueNextSteps;

    $typeOptions = WorkQueueType::cases();
    $statusOptions = [WorkQueueStatus::Open, WorkQueueStatus::InProgress];
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-semibold">Antrian Kerja</h4>
                <p class="text-muted mb-0">
                    @if(auth()->user()->role === 'pengawas')
                        Tugas prioritas di wilayah {{ auth()->user()->getWilayahKerjaLabel() }}
                    @else
                        Oversight antrian pengawasan seluruh NTB
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.dashboard'))
                    <a href="{{ route('v2.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-bar-chart-alt-2 me-1"></i> Dashboard
                    </a>
                @endif
                @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.index'))
                    <a href="{{ route('v2.monitoring.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-radar me-1"></i> Monitoring
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'pengawas')
        @include('v2.partials.wilayah-scope')
    @endif

    @include('v2.partials.kpi-cards', ['cards' => $cards, 'id' => 'antrian-kpi'])

    @if(in_array(auth()->user()->role, ['admin', 'pengawas'], true))
        <div class="alert alert-light border mb-3 mb-md-4">
            <div class="d-flex gap-2">
                <i class="bx bx-info-circle text-primary fs-5 flex-shrink-0"></i>
                <div>
                    <strong class="d-block mb-1">Alur kerja antrian</strong>
                    <span class="text-muted">
                        <strong>Proses</strong> = klaim tugas &nbsp;→&nbsp;
                        <strong>Buka</strong> = kerjakan di modul terkait &nbsp;→&nbsp;
                        <strong>Selesai</strong> = tutup antrian setelah ditindaklanjuti
                    </span>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold">Daftar Antrian</h5>
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <select name="type" class="form-select form-select-sm" style="min-width:180px;" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    @foreach($typeOptions as $type)
                        <option value="{{ $type->value }}" @selected(($filters['type'] ?? request('type')) === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="form-select form-select-sm" style="min-width:160px;" onchange="this.form.submit()">
                    <option value="">Aktif (Open + Proses)</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:12%">Prioritas</th>
                            <th style="width:14%">Jenis</th>
                            <th>Judul</th>
                            <th style="width:16%">Travel / Wilayah</th>
                            <th style="width:12%">Deadline</th>
                            <th style="width:10%">Status</th>
                            <th style="width:18%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            @php $guide = WorkQueueNextSteps::forQueueItem($item); @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $item->priority >= 85 ? 'danger' : ($item->priority >= 70 ? 'warning text-dark' : 'secondary') }}">
                                        {{ $item->priority }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->type->badgeColor() }}">
                                        <i class="bx {{ $item->type->icon() }} me-1"></i>{{ $item->type->label() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->title }}</div>
                                    @if($item->summary)
                                        <div class="text-muted small">{{ $item->summary }}</div>
                                    @endif
                                    @include('v2.partials.work-queue-next-steps', ['guide' => $guide, 'compact' => true])
                                </td>
                                <td>
                                    <div>{{ $item->travel?->Penyelenggara ?? 'Tidak ada' }}</div>
                                    <div class="text-muted small">{{ $item->kabupaten ?? 'Tidak ada' }}</div>
                                </td>
                                <td>
                                    @if($item->due_at)
                                        <span class="{{ $item->due_at->isPast() ? 'text-danger fw-semibold' : '' }}">
                                            {{ $item->due_at->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status->badgeColor() }}">{{ $item->status->label() }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                        <a href="{{ $item->action_url }}" class="btn btn-sm btn-primary">Buka</a>
                                        @if($item->isActionable())
                                            @if($item->status === WorkQueueStatus::Open)
                                                <form method="POST" action="{{ route('v2.antrian.start', $item) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info">Proses</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('v2.antrian.resolve', $item) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">Selesai</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bx bx-check-circle fs-1 d-block mb-2"></i>
                                    Antrian kosong, tidak ada tugas yang menunggu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-transparent">
                {{ $items->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection

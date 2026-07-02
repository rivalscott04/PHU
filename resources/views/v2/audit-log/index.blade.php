@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-1">Riwayat Aktivitas</h4>
            <p class="text-muted mb-0">Catatan siapa melakukan apa dalam sistem pengawasan.</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('v2.audit-log.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Jenis kegiatan</label>
                    <select name="module" class="form-select form-select-sm">
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['module'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari tanggal</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai tanggal</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari nama atau kegiatan</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Contoh: Budi, pengawasan, tindak lanjut">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                    <a href="{{ route('v2.audit-log.index') }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:150px;">Waktu</th>
                            <th style="width:180px;">Pelaku</th>
                            <th>Apa yang dilakukan</th>
                            <th style="width:140px;">Jenis</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $entry)
                            <tr>
                                <td class="text-muted small">{{ $entry['created_at_label'] }}</td>
                                <td>
                                    <strong>{{ $entry['actor'] }}</strong>
                                    <div class="text-muted small">{{ $entry['actor_role'] }}</div>
                                </td>
                                <td>{{ $entry['summary'] }}</td>
                                <td><span class="badge bg-light text-dark">{{ $entry['category'] }}</span></td>
                                <td>
                                    <a href="{{ route('v2.audit-log.show', $entry['id']) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Belum ada aktivitas yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection

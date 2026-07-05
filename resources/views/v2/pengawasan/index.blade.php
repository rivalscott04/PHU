@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @include('partials.bap-module-info', ['variant' => 'pemeriksaan'])
    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_pengawasan'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0">BA Pemeriksaan</h4>
                <small class="text-muted">Jadwal dan hasil pemeriksaan pengawasan PPIU</small>
            </div>
            <div class="d-flex gap-2">
                @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.export.pengawasan'))
                    <a href="{{ route('v2.export.pengawasan', request()->query()) }}" class="btn btn-sm btn-outline-danger">Unduh PDF</a>
                @endif
                @can('create', \App\Models\Inspection::class)
                    <a href="{{ route('v2.pengawasan.create') }}" class="btn btn-primary btn-sm">Buat Pemeriksaan</a>
                @endcan
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pengawasan-table">
                    <thead>
                        <tr>
                            <th>No Pengawasan</th>
                            <th>Travel</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inspections as $inspection)
                            <tr>
                                <td>{{ $inspection->inspection_no }}</td>
                                <td>{{ $inspection->travel?->Penyelenggara }}</td>
                                <td>{{ $inspection->inspection_date?->format('d/m/Y') }}</td>
                                <td>{{ $inspection->inspection_type?->label() ?? $inspection->inspection_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $inspection->status?->badgeColor() ?? 'secondary' }}">
                                        {{ $inspection->status?->label() ?? $inspection->status }}
                                    </span>
                                </td>
                                <td><a href="{{ route('v2.pengawasan.show', $inspection) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $inspections->links() }}
        </div>
    </div>
</div>
@endsection

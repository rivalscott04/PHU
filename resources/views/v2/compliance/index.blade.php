@extends('layouts.app')

@section('content')
@php
    $riskBadges = [
        'LOW' => 'success',
        'MEDIUM' => 'info',
        'HIGH' => 'warning',
        'CRITICAL' => 'danger',
    ];
    $riskLabels = [
        'LOW' => 'Rendah',
        'MEDIUM' => 'Sedang',
        'HIGH' => 'Tinggi',
        'CRITICAL' => 'Kritis',
    ];
@endphp

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-semibold">Profil Kepatuhan</h4>
                <p class="text-muted mb-0">
                    @if(auth()->user()->role === 'kabupaten')
                        Profil kepatuhan travel di wilayah {{ auth()->user()->getWilayahKerjaLabel() }}
                    @elseif(auth()->user()->role === 'user')
                        Profil kepatuhan perusahaan Anda
                    @else
                        Profil kepatuhan seluruh PPIU dan PIHK
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

    @if($guide = \App\Support\RoleWorkflowGuide::for('v2_compliance'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Daftar Travel</h5>
            <span class="badge bg-light text-dark border">{{ $travels->total() }} travel</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Travel</th>
                            @if(auth()->user()->role === 'admin')
                                <th>Kabupaten</th>
                            @endif
                            <th>Jenis</th>
                            <th>Pengawasan</th>
                            <th>Pengaduan</th>
                            <th>Risiko</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($travels as $travel)
                            @php
                                $risk = $travel->riskScore?->risk_level?->value ?? $travel->riskScore?->risk_level;
                            @endphp
                            <tr>
                                <td class="ps-3 fw-medium">{{ $travel->Penyelenggara }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="text-muted">{{ $travel->kab_kota }}</td>
                                @endif
                                <td><span class="badge bg-light text-dark border">{{ $travel->Status }}</span></td>
                                <td>{{ number_format($travel->inspections_count ?? 0) }}</td>
                                <td>{{ number_format($travel->pengaduan_count ?? 0) }}</td>
                                <td>
                                    @if($risk)
                                        <span class="badge bg-{{ $riskBadges[$risk] ?? 'secondary' }}">
                                            {{ $riskLabels[$risk] ?? $risk }}
                                        </span>
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('v2.compliance.show', $travel) }}" class="btn btn-sm btn-outline-primary">
                                        Lihat Profil
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? 7 : 6 }}" class="text-center text-muted py-5">
                                    <i class="bx bx-shield-quarter d-block mb-2" style="font-size:2rem;"></i>
                                    Belum ada travel{{ auth()->user()->role === 'kabupaten' ? ' di wilayah Anda' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($travels->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $travels->links() }}
                </div>
            @endif
        </div>
    </div>

    <p class="text-muted small mt-2 mb-0">
        <i class="bx bx-info-circle me-1"></i>
        Profil kepatuhan bersifat baca saja dan dihasilkan otomatis dari data operasional.
    </p>
</div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Dashboard Kabupaten</h4>
                    <p class="text-muted mb-0 small">{{ $kabupaten ?? '' }}</p>
                </div>
                <a href="{{ route('bap') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-file me-1"></i> Proses BA Pemberangkatan
                </a>
            </div>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('home'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    @include('partials.home-queue-cards', [
        'queues' => $queues,
        'status' => $queueStatus,
        'title' => 'Antrian Wilayah Anda',
        'subtitle' => 'Fokus pada BA Pemberangkatan dan pengaduan di kabupaten/kota ini',
    ])

    @include('partials.home-travel-alerts', ['alerts' => $alerts])

    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Jamaah Haji (bulan ini)</p>
                    <h4 class="mb-0">{{ $summary['jamaah_haji'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">Jamaah Umrah (bulan ini)</p>
                    <h4 class="mb-0">{{ $summary['jamaah_umrah'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">BA Diajukan / Diproses</p>
                    <h4 class="mb-0">
                        {{ ($summary['bap_diajukan'] ?? 0) + ($summary['bap_diproses'] ?? 0) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <p class="text-muted mb-1">BA Diterima</p>
                    <h4 class="mb-0">{{ $summary['bap_diterima'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    @include('partials.home-kabupaten-activity', [
        'recentPendingBap' => $recentPendingBap,
        'openPengaduan' => $openPengaduan,
        'upcomingDepartures' => $upcomingDepartures,
    ])
@endsection

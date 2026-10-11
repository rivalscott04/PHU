@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Hari Ini</h4>
                    <p class="text-muted mb-0 small">Yang perlu dikerjakan. Klik kartu untuk membuka daftarnya.</p>
                </div>
            </div>
        </div>
    </div>

    @if($guide = \App\Support\RoleWorkflowGuide::for('home'))
        @include('partials.workflow-guide', ['guide' => $guide])
    @endif

    @include('partials.home-traffic-light', [
        'status' => $queueStatus,
        'queues' => $queues,
    ])

    @include('partials.home-queue-cards', [
        'queues' => $queues,
        'status' => $queueStatus,
        'title' => 'Yang menunggu',
    ])

    @include('partials.home-travel-alerts', ['alerts' => $alerts])

    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('travel', ['filter' => 'pending']) }}" class="text-decoration-none text-body">
                <div class="card mini-stats-wid h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Registrasi Menunggu</p>
                        <h4 class="mb-0">{{ $summary['registration_pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('bap') }}" class="text-decoration-none text-body">
                <div class="card mini-stats-wid h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">BA Menunggu</p>
                        <h4 class="mb-0">{{ $summary['bap_pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('pengaduan') }}" class="text-decoration-none text-body">
                <div class="card mini-stats-wid h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pengaduan Terbuka</p>
                        <h4 class="mb-0">{{ $summary['pengaduan_open'] ?? 0 }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.risk.index'))
                <a href="{{ route('v2.risk.index') }}" class="text-decoration-none text-body">
            @endif
                <div class="card mini-stats-wid h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Risiko Tinggi</p>
                        <h4 class="mb-0">{{ $summary['risk_high'] ?? 0 }}</h4>
                    </div>
                </div>
            @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.risk.index'))
                </a>
            @endif
        </div>
    </div>

    @include('partials.home-admin-activity', [
        'recentPendingBap' => $recentPendingBap,
        'pendingRegistrations' => $pendingRegistrations,
        'openPengaduan' => $openPengaduan,
        'highRiskTravels' => $highRiskTravels,
        'upcomingDepartures' => $upcomingDepartures,
    ])
@endsection

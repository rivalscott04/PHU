@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Selamat datang, {{ \Illuminate\Support\Str::title($username) }}</h4>
                    <p class="text-muted mb-0 small">Dashboard Travel</p>
                </div>
                @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'form.bap'))
                    <a href="{{ route('form.bap') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i> Ajukan BA Baru
                    </a>
                @endif
            </div>
        </div>
    </div>

    @include('partials.home-travel-checklist', ['checklist' => $checklist])

    @include('partials.home-travel-alerts', ['alerts' => $alerts])

    @include('partials.home-travel-activity', [
        'activeBap' => $activeBap,
        'upcomingDepartures' => $upcomingDepartures,
    ])
@endsection

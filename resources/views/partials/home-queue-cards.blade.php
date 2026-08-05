@php
    use App\Support\RouteAccess;

    $queues = $queues ?? [];
    $status = $status ?? null;
    $title = $title ?? 'Antrian Kerja Hari Ini';
    $subtitle = $subtitle ?? 'Klik kartu untuk langsung membuka daftar terkait';
@endphp

<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="mb-1">{{ $title }}</h5>
            <p class="text-muted mb-0 small">{{ $subtitle }}</p>
        </div>
        @if ($status)
            <span class="badge {{ $status['badge_class'] ?? 'bg-secondary' }} fs-6">
                {{ $status['label'] ?? 'Status' }}
            </span>
        @endif
    </div>
</div>

@if ($status && ! empty($status['message']))
    <div class="alert alert-light border mb-4 text-body">
        {{ $status['message'] }}
    </div>
@endif

<div class="row mb-4">
    @foreach ($queues as $queue)
        @php
            $canAccess = RouteAccess::canAccessRoute(auth()->user(), $queue['route'], $queue['params'] ?? []);
            $url = $canAccess ? route($queue['route'], $queue['params'] ?? []) : '#';
            $count = (int) ($queue['count'] ?? 0);
        @endphp
        <div class="col-xl-3 col-md-6 mb-3">
            @if ($canAccess)
                <a href="{{ $url }}" class="text-decoration-none text-body">
            @endif
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">{{ $queue['label'] }}</p>
                            <h3 class="mb-1 fw-semibold text-body">{{ number_format($count, 0, ',', '.') }}</h3>
                            <small class="text-muted">{{ $queue['hint'] ?? '' }}</small>
                        </div>
                        <div class="avatar-sm rounded-circle bg-light text-muted d-flex align-items-center justify-content-center">
                            <i class="bx {{ $queue['icon'] }} fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            @if ($canAccess)
                </a>
            @endif
        </div>
    @endforeach
</div>

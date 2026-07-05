@php
    use App\Support\RouteAccess;

    $links = RouteAccess::monitoringQuickAccessLinks();
@endphp

@if($links !== [])
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="mb-0 fw-semibold">Akses Cepat</h5>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2">
                @foreach ($links as $link)
                    <a
                        href="{{ route($link['route'], $link['params'] ?? []) }}"
                        class="btn btn-{{ $link['style'] }} text-start"
                    >
                        <i class="bx {{ $link['icon'] }} me-2"></i> {{ $link['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

@php
    use App\Support\RouteAccess;

    $travelId = $travelId ?? $travel?->id;
    $travelName = $travelName ?? $travel?->Penyelenggara;
    $count = (int) ($count ?? $travel?->pengaduan_count ?? 0);
    $canDrilldown = $count > 0
        && $travelId
        && RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.travel.pengaduan', ['travel' => $travelId]);
@endphp

@if($canDrilldown)
    <button
        type="button"
        class="btn btn-link btn-sm p-0 text-primary text-decoration-none pengaduan-drilldown fw-semibold"
        data-travel-id="{{ $travelId }}"
        data-travel-name="{{ $travelName }}"
        title="Lihat daftar pengaduan"
    >
        {{ number_format($count) }}
    </button>
@else
    {{ number_format($count) }}
@endif

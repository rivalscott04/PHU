@php
    use App\Support\RouteAccess;

    $kabupaten = $kabupaten ?? null;
    $count = (int) ($count ?? 0);
    $canDrilldown = $count > 0
        && $kabupaten
        && RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.kabupaten.pengaduan', ['kabupaten' => $kabupaten]);
@endphp

@if($canDrilldown)
    <button
        type="button"
        class="btn btn-link btn-sm p-0 text-primary text-decoration-none pengaduan-kabupaten-drilldown fw-semibold"
        data-kabupaten="{{ $kabupaten }}"
        title="Lihat daftar pengaduan"
    >
        {{ number_format($count) }}
    </button>
@else
    {{ number_format($count) }}
@endif

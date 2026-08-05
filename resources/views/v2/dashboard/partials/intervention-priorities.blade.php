@php
    use App\Support\DashboardExecutive;
    use App\Support\RouteAccess;

    $priorities = $executive['intervention_priorities'] ?? [];
    $canMonitoringTravel = RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.index');
@endphp

<div class="card border-0 shadow-sm mb-3" id="v2-intervention-priorities">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-semibold">Prioritas Intervensi</h5>
            <small class="text-muted">Klik baris untuk filter kabupaten/kota terkait</small>
        </div>
        @if(RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.travel'))
            <a href="{{ route('v2.monitoring.travel') }}" class="btn btn-sm btn-link text-primary p-0">
                Lihat semua travel <i class="bx bx-chevron-right"></i>
            </a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Urgensi</th>
                        <th>Penyelenggara</th>
                        <th>Kabupaten</th>
                        <th>Isu</th>
                        <th class="text-end pe-3"></th>
                    </tr>
                </thead>
                <tbody id="intervention-priorities-body">
                    @forelse ($priorities as $row)
                        <tr class="intervention-priority-row" role="button" tabindex="0"
                            data-filter-kabupaten="{{ $row['kabupaten'] ?? '' }}"
                            title="Klik untuk filter kabupaten {{ $row['kabupaten'] ?? '' }}">
                            <td class="ps-3">
                                <span class="badge bg-{{ DashboardExecutive::urgencyBadge($row['urgency'] ?? 'medium') }}">
                                    {{ DashboardExecutive::urgencyLabel($row['urgency'] ?? 'medium') }}
                                </span>
                            </td>
                            <td class="fw-medium">{{ $row['travel'] ?? '-' }}</td>
                            <td>{{ $row['kabupaten'] ?? '-' }}</td>
                            <td>{{ $row['issue'] ?? '-' }}</td>
                            <td class="text-end pe-3">
                                @if($canMonitoringTravel && ! empty($row['travel_id']))
                                    <a href="{{ route('v2.monitoring.travel.pengaduan', ['travel' => $row['travel_id']]) }}"
                                       class="btn btn-sm btn-light"
                                       onclick="event.stopPropagation()">
                                        Detail
                                    </a>
                                @else
                                    <span class="btn btn-sm btn-light disabled">Filter</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada penyelenggara yang memerlukan intervensi saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

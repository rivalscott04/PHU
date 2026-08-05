@php
    use App\Support\DashboardExecutive;

    $executive = $executive ?? [];
    $summary = $executive['summary'] ?? [];
    $points = $summary['points'] ?? [];
    $period = $summary['period'] ?? null;
@endphp

<div class="card border-0 shadow-sm mb-3" id="v2-executive-summary">
    <div class="card-body">
        <div class="d-flex align-items-start gap-3">
            <div class="avatar-sm rounded-circle bg-light text-muted d-flex align-items-center justify-content-center flex-shrink-0">
                <i class="bx bx-notepad fs-4"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <h5 class="mb-0 fw-semibold">Ringkasan Eksekutif</h5>
                    @if($period)
                        <span class="badge bg-light text-muted border" id="executive-summary-period">{{ $period }}</span>
                    @endif
                </div>
                <ul class="list-unstyled mb-0" id="executive-summary-list">
                    @forelse ($points as $point)
                        @php
                            $tone = $point['tone'] ?? 'default';
                            $statusBadge = DashboardExecutive::pointStatusBadge($tone);
                        @endphp
                        <li class="d-flex align-items-start gap-2 mb-2 executive-summary-point">
                            <span class="mt-2 flex-shrink-0 rounded-circle {{ DashboardExecutive::pointDotClass($tone) }}" style="width:6px;height:6px;"></span>
                            <span>
                                <span class="fw-semibold text-body">{{ $point['label'] }}:</span>
                                <span class="{{ DashboardExecutive::pointTextClass($tone) }}">{{ $point['text'] }}</span>
                                @if($statusBadge)
                                    <span class="{{ $statusBadge['class'] }} ms-1">{{ $statusBadge['label'] }}</span>
                                @endif
                            </span>
                        </li>
                    @empty
                        <li class="text-muted">Memuat ringkasan...</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

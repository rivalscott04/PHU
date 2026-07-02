<div class="card mb-3" id="v2-dashboard-timeline">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Activity Timeline</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-refresh-timeline">Refresh</button>
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0" id="timeline-list">
            @forelse ($timeline as $event)
                <li class="border-bottom pb-3 mb-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <span class="badge bg-light text-dark">{{ strtoupper($event['type']) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                @if (!empty($event['url']))
                                    <a href="{{ $event['url'] }}">{{ $event['title'] }}</a>
                                @else
                                    {{ $event['title'] }}
                                @endif
                            </h6>
                            <p class="text-muted mb-1">{{ $event['description'] }}</p>
                            <small class="text-muted">{{ $event['relative'] ?? '' }}</small>
                        </div>
                    </div>
                </li>
            @empty
                <li class="text-muted text-center">Belum ada aktivitas terbaru.</li>
            @endforelse
        </ul>
    </div>
</div>

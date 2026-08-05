@php
    $status = $status ?? [];
    $queues = $queues ?? [];
    $level = $status['level'] ?? 'ok';
    $lightClass = match ($level) {
        'critical' => 'bg-danger',
        'warning' => 'border border-secondary bg-transparent',
        default => 'bg-secondary opacity-25',
    };
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle {{ $lightClass }}" style="width: 14px; height: 14px;"></span>
                <h5 class="mb-0">{{ $status['label'] ?? 'Kondisi Umum' }}</h5>
            </div>
            <span class="badge {{ $status['badge_class'] ?? 'bg-secondary' }}">{{ strtoupper($level) }}</span>
            <p class="text-muted mb-0 small flex-grow-1">{{ $status['message'] ?? '' }}</p>
        </div>
        @if ($queues !== [])
            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach ($queues as $queue)
                    @if (($queue['count'] ?? 0) > 0)
                        <span class="badge bg-light text-dark border">
                            {{ $queue['label'] }}: {{ $queue['count'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

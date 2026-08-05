@php
    $alerts = $alerts ?? [];
@endphp

@if (! empty($alerts))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Perlu Perhatian</h5>
                    <div class="list-group list-group-flush">
                        @foreach ($alerts as $alert)
                            <a href="{{ $alert['url'] }}"
                               class="list-group-item list-group-item-action px-0 d-flex align-items-start gap-3">
                                <i class="bx {{ $alert['icon'] }} fs-5 mt-1 text-{{ $alert['tone'] }}"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $alert['label'] }}</div>
                                    <small class="text-muted">{{ $alert['hint'] }}</small>
                                </div>
                                <i class="bx bx-chevron-right text-muted"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

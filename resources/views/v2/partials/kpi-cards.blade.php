<div class="row" id="{{ $id ?? 'v2-kpi-cards' }}">
    @foreach ($cards as $key => $card)
        <div class="{{ $colClass ?? 'col-xl-3 col-md-4 col-sm-6' }} mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase small">
                                {{ $card['label'] ?? str_replace('_', ' ', $key) }}
                            </p>
                            <h3 class="mb-1 fw-semibold text-body" data-kpi="{{ $key }}">{{ number_format($card['value'] ?? 0) }}</h3>
                            @if (array_key_exists('trend', $card))
                                @if (($card['trend'] ?? 0) != 0)
                                    <small class="text-muted">
                                        {{ $card['direction'] === 'up' ? 'Naik' : ($card['direction'] === 'down' ? 'Turun' : 'Stabil') }}
                                        {{ abs($card['trend']) }}% vs bulan lalu
                                    </small>
                                @else
                                    <small class="text-muted">Stabil vs bulan lalu</small>
                                @endif
                            @endif
                        </div>
                        @if (! empty($card['icon']))
                            <div class="avatar-sm rounded-circle bg-light text-muted d-flex align-items-center justify-content-center">
                                <i class="bx {{ $card['icon'] }} fs-4"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

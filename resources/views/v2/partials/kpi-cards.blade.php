<div class="row" id="{{ $id ?? 'v2-kpi-cards' }}">
    @foreach ($cards as $key => $card)
        @php
            $color = $card['color'] ?? '#556ee6';
        @endphp
        <div class="{{ $colClass ?? 'col-xl-3 col-md-4 col-sm-6' }} mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid {{ $color }} !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:0.04em;">
                                {{ $card['label'] ?? str_replace('_', ' ', $key) }}
                            </p>
                            <h3 class="mb-1 fw-semibold" data-kpi="{{ $key }}">{{ number_format($card['value'] ?? 0) }}</h3>
                            @if (array_key_exists('trend', $card))
                                @if (($card['trend'] ?? 0) != 0)
                                    <small class="{{ $card['direction'] === 'up' ? 'text-success' : ($card['direction'] === 'down' ? 'text-danger' : 'text-muted') }}">
                                        {{ $card['direction'] === 'up' ? '▲' : ($card['direction'] === 'down' ? '▼' : '■') }}
                                        {{ abs($card['trend']) }}% vs bulan lalu
                                    </small>
                                @else
                                    <small class="text-muted">■ stabil</small>
                                @endif
                            @endif
                        </div>
                        @if (! empty($card['icon']))
                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center" style="background: {{ $color }}15;">
                                <i class="bx {{ $card['icon'] }} fs-4" style="color: {{ $color }};"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

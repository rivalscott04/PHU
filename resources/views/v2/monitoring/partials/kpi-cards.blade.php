@php
    $layout = $kpiLayout ?? [];
    $sections = $layout['sections'] ?? [];
    $isExecutive = ($layout['layout'] ?? '') === 'executive';
@endphp

<div id="{{ $id ?? 'monitoring-kpi' }}">
    @foreach ($sections as $section)
        @if (! empty($section['title']))
            <div class="d-flex align-items-center mb-2 {{ $loop->first ? '' : 'mt-1' }}">
                <h6 class="text-muted text-uppercase small mb-0">{{ $section['title'] }}</h6>
            </div>
        @endif

        <div class="row {{ $loop->last ? 'mb-1' : 'mb-2' }}">
            @foreach ($section['cards'] as $card)
                @php
                    $tone = $card['tone'] ?? 'default';
                    $toneClass = match ($tone) {
                        'danger' => 'border-danger border-opacity-25',
                        'warning' => 'border-warning border-opacity-50',
                        default => '',
                    };
                    $valueClass = match ($tone) {
                        'danger' => 'text-danger',
                        'warning' => 'text-warning',
                        default => 'text-body',
                    };
                    $colClass = $isExecutive
                        ? 'col-xl-3 col-md-6'
                        : (($section['key'] ?? '') === 'profile' ? 'col-md-6' : 'col-xl-3 col-md-6');
                    $hasUrl = ! empty($card['url']);
                @endphp
                <div class="{{ $colClass }} mb-3">
                    @if ($hasUrl)
                        <a href="{{ $card['url'] }}" class="text-decoration-none text-body d-block h-100">
                    @endif
                    <div class="card border-0 shadow-sm h-100 {{ $toneClass }} {{ $hasUrl ? 'monitoring-kpi-link' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="pe-2">
                                    <p class="text-muted mb-1 text-uppercase small">{{ $card['label'] }}</p>
                                    @if (! empty($card['composite']))
                                        <h3 class="mb-1 fw-semibold {{ $valueClass }}"
                                            data-kpi-composite="{{ $card['key'] }}"
                                            data-kpi-parts="{{ implode(',', $card['composite']) }}">
                                            {{ number_format($card['value']) }}
                                        </h3>
                                    @else
                                        <h3 class="mb-1 fw-semibold {{ $valueClass }}" data-kpi="{{ $card['key'] }}">
                                            {{ number_format($card['value']) }}
                                        </h3>
                                    @endif
                                    @if (! empty($card['hint']))
                                        <small class="text-muted d-block">{{ $card['hint'] }}</small>
                                    @endif
                                    @if (($card['key'] ?? '') === 'total_travel')
                                        <small class="text-muted d-block mt-1">
                                            <span data-kpi="total_ppiu">{{ number_format($layout['summary']['total_ppiu'] ?? 0) }}</span> PPIU ·
                                            <span data-kpi="total_pihk">{{ number_format($layout['summary']['total_pihk'] ?? 0) }}</span> PIHK ·
                                            <span data-kpi="total_cabang">{{ number_format($layout['summary']['total_cabang'] ?? 0) }}</span> Cabang
                                        </small>
                                    @elseif (($card['key'] ?? '') === 'total_jamaah')
                                        <small class="text-muted d-block mt-1">
                                            <span data-kpi="total_jamaah_haji_khusus">{{ number_format($layout['summary']['total_jamaah_haji_khusus'] ?? 0) }}</span> haji khusus
                                        </small>
                                    @endif
                                </div>
                                @if (! empty($card['icon']))
                                    <div class="avatar-sm rounded-circle bg-light text-muted d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="bx {{ $card['icon'] }} fs-4"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($hasUrl)
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>

@once
    @push('styles')
        <style>
            .monitoring-kpi-link {
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }
            .monitoring-kpi-link:hover {
                transform: translateY(-1px);
                box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08) !important;
            }
        </style>
    @endpush
@endonce

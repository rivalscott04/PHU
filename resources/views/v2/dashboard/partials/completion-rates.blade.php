@php
    $rates = $executive['completion_rates'] ?? [];
@endphp

<div class="row mb-3" id="v2-completion-rates">
    @foreach ($rates as $key => $rate)
        @php
            $percent = (float) ($rate['percent'] ?? 0);
            $color = $percent >= 75 ? '#34c38f' : ($percent >= 50 ? '#f1b44c' : '#f46a6a');
        @endphp
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid {{ $color }} !important;">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:0.04em;">
                        {{ $rate['label'] ?? $key }}
                    </p>
                    <h3 class="mb-1 fw-semibold completion-rate-value" data-rate="{{ $key }}">{{ number_format($percent, 1) }}%</h3>
                    <small class="text-muted completion-rate-detail" data-rate="{{ $key }}">
                        @if(($rate['total'] ?? 0) > 0)
                            {{ number_format($rate['selesai'] ?? 0) }} dari {{ number_format($rate['total']) }}
                        @else
                            Belum ada data
                        @endif
                    </small>
                </div>
            </div>
        </div>
    @endforeach
</div>

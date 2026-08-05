@php
    use App\Support\DashboardExecutive;

    $rates = $executive['completion_rates'] ?? [];
@endphp

<div class="row mb-3" id="v2-completion-rates">
    @foreach ($rates as $key => $rate)
        @php
            $percent = (float) ($rate['percent'] ?? 0);
            $status = DashboardExecutive::completionRateStatus($percent);
        @endphp
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100 completion-rate-card {{ $status['cardClass'] }}" data-rate="{{ $key }}">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase small">
                        {{ $rate['label'] ?? $key }}
                    </p>
                    <div class="d-flex align-items-baseline gap-2 flex-wrap">
                        <h3 class="mb-0 fw-semibold text-body completion-rate-value" data-rate="{{ $key }}">{{ number_format($percent, 1) }}%</h3>
                        @if($status['badgeLabel'] !== '')
                            <span class="completion-rate-badge {{ $status['badgeClass'] }}" data-rate="{{ $key }}">{{ $status['badgeLabel'] }}</span>
                        @else
                            <span class="completion-rate-badge d-none" data-rate="{{ $key }}"></span>
                        @endif
                    </div>
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

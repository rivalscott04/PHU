@php
    $checklist = $checklist ?? [];
    $steps = $checklist['steps'] ?? [];
    $stats = $checklist['stats'] ?? [];
    $registrationStatus = $checklist['registration_status'] ?? null;
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Langkah Berikutnya</h5>
                        <p class="text-muted mb-0 small">
                            @if (! empty($checklist['travel_name']))
                                {{ $checklist['travel_name'] }}
                            @else
                                Ikuti langkah berikut untuk operasional travel Anda
                            @endif
                        </p>
                    </div>
                    @if ($registrationStatus)
                        <span class="badge {{ $registrationStatus->badgeClass() }}">
                            {{ $registrationStatus->label() }}
                        </span>
                    @endif
                </div>

                @if ($registrationStatus?->value === 'rejected' && ! empty($checklist['registration_notes']))
                    <div class="alert alert-danger mb-3">
                        <strong>Registrasi ditolak:</strong> {{ $checklist['registration_notes'] }}
                    </div>
                @endif

                <div class="list-group list-group-flush">
                    @foreach ($steps as $step)
                        @php
                            $tone = $step['tone'] ?? 'secondary';
                            $icon = match ($tone) {
                                'success' => 'bx-check-circle text-success',
                                'warning' => 'bx-time-five text-warning',
                                'danger' => 'bx-x-circle text-danger',
                                default => $step['done'] ?? false ? 'bx-check-circle text-success' : 'bx-circle text-muted',
                            };
                        @endphp
                        <div class="list-group-item px-0 d-flex align-items-start gap-3">
                            <i class="bx {{ $icon }} fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $step['label'] }}</div>
                                <small class="text-muted">{{ $step['hint'] ?? '' }}</small>
                            </div>
                            @if (! empty($step['url']))
                                <a href="{{ $step['url'] }}" class="btn btn-sm btn-outline-primary">Buka</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <p class="text-muted mb-1">Jamaah Terdaftar</p>
                <h4 class="mb-0">{{ $stats['jamaah_total'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <p class="text-muted mb-1">BA Diajukan</p>
                <h4 class="mb-0">{{ $stats['bap_diajukan'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <p class="text-muted mb-1">BA Diproses</p>
                <h4 class="mb-0">{{ $stats['bap_diproses'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <p class="text-muted mb-1">BA Diterima</p>
                <h4 class="mb-0">{{ $stats['bap_diterima'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

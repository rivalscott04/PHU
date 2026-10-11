{{-- Posisi pendaftaran PPIU yang belum disetujui. Data: HomeCommandCenter::registrationSteps(). --}}
@php
    $ikon = [
        'done' => 'bx-check',
        'current' => 'bx-time-five',
        'warning' => 'bx-error',
        'danger' => 'bx-x',
    ];
@endphp
<ol class="reg-stepper" aria-label="Posisi pendaftaran">
    @foreach ($steps as $i => $step)
        <li class="reg-step is-{{ $step['state'] }}" @if (in_array($step['state'], ['current', 'warning', 'danger'], true)) aria-current="step" @endif>
            <span class="reg-step-dot">
                @isset($ikon[$step['state']])
                    <i class="bx {{ $ikon[$step['state']] }}" aria-hidden="true"></i>
                @else
                    {{ $i + 1 }}
                @endisset
            </span>
            <span class="reg-step-label">{{ $step['label'] }}</span>
            @if ($step['hint'])
                <span class="reg-step-hint">{{ $step['hint'] }}</span>
            @endif
        </li>
    @endforeach
</ol>

@once
    @push('styles')
        <style>
            .reg-stepper { display: flex; list-style: none; margin: 0 0 1.25rem; padding: 0; counter-reset: step; }
            .reg-step { flex: 1 1 0; position: relative; display: flex; flex-direction: column; align-items: center; text-align: center; gap: .25rem; padding: 0 .25rem; }
            /* Garis penghubung ke langkah sebelumnya. */
            .reg-step + .reg-step::before { content: ""; position: absolute; top: 17px; right: 50%; width: 100%; height: 2px; background: var(--bs-border-color, #e2e5ea); z-index: 0; }
            .reg-step.is-done + .reg-step::before,
            .reg-step.is-done + .reg-step.is-done::before { background: var(--bs-success, #34c38f); }
            .reg-step-dot { position: relative; z-index: 1; width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-weight: 600; font-size: .875rem; background: var(--bs-body-bg, #fff); border: 2px solid var(--bs-border-color, #e2e5ea); color: var(--bs-secondary-color, #74788d); }
            .reg-step-dot .bx { font-size: 1.15rem; }
            .reg-step-label { font-weight: 600; font-size: .8125rem; color: var(--bs-secondary-color, #74788d); }
            .reg-step-hint { font-size: .75rem; color: var(--bs-secondary-color, #74788d); }
            .reg-step.is-done .reg-step-dot { background: var(--bs-success, #34c38f); border-color: var(--bs-success, #34c38f); color: #fff; }
            .reg-step.is-done .reg-step-label { color: var(--bs-body-color, #495057); }
            .reg-step.is-current .reg-step-dot { border-color: var(--bs-primary, #556ee6); color: var(--bs-primary, #556ee6); box-shadow: 0 0 0 4px rgba(85, 110, 230, .15); }
            .reg-step.is-warning .reg-step-dot { border-color: var(--bs-warning, #f1b44c); background: var(--bs-warning, #f1b44c); color: #fff; }
            .reg-step.is-danger .reg-step-dot { border-color: var(--bs-danger, #f46a6a); background: var(--bs-danger, #f46a6a); color: #fff; }
            .reg-step:is(.is-current, .is-warning, .is-danger) .reg-step-label { color: var(--bs-body-color, #495057); }
            .reg-step.is-current .reg-step-hint { color: var(--bs-primary, #556ee6); font-weight: 500; }
            .reg-step.is-warning .reg-step-hint { color: #b07a12; font-weight: 500; }
            .reg-step.is-danger .reg-step-hint { color: var(--bs-danger, #f46a6a); font-weight: 500; }
            @media (max-width: 575.98px) {
                /* Di layar sempit jadi vertikal supaya label tidak terpotong. */
                .reg-stepper { flex-direction: column; gap: .75rem; }
                .reg-step { display: grid; grid-template-columns: 36px 1fr; column-gap: .75rem; row-gap: 0; text-align: left; align-items: center; padding: 0; }
                .reg-step-dot { grid-row: span 2; }
                .reg-step-label { align-self: end; }
                .reg-step-hint { align-self: start; }
                .reg-step + .reg-step::before { display: none; }
            }
        </style>
    @endpush
@endonce

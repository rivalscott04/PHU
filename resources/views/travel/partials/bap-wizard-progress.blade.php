@php
    $currentStep = $currentStep ?? 1;
    $steps = [
        1 => ['label' => 'Data Keberangkatan', 'icon' => 'bx-edit'],
        2 => ['label' => 'Upload PDF', 'icon' => 'bx-upload'],
        3 => ['label' => 'Review & Ajukan', 'icon' => 'bx-check-circle'],
    ];
@endphp

<nav class="bap-wizard-progress mb-4" aria-label="Langkah pengajuan BA Pemberangkatan">
    <ol class="bap-wizard-steps list-unstyled d-flex mb-0">
        @foreach ($steps as $num => $step)
            @php
                $isDone = $currentStep > $num;
                $isActive = $currentStep === $num;
            @endphp
            <li class="bap-wizard-step flex-fill text-center {{ $isDone ? 'is-done' : '' }} {{ $isActive ? 'is-active' : '' }}">
                <div class="bap-wizard-step-marker mx-auto">
                    @if ($isDone)
                        <i class="bx bx-check"></i>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <div class="bap-wizard-step-label small mt-2">
                    <i class="bx {{ $step['icon'] }} d-none d-md-inline"></i>
                    {{ $step['label'] }}
                </div>
            </li>
        @endforeach
    </ol>
</nav>

@once
    @push('styles')
        <style>
            .bap-wizard-steps {
                position: relative;
                padding: 0 0.5rem;
            }
            .bap-wizard-steps::before {
                content: '';
                position: absolute;
                top: 18px;
                left: 12%;
                right: 12%;
                height: 2px;
                background: var(--bs-border-color, #dee2e6);
                z-index: 0;
            }
            .bap-wizard-step {
                position: relative;
                z-index: 1;
            }
            .bap-wizard-step-marker {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 14px;
                background: #fff;
                border: 2px solid var(--bs-border-color, #dee2e6);
                color: var(--bs-secondary-color, #6c757d);
            }
            .bap-wizard-step.is-active .bap-wizard-step-marker {
                border-color: var(--bs-primary, #556ee6);
                background: var(--bs-primary, #556ee6);
                color: #fff;
            }
            .bap-wizard-step.is-done .bap-wizard-step-marker {
                border-color: var(--bs-success, #34c38f);
                background: var(--bs-success, #34c38f);
                color: #fff;
            }
            .bap-wizard-step.is-active .bap-wizard-step-label {
                color: var(--bs-primary, #556ee6);
                font-weight: 600;
            }
            .bap-wizard-step.is-done .bap-wizard-step-label {
                color: var(--bs-success, #34c38f);
            }
            @media (max-width: 576px) {
                .bap-wizard-step-label {
                    font-size: 0.7rem;
                }
                .bap-wizard-step-marker {
                    width: 30px;
                    height: 30px;
                    font-size: 12px;
                }
                .bap-wizard-steps::before {
                    top: 15px;
                }
            }
        </style>
    @endpush
@endonce

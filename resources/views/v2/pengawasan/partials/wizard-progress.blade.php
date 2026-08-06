@php
    $wizardSteps = [
        ['label' => 'Info Kunjungan', 'full' => 'Info Kunjungan'],
        ['label' => 'Pemeriksaan', 'full' => 'Isi Pemeriksaan'],
        ['label' => 'Temuan', 'full' => 'Temuan & Selesai'],
    ];
    $activeStep = $activeStep ?? 1;
@endphp

<div class="pengawasan-wizard-header mb-4">
    <p id="pengawasan-wizard-current-label" class="text-muted small mb-3">
        Langkah {{ $activeStep }} dari {{ count($wizardSteps) }}: {{ $wizardSteps[$activeStep - 1]['full'] }}
    </p>

    <nav id="pengawasan-wizard-progress" class="pengawasan-wizard-progress" aria-label="Langkah pemeriksaan pengawasan">
        <ol class="pengawasan-wizard-steps list-unstyled d-flex mb-0">
            @foreach ($wizardSteps as $index => $step)
                @php $stepNumber = $index + 1; @endphp
                <li class="pengawasan-wizard-step flex-fill text-center {{ $stepNumber === $activeStep ? 'is-active' : '' }} {{ $stepNumber < $activeStep ? 'is-done is-clickable' : '' }}"
                    data-step="{{ $stepNumber }}"
                    @if ($stepNumber < $activeStep) role="button" tabindex="0" @endif>
                    <div class="pengawasan-wizard-step-marker mx-auto">
                        <span class="pengawasan-wizard-step-number">{{ $stepNumber }}</span>
                        <i class="bx bx-check pengawasan-wizard-step-check"></i>
                    </div>
                    <div class="pengawasan-wizard-step-label">{{ $step['label'] }}</div>
                </li>
            @endforeach
        </ol>
    </nav>
</div>

<script>
    window.pengawasanWizardStepLabels = @json(array_column($wizardSteps, 'full'));
    window.pengawasanWizardActiveStep = {{ $activeStep }};
</script>

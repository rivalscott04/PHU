@php
    $wizardSteps = [
        ['label' => 'Pribadi', 'full' => 'Data Pribadi'],
        ['label' => 'Alamat', 'full' => 'Alamat'],
        ['label' => 'Kontak', 'full' => 'Kontak & Keluarga'],
        ['label' => 'Tambahan', 'full' => 'Data Tambahan'],
        ['label' => 'Paspor', 'full' => 'Paspor & Haji Khusus'],
        ['label' => 'Dokumen', 'full' => 'Upload Dokumen'],
        ['label' => 'Review', 'full' => 'Review'],
    ];
@endphp

<div class="jamaah-wizard-header mb-4">
    <p id="jamaah-wizard-current-label" class="jamaah-wizard-current-label text-muted small mb-3 d-md-none">
        Langkah 1 dari {{ count($wizardSteps) }}: {{ $wizardSteps[0]['full'] }}
    </p>

    <nav id="jamaah-wizard-progress" class="jamaah-wizard-progress" aria-label="Langkah pendaftaran jamaah haji khusus">
        <ol class="jamaah-wizard-steps list-unstyled d-flex mb-0">
            @foreach ($wizardSteps as $index => $step)
                <li class="jamaah-wizard-step flex-fill text-center {{ $index === 0 ? 'is-active' : '' }}"
                    data-step-index="{{ $index }}">
                    <div class="jamaah-wizard-step-marker mx-auto">
                        <span class="jamaah-wizard-step-number">{{ $index + 1 }}</span>
                        <i class="bx bx-check jamaah-wizard-step-check"></i>
                    </div>
                    <div class="jamaah-wizard-step-label">{{ $step['label'] }}</div>
                </li>
            @endforeach
        </ol>
    </nav>
</div>

<script>
    window.jamaahWizardStepLabels = @json(array_column($wizardSteps, 'full'));
</script>

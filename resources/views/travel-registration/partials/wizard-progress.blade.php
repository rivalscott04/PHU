@php
    $wizardSteps = [
        ['label' => 'Profil', 'full' => 'Profil Travel'],
        ['label' => 'Izin', 'full' => 'Izin Operasional'],
        ['label' => 'Akreditasi', 'full' => 'Data Akreditasi'],
        ['label' => 'Alamat', 'full' => 'Kontak & Alamat'],
        ['label' => 'Dokumen', 'full' => 'Upload Dokumen'],
        ['label' => 'Akun', 'full' => 'Akun PIC'],
        ['label' => 'Review', 'full' => 'Review & Kirim'],
    ];
@endphp

<div class="travel-wizard-header mb-4">
    <p id="travel-wizard-current-label" class="travel-wizard-current-label text-muted small mb-3">
        Langkah 1 dari {{ count($wizardSteps) }}: {{ $wizardSteps[0]['full'] }}
    </p>

    <nav id="travel-wizard-progress" class="travel-wizard-progress" aria-label="Langkah registrasi travel">
        <ol class="travel-wizard-steps list-unstyled d-flex mb-0">
            @foreach ($wizardSteps as $index => $step)
                <li class="travel-wizard-step flex-fill text-center {{ $index === 0 ? 'is-active' : '' }}"
                    data-step-index="{{ $index }}">
                    <div class="travel-wizard-step-marker mx-auto">
                        <span class="travel-wizard-step-number">{{ $index + 1 }}</span>
                        <i class="bx bx-check travel-wizard-step-check"></i>
                    </div>
                    <div class="travel-wizard-step-label">{{ $step['label'] }}</div>
                </li>
            @endforeach
        </ol>
    </nav>
</div>

<script>
    window.travelWizardStepLabels = @json(array_column($wizardSteps, 'full'));
</script>

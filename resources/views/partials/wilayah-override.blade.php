@props([
    'buttonId',
    'panelId',
    'inputId',
    'title',
    'label',
    'placeholder',
    'triggerLabel' => 'Tidak ada di daftar? Isi manual',
])

<button type="button"
    class="btn btn-link btn-sm px-0 mt-1 wilayah-override-trigger"
    id="{{ $buttonId }}"
    disabled>
    <i class="bx bx-edit-alt me-1"></i>{{ $triggerLabel }}
</button>

<div id="{{ $panelId }}" class="wilayah-override-panel d-none mt-2" aria-hidden="true">
    <div class="wilayah-override-panel__body">
        <p class="wilayah-override-panel__heading mb-2">
            <i class="bx bx-edit-alt me-1"></i>{{ $title }}
        </p>
        <label for="{{ $inputId }}" class="form-label wilayah-override-panel__label">{{ $label }}</label>
        <input type="text"
            class="form-control"
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            autocomplete="off">
        <small class="text-muted d-block mt-2">
            Pastikan ejaan sesuai alamat resmi (KTP/KK).
        </small>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-3 wilayah-override-cancel">
            Kembali ke daftar
        </button>
    </div>
</div>

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
    class="btn btn-link btn-sm px-0 mt-1"
    id="{{ $buttonId }}"
    disabled>
    <i class="bx bx-edit-alt me-1"></i>{{ $triggerLabel }}
</button>

<div id="{{ $panelId }}" class="alert alert-warning mt-2 mb-0 d-none" aria-hidden="true">
    <h6 class="alert-heading mb-2">
        <i class="bx bx-edit-alt me-1"></i>{{ $title }}
    </h6>
    <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>
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

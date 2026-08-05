@props(['url', 'path', 'label'])

@if (Str::endsWith(strtolower($path), '.pdf'))
    <button type="button" class="btn btn-xs btn-outline-primary btn-sm"
        onclick="openPdfPreview('{{ $url }}', '{{ $label }}')">
        <i class="bx bx-file-find me-1"></i> {{ $label }}
    </button>
@else
    <button type="button" class="btn btn-xs btn-outline-primary btn-sm"
        onclick="openImagePreview('{{ $url }}', '{{ $label }}')">
        <i class="bx bx-image me-1"></i> {{ $label }}
    </button>
@endif

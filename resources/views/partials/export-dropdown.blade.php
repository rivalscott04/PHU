@props([
    'excelUrl',
    'pdfUrl' => null,
    'label' => 'Unduh Data',
    'buttonClass' => 'btn-info',
])

<div class="btn-group">
    <button type="button" class="btn btn-sm {{ $buttonClass }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-export me-1"></i> {{ $label }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="{{ $excelUrl }}">
                <i class="bx bx-file me-2"></i> Excel
            </a>
        </li>
        @if($pdfUrl)
            <li>
                <a class="dropdown-item" href="{{ $pdfUrl }}">
                    <i class="bx bx-file-pdf me-2"></i> PDF
                </a>
            </li>
        @endif
    </ul>
</div>

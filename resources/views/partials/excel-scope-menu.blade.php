@props([
    'exportRoute',
    'buttonClass' => 'btn-outline-primary',
    'menuId',
])

@php
    $shared = request()->only(['filter', 'search', 'per_page', 'page']);
    $pageUrl = route($exportRoute, array_merge($shared, ['scope' => 'page']));
    $allUrl = route($exportRoute, array_merge($shared, ['scope' => 'all']));
@endphp

<div class="btn-group" id="{{ $menuId }}">
    <button type="button" class="btn btn-sm {{ $buttonClass }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-download me-1"></i> Export Excel
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" data-excel-scope="page" href="{{ $pageUrl }}">
                <span class="d-block">Halaman ini</span>
                <small class="text-muted">Baris yang sedang tampil</small>
            </a>
        </li>
        <li>
            <a class="dropdown-item" data-excel-scope="all" href="{{ $allUrl }}">
                <span class="d-block">Semua data</span>
                <small class="text-muted">Seluruh hasil pada tampilan ini</small>
            </a>
        </li>
    </ul>
</div>

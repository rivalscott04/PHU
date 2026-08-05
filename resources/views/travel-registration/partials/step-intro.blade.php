@php
    $icon = $icon ?? 'bx-info-circle';
    $title = $title ?? '';
    $description = $description ?? '';
@endphp

<div class="alert alert-info mb-4 col-lg-8 mx-auto">
    <h6 class="alert-heading"><i class="bx {{ $icon }} me-1"></i>{{ $title }}</h6>
    <p class="mb-0">{{ $description }}</p>
</div>

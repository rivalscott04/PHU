@php
    $icon = $icon ?? 'bx-info-circle';
    $title = $title ?? '';
    $description = $description ?? '';
@endphp

<div class="travel-step-intro mb-4">
    <div class="travel-step-intro-icon">
        <i class="bx {{ $icon }}"></i>
    </div>
    <div>
        <h5 class="travel-step-intro-title mb-1">{{ $title }}</h5>
        <p class="travel-step-intro-desc mb-0">{{ $description }}</p>
    </div>
</div>

{{-- Badge ringkas indeks kepercayaan untuk kartu daftar --}}
@php
    $trust = $trust ?? [];
    $compact = $compact ?? false;
    $hasData = $trust['has_data'] ?? false;
@endphp

@if ($hasData)
    <div
        class="trust-badge trust-badge--{{ $trust['bg_class'] ?? 'secondary' }} {{ $compact ? 'trust-badge--compact' : '' }}"
        role="status"
        aria-label="Tingkat kepercayaan: {{ $trust['label'] ?? 'Tidak diketahui' }}"
    >
        <span class="trust-badge__stars" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fa{{ $i <= ($trust['stars'] ?? 0) ? 's' : 'r' }} fa-star"></i>
            @endfor
        </span>
        <span class="trust-badge__label">{{ $trust['label'] ?? 'Belum diketahui' }}</span>
    </div>
@else
    <div class="trust-badge trust-badge--muted {{ $compact ? 'trust-badge--compact' : '' }}" role="status">
        <i class="fas fa-circle-info me-1" aria-hidden="true"></i>
        <span class="trust-badge__label">Belum ada data indeks</span>
    </div>
@endif

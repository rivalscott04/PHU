@php
    use App\Support\RouteAccess;
    use Illuminate\Support\Str;

    $compact = $compact ?? false;
    $showActions = $showActions ?? true;
    $expanded = $expanded ?? false;
    $accordionId = $accordionId ?? 'workflow-'.substr(md5(($guide['title'] ?? '').($guide['hint'] ?? '')), 0, 8);
@endphp

@if($compact)
    <div class="text-primary small mt-1">
        <i class="bx bx-directions me-1"></i>{{ $guide['hint'] }}
    </div>
@else
    <div class="accordion workflow-guide-accordion mb-3" id="accordion-{{ $accordionId }}">
        <div class="accordion-item border-0 shadow-sm border-start border-4 border-primary overflow-hidden">
            <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                <button
                    class="accordion-button {{ $expanded ? '' : 'collapsed' }} py-3 shadow-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{ $accordionId }}"
                    aria-expanded="{{ $expanded ? 'true' : 'false' }}"
                    aria-controls="collapse-{{ $accordionId }}"
                >
                    <span class="d-flex flex-column flex-md-row align-items-md-center gap-1 gap-md-2 w-100 pe-2">
                        <span class="fw-semibold text-body">
                            <i class="bx bx-directions me-1 text-primary"></i>{{ $guide['title'] }}
                        </span>
                        <span class="text-muted small fw-normal">{{ Str::limit($guide['hint'], 90) }}</span>
                    </span>
                </button>
            </h2>
            <div
                id="collapse-{{ $accordionId }}"
                class="accordion-collapse collapse {{ $expanded ? 'show' : '' }}"
                aria-labelledby="heading-{{ $accordionId }}"
            >
                <div class="accordion-body pt-0">
                    <p class="text-muted mb-3">{{ $guide['hint'] }}</p>
                    <ol class="mb-0 ps-3">
                        @foreach ($guide['steps'] as $step)
                            <li class="mb-1">{{ $step }}</li>
                        @endforeach
                    </ol>
                    @if($showActions && ! empty($guide['actions']))
                        <hr class="my-3">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($guide['actions'] as $action)
                                @if(RouteAccess::canAccessUrl($action['url']))
                                    <a href="{{ $action['url'] }}" class="btn btn-sm btn-{{ $action['style'] }}">
                                        <i class="bx {{ $action['icon'] }} me-1"></i>{{ $action['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@once
    @push('styles')
        <style>
            .workflow-guide-accordion .accordion-button:not(.collapsed) {
                background-color: rgba(85, 110, 230, 0.06);
                color: inherit;
            }
            .workflow-guide-accordion .accordion-button:focus {
                box-shadow: none;
                border-color: transparent;
            }
            .workflow-guide-accordion .accordion-button::after {
                flex-shrink: 0;
            }
        </style>
    @endpush
@endonce

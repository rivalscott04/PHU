@php
    use App\Enums\UserRole;
    use App\Services\TravelCapabilityService;
    use App\Support\RouteAccess;
    use App\Support\SidebarBadges;

    $menus = TravelCapabilityService::getSidebarMenus();
    $sidebarUser = auth()->user();
    $sidebarBadges = $sidebarUser ? SidebarBadges::forUser($sidebarUser) : [];

    $renderBadge = static function (?string $badgeKey) use ($sidebarBadges): string {
        if (! $badgeKey) {
            return '';
        }

        $count = (int) ($sidebarBadges[$badgeKey] ?? 0);

        if ($count <= 0) {
            return '';
        }

        $label = $count > 99 ? '99+' : (string) $count;

        return '<span class="badge rounded-pill bg-danger sidebar-menu-badge">'.$label.'</span>';
    };

    $canShowItem = static function (array $item) use ($sidebarUser): bool {
        if (! ($item['visible'] ?? true) || ! $sidebarUser) {
            return false;
        }

        return RouteAccess::canAccessRoute(
            $sidebarUser,
            $item['route'],
            $item['params'] ?? []
        );
    };

    $itemUrl = static function (array $item): string {
        return route($item['route'], $item['params'] ?? []);
    };
@endphp

<style>
@media (max-width: 768px) {
    .vertical-menu {
        width: 100% !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 1000 !important;
        height: 100vh !important;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .vertical-menu.show {
        transform: translateX(0);
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }

    .sidebar-overlay.show {
        display: block;
    }
}

#sidebar-menu .metismenu {
    padding: 0.25rem 0;
}

#sidebar-menu .metismenu > li {
    margin: 0;
}

#sidebar-menu .metismenu > li > a {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    padding: 0.45rem 1.1rem !important;
    font-size: 0.875rem;
    line-height: 1.3;
    border-radius: 6px;
    margin: 1px 0.5rem;
}

#sidebar-menu .metismenu > li > a i {
    flex-shrink: 0;
    width: 1.15rem;
    margin-right: 0.55rem !important;
    font-size: 1.05rem;
    line-height: 1;
}

#sidebar-menu .metismenu > li > a .sidebar-menu-text {
    flex: 1 1 auto;
    min-width: 0;
}

#sidebar-menu .metismenu .has-arrow::after {
    transition: transform 0.3s ease;
    color: rgba(255,255,255,0.7) !important;
    top: 50%;
    transform: translateY(-50%) rotate(-90deg);
}

#sidebar-menu .metismenu .has-arrow[aria-expanded="true"]::after {
    transform: translateY(-50%) rotate(0deg);
    color: white !important;
}

#sidebar-menu .metismenu .sub-menu {
    transition: all 0.3s ease;
    overflow: hidden;
    padding-left: 0.35rem;
}

#sidebar-menu .metismenu .sub-menu li a {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    border-radius: 6px;
    margin: 1px 0.5rem;
    padding: 0.4rem 0.85rem 0.4rem 2rem !important;
    font-size: 0.78rem;
    line-height: 1.25;
    gap: 0.35rem;
}

#sidebar-menu .metismenu .sub-menu li a i {
    display: none;
}

#sidebar-menu .metismenu .sub-menu li a:hover,
#sidebar-menu .metismenu > li > a:hover {
    background-color: rgba(255,255,255,0.08);
}

#sidebar-menu .metismenu .sub-menu .menu-heading {
    padding: 0.45rem 1rem 0.15rem 2rem;
    font-size: 0.625rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.4);
    pointer-events: none;
    user-select: none;
    margin: 0;
}

#sidebar-menu .metismenu .sub-menu .menu-heading:not(:first-child) {
    margin-top: 0.2rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
}

.sidebar-scope-banner {
    margin: 0.5rem 0.5rem 0.35rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-menu-badge {
    font-size: 0.625rem;
    min-width: 1.1rem;
    padding: 0.15em 0.4em;
    line-height: 1.1;
    flex-shrink: 0;
    margin-left: auto;
}

.sidebar-menu-label {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-width: 0;
}

.sidebar-menu-hint {
    display: block;
    font-size: 0.625rem;
    color: rgba(255, 255, 255, 0.4);
    line-height: 1.2;
    margin-top: 0.05rem;
    white-space: normal;
}
</style>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            @if($sidebarUser && in_array($sidebarUser->role, [UserRole::Pengawas->value, UserRole::Kabupaten->value], true))
                <div class="sidebar-scope-banner">
                    <small class="text-white-50 d-block mb-1">
                        {{ $sidebarUser->role === UserRole::Pengawas->value ? 'Wilayah kerja' : 'Wilayah Anda' }}
                    </small>
                    <span class="text-white small fw-medium">
                        {{ $sidebarUser->role === UserRole::Pengawas->value
                            ? $sidebarUser->getWilayahKerjaLabel()
                            : ($sidebarUser->kabupaten ?? 'Kabupaten/Kota') }}
                    </span>
                </div>
            @endif

            <ul class="metismenu list-unstyled" id="side-menu">
                @foreach($menus as $menu)
                    @if(isset($menu['hasSubmenu']) && $menu['hasSubmenu'])
                        @php
                            $visibleItems = [];
                            if (isset($menu['groups'])) {
                                foreach ($menu['groups'] as $group) {
                                    foreach ($group['items'] as $item) {
                                        if ($canShowItem($item)) {
                                            $visibleItems[] = $item;
                                        }
                                    }
                                }
                            } else {
                                foreach ($menu['items'] ?? [] as $item) {
                                    if ($canShowItem($item)) {
                                        $visibleItems[] = $item;
                                    }
                                }
                            }
                        @endphp

                        @if($visibleItems !== [])
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    <span class="sidebar-menu-text">{{ $menu['name'] }}</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    @if(isset($menu['groups']))
                                        @foreach($menu['groups'] as $group)
                                            @php
                                                $visibleGroupItems = array_values(array_filter(
                                                    $group['items'],
                                                    fn ($item) => $canShowItem($item)
                                                ));
                                            @endphp
                                            @if($visibleGroupItems !== [])
                                                <li class="menu-heading">{{ $group['label'] }}</li>
                                                @foreach($visibleGroupItems as $item)
                                                    <li>
                                                        <a href="{{ $itemUrl($item) }}">
                                                            <span class="sidebar-menu-label">
                                                                <span>{{ $item['name'] }}</span>
                                                                @if(! empty($item['hint']))
                                                                    <small class="sidebar-menu-hint">{{ $item['hint'] }}</small>
                                                                @endif
                                                            </span>
                                                            {!! $renderBadge($item['badge'] ?? null) !!}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($menu['items'] as $item)
                                            @if($canShowItem($item))
                                                <li>
                                                    <a href="{{ $itemUrl($item) }}">
                                                        <span class="sidebar-menu-label">
                                                            <span>{{ $item['name'] }}</span>
                                                            @if(! empty($item['hint']))
                                                                <small class="sidebar-menu-hint">{{ $item['hint'] }}</small>
                                                            @endif
                                                        </span>
                                                        {!! $renderBadge($item['badge'] ?? null) !!}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @elseif($canShowItem($menu))
                        <li>
                            <a href="{{ $itemUrl($menu) }}" class="waves-effect">
                                <i class="{{ $menu['icon'] }}"></i>
                                <span class="sidebar-menu-text">{{ $menu['name'] }}</span>
                                {!! $renderBadge($menu['badge'] ?? null) !!}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.navbar-toggle');
    const sidebar = document.querySelector('.vertical-menu');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    const menuItems = document.querySelectorAll('.metismenu a[href^="{{ url("/") }}"]');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });
});
</script>

@push('js')
<script>
    (function ($) {
        var $menu = $('#side-menu');

        if (!$menu.length || typeof $.fn.metisMenu !== 'function') {
            return;
        }

        if ($menu.data('metisMenu')) {
            $menu.metisMenu('dispose');
        }

        $menu.metisMenu({ toggle: true });
    })(jQuery);
</script>
@endpush

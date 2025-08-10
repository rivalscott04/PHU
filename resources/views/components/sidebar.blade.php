@php
    use App\Services\TravelCapabilityService;
    $menus = TravelCapabilityService::getSidebarMenus();
@endphp

<style>
/* Mobile Responsive Accordion Styles */
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
    
    .metismenu .has-arrow {
        padding: 12px 15px !important;
        font-size: 14px !important;
    }
    
    .metismenu .sub-menu {
        padding-left: 20px !important;
    }
    
    .metismenu .sub-menu li a {
        padding: 10px 15px !important;
        font-size: 13px !important;
    }
    
    /* Mobile overlay */
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

/* Accordion Animation */
.metismenu .sub-menu {
    transition: all 0.3s ease;
    overflow: hidden;
}

/* Arrow styles for Skote's built-in accordion arrows */
.metismenu .has-arrow::after {
    transition: transform 0.3s ease;
    color: rgba(255,255,255,0.7) !important;
}

.metismenu .has-arrow[aria-expanded="true"]::after {
    transform: rotate(180deg);
    color: white !important;
}

/* Force white color for active accordion arrows - highest specificity */
#sidebar-menu .metismenu .has-arrow[aria-expanded="true"]::after,
.vertical-menu .metismenu .has-arrow[aria-expanded="true"]::after,
body .vertical-menu .metismenu .has-arrow[aria-expanded="true"]::after {
    color: white !important;
}

/* Compact Design */
.metismenu .has-arrow {
    border-radius: 8px;
    margin: 2px 8px;
}

.metismenu .sub-menu li a {
    border-radius: 6px;
    margin: 1px 4px;
    transition: all 0.2s ease;
}

.metismenu .sub-menu li a:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateX(5px);
}
</style>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                @foreach($menus as $menu)
                    @if(isset($menu['hasSubmenu']) && $menu['hasSubmenu'])
                        <!-- Accordion Menu with Submenu -->
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="{{ $menu['icon'] }}"></i>
                                <span>{{ $menu['name'] }}</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @foreach($menu['items'] as $item)
                                    @if($item['visible'])
                                        <li>
                                            <a href="{{ route($item['route']) }}">
                                                <i class="{{ $item['icon'] }}"></i>
                                                <span>{{ $item['name'] }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>

                    @else
                        <!-- Single Menu Item -->
                        @if($menu['visible'])
                            <li>
                                <a href="{{ route($menu['route']) }}" class="waves-effect">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    @if(isset($menu['badge']))
                                        <span class="badge rounded-pill bg-info float-end">{{ $menu['badge'] }}</span>
                                    @endif
                                    <span>{{ $menu['name'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>

<!-- Mobile Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
// Mobile sidebar toggle
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
    
    // Close sidebar when clicking on menu items on mobile
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

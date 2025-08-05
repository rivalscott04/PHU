@php
    use App\Services\TravelCapabilityService;
    $menus = TravelCapabilityService::getSidebarMenus();
@endphp

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                @foreach($menus as $menu)
                    @if(isset($menu['items']))
                        <!-- Menu Group -->
                        <li class="menu-title" key="t-menu">{{ $menu['name'] }}</li>
                        @foreach($menu['items'] as $item)
                            @if($item['visible'])
                                <li>
                                    <a href="{{ route($item['route']) }}" class="waves-effect">
                                        <i class="{{ $item['icon'] }}"></i>
                                        @if(isset($item['badge']))
                                            <span class="badge rounded-pill bg-info float-end">{{ $item['badge'] }}</span>
                                        @endif
                                        <span key="t-dashboards">{{ $item['name'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <!-- Single Menu Item -->
                        @if($menu['visible'])
                            <li>
                                <a href="{{ route($menu['route']) }}" class="waves-effect">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    @if(isset($menu['badge']))
                                        <span class="badge rounded-pill bg-info float-end">{{ $menu['badge'] }}</span>
                                    @endif
                                    <span key="t-dashboards">{{ $menu['name'] }}</span>
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

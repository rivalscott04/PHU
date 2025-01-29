<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                    <li class="menu-title" key="t-menu">Menu</li>
                    <li>
                        <a href="{{ route('home') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span class="badge rounded-pill bg-info float-end">04</span>
                            <span key="t-dashboards">Dashboards</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('travel') }}" class="waves-effect">
                            <i class="bx bxs-plane-alt"></i>
                            <span key="t-dashboards">Data Travel</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cabang.travel') }}" class="waves-effect">
                            <i class="bx bxs-business"></i>
                            <span key="t-dashboards">Data Cabang Travel</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('travels') }}" class="waves-effect">
                            <i class="bx bx-user-plus"></i>
                            <span key="t-dashboards">Akun Travel</span>
                        </a>
                    </li>
                @endif
            </ul>
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">Travel</li>
                <li>
                    <a href="{{ route('bap') }}" class="waves-effect">
                        <i class="bx bx-list-ul"></i>
                        <span key="t-dashboards">Data BAP</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jamaah') }}" class="waves-effect">
                        <i class="bx bxs-group"></i>
                        <span key="t-dashboards">Jamaah</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>

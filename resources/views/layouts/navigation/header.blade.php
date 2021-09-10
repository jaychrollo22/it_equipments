<!--begin::Header-->
<div id="kt_header" class="header header-fixed">
    <!--begin::Container-->
    <div class="container d-flex align-items-stretch justify-content-between">
        <!--begin::Left-->
        <div class="d-flex align-items-stretch mr-3">
            <!--begin::Header Logo-->
            <div class="header-logo">
                <a href="{{ url('/home') }}">
                    {{-- <img alt="Logo" src="img/lfuggoc_logo.png" class="logo-default max-h-40px" /> --}}
                    <img alt="Logo" src="img/lfuggoc_logo.png" class="logo-sticky max-h-40px" />
                </a>
            </div>
            <!--end::Header Logo-->
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                <!--begin::Header Menu-->
                <div id="kt_header_menu" class="header-menu header-menu-left header-menu-mobile header-menu-layout-default">
                    <!--begin::Header Nav-->
                    <ul class="menu-nav">
                        {{-- Dashboard --}}
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('/home') }}" class="menu-link">
                                <span class="menu-text">Dashboard</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        @endif
                        {{-- Inventories --}}
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-text">Inventories</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                <ul class="menu-subnav">
                                    @if(session('user_role') == "Administrator")
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('inventories') }}" class="menu-link">
                                                <span class="menu-text">Masterlist</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('inventory-transfer') }}" class="menu-link" disabled>
                                                <span class="menu-text">Transfer</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                    @endif
                                    @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                                    <li class="menu-item" aria-haspopup="true">
                                        <a href="{{ url('inventory-receive') }}" class="menu-link" disabled>
                                            <span class="menu-text">Receive</span>
                                            <span class="menu-desc"></span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                        @endif
                        {{-- Settings --}}
                        @if(session('user_role') == "Administrator")
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">Settings</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('setting-buildings') }}" class="menu-link">
                                                <span class="menu-text">Buildings</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('setting-categories') }}" class="menu-link">
                                                <span class="menu-text">Categories</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('setting-companies') }}" class="menu-link">
                                                <span class="menu-text">Companies</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('setting-departments') }}" class="menu-link">
                                                <span class="menu-text">Departments</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('setting-locations') }}" class="menu-link">
                                                <span class="menu-text">Locations</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('setting-types') }}" class="menu-link">
                                                <span class="menu-text">Types</span>
                                                <span class="menu-desc"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        {{-- Users --}}
                        @if(session('user_role') == "Administrator")
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('users') }}" class="menu-link">
                                <span class="menu-text">Users</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        @endif
                        {{-- Reports --}}
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                            <a href="javascript:;" class="menu-link menu-toggle">
                                <span class="menu-text">Reports</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                <ul class="menu-subnav">
                                    <li class="menu-item" aria-haspopup="true">
                                        <a href="{{ url('reports-asset-logs') }}" class="menu-link">
                                            <span class="menu-text">Asset Logs</span>
                                            <span class="menu-desc"></span>
                                        </a>
                                    </li>
                                    <li class="menu-item" aria-haspopup="true">
                                        <a href="{{ url('reports-borrow-logs') }}" class="menu-link">
                                            <span class="menu-text">Borrow Logs</span>
                                            <span class="menu-desc"></span>
                                        </a>
                                    </li>
                                    <li class="menu-item" aria-haspopup="true">
                                        <a href="{{ url('reports-return-logs') }}" class="menu-link">
                                            <span class="menu-text">Return Logs</span>
                                            <span class="menu-desc"></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        {{-- Activity Logs --}}
                        @if(session('user_role') == "Administrator")
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('activity-logs') }}" class="menu-link">
                                <span class="menu-text">Activity Logs</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        @endif
                        {{-- Items --}}
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('items') }}" target="_blank" class="menu-link">
                                <span class="menu-text">Items</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                    <!--end::Header Nav-->
                </div>
                <!--end::Header Menu-->
            </div>
            <!--end::Header Menu Wrapper-->
        </div>
        <!--end::Left-->
        <!--begin::Topbar-->
        <div class="topbar">
            <!--begin::User-->
            <div class="dropdown">
                <!--begin::Toggle-->
                <div class="topbar-item">
                    <div class="btn btn-icon btn-hover-transparent-white d-flex align-items-center btn-lg px-md-2 w-md-auto" id="kt_quick_user_toggle">
                        <span class="text-white opacity-70 font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span class="text-white opacity-90 font-weight-bolder font-size-base d-none d-md-inline mr-4">{{ Auth::user()->name }}</span>
                        <span class="symbol symbol-35">
                            <span class="symbol-label text-white font-size-h5 font-weight-bold bg-white-o-30">{{ substr(session('user.first_name'),0,1) }}</span>
                        </span>
                    </div>
                </div>
                <!--end::Toggle-->
            </div>
            <!--end::User-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
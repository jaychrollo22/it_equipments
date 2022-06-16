<div id="kt_header" class="header header-fixed">
    <div class="container d-flex align-items-stretch justify-content-between">
        <div class="d-flex align-items-stretch mr-3">
            <div class="header-logo">
                <a href="{{ url('/home') }}">
                    <img alt="Logo" src="img/lfuggoc_logo.png" class="logo-sticky max-h-40px" />
                </a>
            </div>
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                <div id="kt_header_menu" class="header-menu header-menu-left header-menu-mobile header-menu-layout-default">
                    <ul class="menu-nav">
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('/home-user') }}" class="menu-link">
                                <span class="menu-text">Assigned/Borrowed</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('/home-borrow-requests') }}" class="menu-link">
                                <span class="menu-text">Borrow Requests</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        <li class="menu-item menu-item-submenu menu-item-rel">
                            <a href="{{ url('/home-return-requests') }}" class="menu-link">
                                <span class="menu-text">Return Requests</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="topbar">
            <div class="dropdown">
                <div class="topbar-item">
                    <div class="btn btn-icon btn-hover-transparent-white d-flex align-items-center btn-lg px-md-2 w-md-auto" id="kt_quick_user_toggle">
                        <span class="text-white opacity-70 font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span class="text-white opacity-90 font-weight-bolder font-size-base d-none d-md-inline mr-4">{{ Auth::user()->name }}</span>
                        <span class="symbol symbol-35">
                            <span class="symbol-label text-white font-size-h5 font-weight-bold bg-white-o-30">{{ substr(session('user.first_name'),0,1) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

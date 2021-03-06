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
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support" || session('user_role') == "Finance")
                            @if(session('user_permissions.inventory_masterlist') == 'true' 
                                || session('user_permissions.inventory_transfer_location') == 'true'
                                || session('user_permissions.inventory_receive_transfer') == 'true'
                                || session('user_permissions.report_for_disposal') == 'true'
                            )
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">Inventories</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            @if(session('user_permissions.inventory_masterlist') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('inventories') }}" class="menu-link">
                                                        <span class="menu-text">Masterlist</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.inventory_transfer_location') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('inventory-transfer') }}" class="menu-link" disabled>
                                                        <span class="menu-text">Transfer</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.inventory_receive_transfer') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('inventory-receive') }}" class="menu-link" disabled>
                                                        <span class="menu-text">Receive Transfer</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_for_disposal') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('for-disposal') }}" class="menu-link" disabled>
                                                        <span class="menu-text">For Disposal</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.inventory_for_maintenance') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('for-maintenance') }}" class="menu-link" disabled>
                                                        <span class="menu-text">For Maintenance</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        {{-- Reports --}}
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                            @if(session('user_permissions.report_employee_asset') == 'true' 
                                || session('user_permissions.report_asset_handover_form') == 'true' 
                                || session('user_permissions.report_borrowed_inventories') == 'true' 
                                || session('user_permissions.report_returned_inventories') == 'true'
                                || session('user_permissions.report_disposed_logs') == 'true'
                            )
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">Reports</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            @if(session('user_permissions.report_employee_asset') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-asset-logs') }}" class="menu-link">
                                                        <span class="menu-text">Employee Assets</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_employee_asset') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-letter-of-undertaking') }}" class="menu-link">
                                                        <span class="menu-text">Asset Letter of Undertaking</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_employee_asset') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-asset-user-history') }}" class="menu-link">
                                                        <span class="menu-text">Asset History</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_asset_handover_form') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-asset-handover-forms') }}" class="menu-link">
                                                        <span class="menu-text">Asset Handover Forms</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_borrowed_inventories') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-borrow-logs') }}" class="menu-link">
                                                        <span class="menu-text">Borrowed Inventories</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_returned_inventories') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-return-logs') }}" class="menu-link">
                                                        <span class="menu-text">Returned Inventories</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.report_disposed_logs') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('reports-disposed-logs') }}" class="menu-link">
                                                        <span class="menu-text">Disposed Logs</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        {{-- Settings --}}
                        @if(session('user_role') == "Administrator")
                            @if(session('user_permissions.settings') == 'true')
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
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('setting-system-approvers') }}" class="menu-link">
                                                    <span class="menu-text">System Approvers</span>
                                                    <span class="menu-desc"></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        {{-- RFID --}}
                        @if(session('user_role') == "Administrator")
                            @if(session('user_permissions.rfid_registration_devices') == 'true')
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">RFID</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('rfid-registration-devices') }}" class="menu-link">
                                                    <span class="menu-text">Registration Devices</span>
                                                    <span class="menu-desc"></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        {{-- Users --}}
                        @if(session('user_role') == "Administrator")
                            @if(session('user_permissions.users') == 'true')
                                <li class="menu-item menu-item-submenu menu-item-rel">
                                    <a href="{{ url('users') }}" class="menu-link">
                                        <span class="menu-text">Users</span>
                                        <span class="menu-desc"></span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                </li>
                            @endif
                        @endif
                        
                        {{-- Activity Logs --}}
                        @if(session('user_role') == "Administrator")
                            @if(session('user_permissions.activity_logs') == 'true')
                                <li class="menu-item menu-item-submenu menu-item-rel">
                                    <a href="{{ url('activity-logs') }}" class="menu-link">
                                        <span class="menu-text">Activity Logs</span>
                                        <span class="menu-desc"></span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                </li>
                            @endif
                        @endif
                        {{-- Items --}}
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                            @if(session('user_permissions.items') == 'true')
                                <li class="menu-item menu-item-submenu menu-item-rel">
                                    <a href="{{ url('items') }}" target="_blank" class="menu-link">
                                        <span class="menu-text">Items</span>
                                        <span class="menu-desc"></span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                </li>
                            @endif
                        @endif
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                            @if(session('user_permissions.requests_borrow') == 'true' || session('user_permissions.requests_return') == 'true')
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">Requests</span>
                                        <i class="menu-arrow"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            @if(session('user_permissions.requests_borrow') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('borrow-requests') }}" class="menu-link">
                                                        <span class="menu-text">Borrow Requests</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(session('user_permissions.requests_return') == 'true')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('return-requests') }}" class="menu-link">
                                                        <span class="menu-text">Return Requests</span>
                                                        <span class="menu-desc"></span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                            @endif
                        @endif
                        @if(session('user_role') == "Administrator" || session('user_role') == "IT Support")
                            <li class="menu-item menu-item-submenu menu-item-rel">
                                <a href="{{ url('qr-scanner') }}" target="_blank" class="menu-link">
                                    <span class="menu-text">QR SCANNER</span>
                                    <span class="menu-desc"></span>
                                    <i class="menu-arrow"></i>
                                </a>
                            </li>
                        @endif
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
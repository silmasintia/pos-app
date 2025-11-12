    <aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
        <div class="sidebar-header d-flex align-items-center justify-content-start">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <div class="logo-main">
                    <div class="logo-normal">
                        <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2"
                                transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                            <rect x="7.72803" y="27.728" width="28" height="4" rx="2"
                                transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                            <rect x="10.5366" y="16.3945" width="16" height="4" rx="2"
                                transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                            <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2"
                                transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="logo-mini">
                        <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2"
                                transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                            <rect x="7.72803" y="27.728" width="28" height="4" rx="2"
                                transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                            <rect x="10.5366" y="16.3945" width="16" height="4" rx="2"
                                transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                            <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2"
                                transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                        </svg>
                    </div>
                </div>
                <h4 class="logo-title">Cashify</h4>
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </i>
            </div>
        </div>
        <div class="sidebar-body pt-0 data-scrollbar">
            <div class="sidebar-list">
                <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('dashboard') }}">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="icon-20">
                                    <path opacity="0.4"
                                        d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                        fill="currentColor"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                        fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <hr class="hr-horizontal">
                    </li>

                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Master Data</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('users.index') }}">
                            <i class="icon">
                                <i class="fas fa-users"></i>
                            </i>
                            <span class="item-name">Users</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('product-management*') ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#product-management" role="button"
                            aria-expanded="{{ request()->is('product-management*') ? 'true' : 'false' }}"
                            aria-controls="product-management">
                            <i class="icon">
                                <i class="fas fa-box-open"></i>
                            </i>
                            <span class="item-name">Catalog</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ request()->is('product-management*') ? 'show' : '' }}"
                            id="product-management" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('product-management/categories*') ? 'active' : '' }}"
                                    href="{{ route('product-management.categories.index') }}">
                                    <svg class="icon-10 me-2" xmlns="http://www.w3.org/2000/svg" width="10"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="8"></circle>
                                    </svg>
                                    Categories
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('product-management/products*') ? 'active' : '' }}"
                                    href="{{ route('product-management.products.index') }}">
                                    <svg class="icon-10 me-2" xmlns="http://www.w3.org/2000/svg" width="10"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="8"></circle>
                                    </svg>
                                    Products
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('customers*') || request()->is('suppliers*') ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#contacts-management" role="button"
                            aria-expanded="{{ request()->is('customers*') || request()->is('suppliers*') ? 'true' : 'false' }}"
                            aria-controls="contacts-management">
                            <i class="icon">
                                <i class="fas fa-user-friends"></i>
                            </i>
                            <span class="item-name">Contacts</span>
                            <i class="right-icon">
                                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>
                        <ul class="sub-nav collapse {{ request()->is('customers*') || request()->is('suppliers*') ? 'show' : '' }}"
                            id="contacts-management" data-bs-parent="#sidebar-menu">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}"
                                    href="{{ route('customers.index') }}">
                                    <svg class="icon-10 me-2" xmlns="http://www.w3.org/2000/svg" width="10"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="8"></circle>
                                    </svg>
                                    Customers
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}"
                                    href="{{ route('suppliers.index') }}">
                                    <svg class="icon-10 me-2" xmlns="http://www.w3.org/2000/svg" width="10"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="8"></circle>
                                    </svg>
                                    Suppliers
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <hr class="hr-horizontal">
                    </li>

                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Transaction</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sales*') ? 'active' : '' }}"
                            href="{{ route('sales.index') }}">
                            <i class="icon">
                                <i class="fas fa-cash-register"></i>
                            </i>
                            <span class="item-name">Sales (POS)</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('purchases*') ? 'active' : '' }}"
                            href="{{ route('purchases.index') }}">
                            <i class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </i>
                            <span class="item-name">Purchases</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}"
                            href="{{ route('transactions.index') }}">
                            <i class="icon">
                                <i class="fas fa-receipt"></i>
                            </i>
                            <span class="item-name">Transactions</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('cash*') ? 'active' : '' }}"
                            href="{{ route('cash.index') }}">
                            <i class="icon">
                                <i class="fas fa-wallet"></i>
                            </i>
                            <span class="item-name">Cash</span>
                        </a>
                    </li>

                    <li>
                        <hr class="hr-horizontal">
                    </li>

                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Stock Management</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('stock-opname*') ? 'active' : '' }}"
                            href="{{ route('stock-opname.index') }}">
                            <i class="icon">
                                <i class="fas fa-boxes"></i>
                            </i>
                            <span class="item-name">Stock Opname</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('adjustment*') ? 'active' : '' }}"
                            href="{{ route('adjustment.index') }}">
                            <i class="icon">
                                <i class="fas fa-sliders-h"></i>
                            </i>
                            <span class="item-name">Adjustment</span>
                        </a>
                    </li>

                    <li>
                        <hr class="hr-horizontal">
                    </li>

                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Reports</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reports/sales*') ? 'active' : '' }}"
                            href="{{ route('reports.sales') }}">
                            <i class="icon">
                                <i class="fas fa-shopping-bag"></i>
                            </i>
                            <span class="item-name">Sales</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reports/purchases*') ? 'active' : '' }}"
                            href="{{ route('reports.purchases') }}">
                            <i class="icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </i>
                            <span class="item-name">Purchases</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reports/profit-loss*') ? 'active' : '' }}"
                            href="{{ route('reports.profit-loss') }}">
                            <i class="icon">
                                <i class="fas fa-chart-line"></i>
                            </i>
                            <span class="item-name">Profit & Loss</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reports/log-histories*') ? 'active' : '' }}"
                            href="{{ route('reports.log-histories') }}">
                            <i class="icon">
                                <i class="fas fa-history"></i>
                            </i>
                            <span class="item-name">Log Histories</span>
                        </a>
                    </li>
                    <li>
                        <hr class="hr-horizontal">
                    </li>

                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Settings</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('profile*') ? 'active' : '' }}"
                            href="{{ route('profile.index') }}">
                            <i class="icon">
                                <i class="fas fa-store"></i>
                            </i>
                            <span class="item-name">Profile</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('social-media*') ? 'active' : '' }}"
                            href="{{ route('social-media.index') }}">
                            <i class="icon">
                                <i class="fas fa-share-alt"></i>
                            </i>
                            <span class="item-name">Social Media</span>
                        </a>
                    </li>

                    <li>
                        <hr class="hr-horizontal">
                    </li>
                    <li class="nav-item mb-5">
                        <a id="logout-link" class="nav-link" href="#">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 7L15.59 8.41L18.17 11H8V13H18.17L15.59 15.58L17 17L22 12L17 7Z"
                                        fill="currentColor"></path>
                                    <path d="M8 4H12V2H8C6.9 2 6 2.9 6 4V20C6 21.1 6.9 22 8 22H12V20H8V4Z"
                                        fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Logout</span>
                        </a>
                        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST"
                            class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sidebar-footer"></div>
    </aside>
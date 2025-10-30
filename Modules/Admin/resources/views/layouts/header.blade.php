<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box" style="background-color: #000000;">
                <a href="{{ route('dashboard.index') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="@settings('website-logo')" alt="@settings('website-name')" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="@settings('website-logo')" alt="@settings('website-name')" height="24" > <span
                            class="logo-txt text-white">@settings('website-name')</span>
                    </span>
                </a>

                <a href="{{ route('dashboard.index') }}" class="logo logo-light">navbar-brand-box
                    <span class="logo-sm">
                        <img src="@settings('website-logo')" alt="@settings('website-name')" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="@settings('website-logo')" alt="@settings('website-name')" height="24"> <span
                            class="logo-txt text-white">@settings('website-name')</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">

            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                    <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-light-subtle border-start border-end"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('backend/images/avatar.jpg') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ auth('admin')->user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    @if (auth('admin')->user()->can('admin-settings'))
                        <a class="dropdown-item {{ Nav::isRoute(['admin-settings.index', 'admin-settings.edit']) }}"
                            href="{{ route('admin-settings.index') }}">
                            <i class="mdi mdi-application-cog-outline font-size-16 align-middle me-1"></i>
                            Settings
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); this.classList.add('disabled'); this.style.pointerEvents = 'none'; document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout font-size-16 align-middle me-1"></i>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

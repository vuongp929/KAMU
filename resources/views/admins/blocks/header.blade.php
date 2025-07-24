<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header d-flex align-items-center justify-content-between">
            <!-- Logo và Hamburger -->
            <div class="d-flex align-items-center">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <!-- Logo phiên bản dark -->
                    <a href="{{ route('admins.dashboard') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/admins/images/logo-sm.png') }}" alt="Logo Small" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/admins/images/logo-dark.png') }}" alt="Logo Dark" height="17">
                        </span>
                    </a>
                    <!-- Logo phiên bản light -->
                    <a href="{{ route('admins.dashboard') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/admins/images/logo-sm.png') }}" alt="Logo Small" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/admins/images/logo-light.png') }}" alt="Logo Light" height="17">
                        </span>
                    </a>
                </div>

                <!-- Nút Hamburger Menu -->
                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <!-- Phần bên phải của header -->
            <div class="d-flex align-items-center">
                <!-- Nút Fullscreen -->
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-bs-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>
                <!-- Nút chuyển dark/light mode -->
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>
                <!-- Dropdown người dùng -->
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{ asset('assets/admins/images/users/avatar-1.jpg') }}"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name ?? 'User' }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Administrator</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Welcome, {{ Auth::user()->name ?? 'User' }}!</h6>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span>Profile</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline-block w-100">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span>Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

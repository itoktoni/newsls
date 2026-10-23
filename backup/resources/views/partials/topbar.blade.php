<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ url('/') }}" class="logo">
                    <span class="logo-sm">
                        <img src="{{ env('APP_LOGO') ? url(env('APP_LOGO')) : url('default.png') }}" alt="" height="55" />
                    </span>
                    <span class="logo-lg"> <img class="mt-1" src="{{ env('APP_LOGO') ? url(env('APP_LOGO')) : url('default.png') }}" alt="" height="55" /></span>
                </a>


            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item d-lg-none" id="vertical-menu-btn">
            <i class="dripicons-menu"></i>
        </button>


            <!-- App Search-->
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search..." />
                    <button class="btn btn-primary" type="button"><i class="dripicons-search"></i></button>
                </div>
            </form>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="search" class="icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Search Result" />
                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @auth
            @if (auth()->user()->level == UserLevel::Developer)
            <div class="dropdown d-inline-block">
                <a href="{{ url('env-editor') }}">
                    <button type="button" class="btn header-item ">
                        <i class="dripicons-toggles"></i>
                    </button>
                </a>

                @if (env('TELESCOPE_ENABLED'))
                <a href="{{ url('telescope') }}">
                    <button type="button" class="btn header-item ">
                        <i class="dripicons-swap"></i>
                    </button>
                </a>
                @endif

            </div>

            @endif
            @endauth

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ url('assets/images/users/user.png') }}" alt="Header Avatar" />
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ auth()->user()->name }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('update-profile') }}"><i class="dripicons-user"></i>Profile</a>
                    <a class="dropdown-item" href="#"><i class="dripicons-card"></i> Billing</a>
                    <a class="dropdown-item" href="{{ route('create-settings') }}"><i class="dripicons-gear"></i> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('signout') }}">
                        <i class="dripicons-enter"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

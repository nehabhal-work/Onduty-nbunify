<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OT Duty') | DY Patil Hospital</title>

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap-icons/bootstrap-icons.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendor/choices/choices.min.css') }}?v={{ filemtime(public_path('vendor/choices/choices.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="appSidebar" aria-labelledby="sidebarTitle">
            <div class="sidebar-brand">
                <div class="brand-mark"><i class="bi bi-heart-pulse-fill"></i></div>
                <div>
                    <div class="brand-name">DY Patil Hospital</div>
                    <div class="brand-caption">Operation Theatre</div>
                </div>
                <button type="button" class="btn-close d-lg-none ms-auto" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
            </div>

            <div id="sidebarTitle" class="sidebar-section-label">WORKSPACE</div>
            <nav class="sidebar-nav" aria-label="Main navigation">
                @auth
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
                    </a>
                @endauth
                <a href="{{ route('ot-duty.index') }}" class="sidebar-link {{ request()->routeIs('ot-duty.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-check"></i><span>OT Duty Assignment</span>
                </a>
                @can('manage-ot-duty')
                    <a href="{{ route('ot-duty.create') }}" class="sidebar-link {{ request()->routeIs('ot-duty.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i><span>New Assignment</span>
                    </a>
                @endcan
            </nav>

            <div class="sidebar-footer">
                @auth
                    <div class="staff-identity">
                        <div class="staff-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="staff-details">
                            <div class="staff-name">{{ auth()->user()->name }}</div>
                            <div class="staff-role">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sidebar-link sidebar-logout">
                            <i class="bi bi-box-arrow-left"></i><span>Log out</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="sidebar-link">
                        <i class="bi bi-box-arrow-in-right"></i><span>Staff login</span>
                    </a>
                @endauth
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <button class="btn btn-outline-secondary sidebar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">@yield('page-heading', 'OT Duty Assignment')</div>
                <div class="topbar-date"><i class="bi bi-calendar3 me-2"></i>{{ now()->format('l, d M Y') }}</div>
            </header>

            <main class="app-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('vendor/bootstrap/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset('vendor/choices/choices.min.js') }}?v={{ filemtime(public_path('vendor/choices/choices.min.js')) }}"></script>
    <script src="{{ asset('vendor/qrcode/qrcode.min.js') }}?v={{ filemtime(public_path('vendor/qrcode/qrcode.min.js')) }}"></script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>

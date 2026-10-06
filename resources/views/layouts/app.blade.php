<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OT Duty') | DY Patil Hospital</title>

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="appSidebar" aria-labelledby="sidebarTitle">
            <div class="sidebar-brand align-items-start">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('img/logo.png') }}" alt="DY Patil Hospital" class="brand-logo">
                    <div class="brand-caption">Operation Theatre</div>
                </div>
                <button type="button" class="btn-close btn-close-white d-lg-none ms-auto" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
            </div>

            <div id="sidebarTitle" class="sidebar-section-label">WORKSPACE</div>
            <nav class="sidebar-nav" aria-label="Main navigation">
                @auth
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span>Dashboard</span>
                    </a>
                @endauth
                <a href="{{ route('ot-duty.index') }}" class="sidebar-link {{ request()->routeIs('ot-duty.*') ? 'active' : '' }}">
                    <span>OT Duty Assignment</span>
                </a>
                @can('manage-staff-directory')
                    <a href="{{ route('staff-directory.create', ['type' => 'sister']) }}" class="sidebar-link {{ request()->routeIs('staff-directory.create') && request('type') === 'sister' ? 'active' : '' }}">
                        <span>Add Sister</span>
                    </a>
                    <a href="{{ route('staff-directory.create', ['type' => 'technician']) }}" class="sidebar-link {{ request()->routeIs('staff-directory.create') && request('type') === 'technician' ? 'active' : '' }}">
                        <span>Add Technician</span>
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
                            <span>Log out</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="sidebar-link">
                        <span>Staff login</span>
                    </a>
                @endauth
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <button class="btn btn-outline-secondary sidebar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Open menu">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                </button>
                <div class="topbar-title">@yield('page-heading', 'OT Duty Assignment')</div>
                <div class="topbar-date">{{ now()->format('l, d M Y') }}</div>
            </header>

            @if ($subscriptionTrialActive)
                <div class="trial-notice">
                    <span>Free trial <span data-trial-countdown data-trial-ends-at="{{ $subscriptionTrialEndsAt->toIso8601String() }}"></span></span>
                    <span class="trial-end-date">ending {{ $subscriptionTrialEndsAt->format('d M Y') }}</span>
                </div>
            @endif

            <main class="app-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>

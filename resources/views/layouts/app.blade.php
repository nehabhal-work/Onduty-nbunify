<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OT Duty') | DY Patil Hospital</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <a href="{{ route('staff-directory.index') }}" class="sidebar-link {{ request()->routeIs('staff-directory.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i><span>Sister &amp; Technician</span>
                    </a>
                @endcan
                @can('manage-subscription-settings')
                    <a href="{{ route('superadmin.subscription-settings') }}" class="sidebar-link {{ request()->routeIs('superadmin.*') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i><span>Subscription settings</span>
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

            @if ($subscriptionTrialActive)
                <div class="trial-notice">
                    <span>
                        <i class="bi bi-hourglass-split me-2"></i>
                        Free trial ends in <strong class="trial-countdown" data-trial-countdown data-trial-ends-at="{{ $subscriptionTrialEndsAt->toIso8601String() }}" aria-live="off">--d --h --m --s</strong>
                        <span class="trial-end-date">(ending {{ $subscriptionTrialEndsAt->format('d M Y') }})</span>
                    </span>
                    <a href="{{ route('subscription.payment') }}">Payment details</a>
                </div>
            @endif

            <main class="app-content">
                @yield('content')
            </main>

            <footer class="app-footer">
                <span>Developed and maintained by <a href="https://nbunify.com" target="_blank" rel="noopener noreferrer">Nbunify Pvt. Ltd.</a></span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

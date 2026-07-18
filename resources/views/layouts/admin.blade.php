<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin') | EventSphere
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link href="{{ asset('css/admin-polish.css') }}"
          rel="stylesheet">

    @stack('styles')
</head>

<body class="admin-page">

<div class="admin-layout">

    @include('partials.sidebar')

    <div class="sidebar-overlay"
         id="sidebarOverlay"></div>

    <main class="admin-main">

        <header class="admin-topbar">

            <div class="topbar-left">

                <button type="button"
                        class="mobile-sidebar-button"
                        id="sidebarToggle"
                        aria-label="Open sidebar">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h1 class="topbar-title">
                        @yield('page-title', 'Admin Panel')
                    </h1>

                    <div class="topbar-subtitle">
                        EventSphere Administration
                    </div>
                </div>

            </div>

            <div class="dropdown">

                <button type="button"
                        class="topbar-user-button dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                    <span class="topbar-avatar">
                        {{ strtoupper(
                            substr(
                                auth()->user()?->name ?? 'A',
                                0,
                                1
                            )
                        ) }}
                    </span>

                    <span class="topbar-user-info text-start">
                        <span class="topbar-user-name d-block">
                            {{ auth()->user()?->name ?? 'Administrator' }}
                        </span>

                        <span class="topbar-user-role d-block">
                            {{ auth()->user()?->role?->display_name
                                ?? ucfirst(
                                    auth()->user()?->role?->name
                                    ?? 'Administrator'
                                ) }}
                        </span>
                    </span>

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    @if(\Illuminate\Support\Facades\Route::has(
                        'profile.edit'
                    ))
                        <li>
                            <a href="{{ route('profile.edit') }}"
                               class="dropdown-item">

                                <i class="bi bi-person me-2"></i>
                                Profile
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ url('/') }}"
                           class="dropdown-item">

                            <i class="bi bi-house me-2"></i>
                            Public Site
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form method="POST"
                              action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                    class="dropdown-item text-danger">

                                <i class="bi bi-box-arrow-right me-2"></i>
                                Log out
                            </button>
                        </form>
                    </li>

                </ul>
            </div>

        </header>

        <section class="admin-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger validation-summary">

                    <div class="fw-bold mb-2">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Please correct the following errors:
                    </div>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif

            @yield('content')

        </section>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar =
            document.getElementById('adminSidebar');

        const toggle =
            document.getElementById('sidebarToggle');

        const overlay =
            document.getElementById('sidebarOverlay');

        function openSidebar() {
            if (!sidebar || !overlay) {
                return;
            }

            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (!sidebar || !overlay) {
                return;
            }

            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (toggle) {
            toggle.addEventListener(
                'click',
                function () {
                    if (
                        sidebar &&
                        sidebar.classList.contains('show')
                    ) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                }
            );
        }

        if (overlay) {
            overlay.addEventListener(
                'click',
                closeSidebar
            );
        }

        if (sidebar) {
            sidebar
                .querySelectorAll('.sidebar-link')
                .forEach(function (link) {
                    link.addEventListener(
                        'click',
                        function () {
                            if (
                                window.innerWidth < 992
                            ) {
                                closeSidebar();
                            }
                        }
                    );
                });
        }

        document.addEventListener(
            'keydown',
            function (event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            }
        );

        window.addEventListener(
            'resize',
            function () {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            }
        );
    });
</script>

@stack('scripts')

</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin Dashboard') | EventSphere
    </title>

    {{-- Bootstrap CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    {{-- Laravel Vite Assets --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        :root {
            --sidebar-width: 270px;
            --sidebar-background: #111827;
            --sidebar-secondary: #1f2937;
            --sidebar-text: #d1d5db;
            --sidebar-active: #2563eb;
            --page-background: #f3f4f6;
            --topbar-height: 70px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: var(--page-background);
            color: #111827;
            overflow-x: hidden;
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-background);
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 30px;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.12);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--sidebar-background);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }

        .sidebar-header {
            min-height: 76px;
            padding: 22px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 23px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.02);
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 5px 12px;
            padding: 12px 16px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar a:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
        }

        .sidebar a.active {
            color: #ffffff;
            background: var(--sidebar-active);
            font-weight: 600;
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.25);
        }

        .sidebar a i {
            width: 22px;
            min-width: 22px;
            font-size: 18px;
            text-align: center;
        }

        .sidebar form {
            margin-top: 25px;
        }

        .sidebar form button {
            min-height: 43px;
            border-radius: 8px;
            font-weight: 600;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Wrapper
        |--------------------------------------------------------------------------
        */

        .admin-wrapper {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        /*
        |--------------------------------------------------------------------------
        | Top Navigation
        |--------------------------------------------------------------------------
        */

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            min-height: var(--topbar-height);
            padding: 0 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .topbar-title {
            margin: 0;
            color: #111827;
            font-size: 20px;
            font-weight: 700;
        }

        .sidebar-toggle {
            display: none;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            color: #111827;
            font-size: 21px;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: #f3f4f6;
        }

        .admin-user-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-user-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: #2563eb;
            border-radius: 50%;
            font-size: 20px;
        }

        .admin-user-details {
            line-height: 1.25;
        }

        .admin-user-name {
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        .admin-user-role {
            color: #6b7280;
            font-size: 12px;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Content
        |--------------------------------------------------------------------------
        */

        .admin-content {
            min-height: calc(100vh - var(--topbar-height));
            padding: 25px;
            background: var(--page-background);
        }

        .admin-content .container-fluid {
            max-width: 1600px;
        }

        /*
        |--------------------------------------------------------------------------
        | Global Admin UI
        |--------------------------------------------------------------------------
        */

        .card {
            border: 0;
            border-radius: 12px;
        }

        .card-header {
            padding: 17px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-body {
            padding: 20px;
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            padding: 13px 15px;
            color: #374151;
            background: #f9fafb;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 13px 15px;
            color: #374151;
        }

        .table-hover tbody tr:hover {
            background: #f9fafb;
        }

        .form-control,
        .form-select {
            min-height: 43px;
            border-color: #d1d5db;
            border-radius: 8px;
        }

        textarea.form-control {
            min-height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .alert {
            border: 0;
            border-radius: 10px;
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar Overlay
        |--------------------------------------------------------------------------
        */

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 1040;
            display: none;
            background: rgba(17, 24, 39, 0.55);
        }

        .sidebar-overlay.show {
            display: block;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .admin-wrapper {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
            }

            .admin-topbar {
                padding: 0 16px;
            }

            .admin-content {
                padding: 18px;
            }
        }

        @media (max-width: 575.98px) {
            :root {
                --sidebar-width: 250px;
            }

            .admin-content {
                padding: 14px;
            }

            .topbar-title {
                font-size: 17px;
            }

            .admin-user-details {
                display: none;
            }

            .card-body {
                padding: 15px;
            }

            .table thead th,
            .table tbody td {
                padding: 11px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Sidebar --}}
    <aside class="sidebar" id="adminSidebar">
        @include('partials.sidebar')
    </aside>

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay"
         id="sidebarOverlay">
    </div>

    {{-- Main Section --}}
    <div class="admin-wrapper">

        {{-- Topbar --}}
        <header class="admin-topbar">

            <div class="topbar-left">

                <button type="button"
                        class="sidebar-toggle"
                        id="sidebarToggle"
                        aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>

                <h1 class="topbar-title">
                    @yield('page-title', 'Admin Panel')
                </h1>

            </div>

            <div class="admin-user-area">

                <div class="admin-user-icon">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="admin-user-details">

                    <div class="admin-user-name">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </div>

                    <div class="admin-user-role">
                        Administrator
                    </div>

                </div>

            </div>

        </header>

        {{-- Page Content --}}
        <main class="admin-content">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <strong>
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        Please correct the following errors:
                    </strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>
            @endif

            @yield('content')

        </main>

    </div>

    {{-- Bootstrap JavaScript --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            const toggleButton = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar.classList.add('show');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            toggleButton?.addEventListener('click', function () {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            overlay?.addEventListener('click', closeSidebar);

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });

            document.querySelectorAll('#adminSidebar a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });
        });
    </script>

    @stack('scripts')

</body>
</html>
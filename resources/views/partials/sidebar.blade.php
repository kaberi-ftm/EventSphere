@php
    $findRoute = function (array $routeNames) {
        foreach ($routeNames as $routeName) {
            if (
                \Illuminate\Support\Facades\Route::has(
                    $routeName
                )
            ) {
                return $routeName;
            }
        }

        return null;
    };

    $routes = [
        'dashboard' => $findRoute([
            'admin.dashboard',
        ]),

        'users' => $findRoute([
            'admin.users.index',
            'users.index',
        ]),

        'clubs' => $findRoute([
            'admin.clubs.index',
            'clubs.index',
        ]),

        'events' => $findRoute([
            'admin.events.index',
            'events.index',
        ]),

        'venues' => $findRoute([
            'admin.venues.index',
            'venues.index',
        ]),

        'registrations' => $findRoute([
            'admin.registrations.index',
            'registrations.index',
        ]),

        'attendances' => $findRoute([
            'admin.attendances.index',
            'attendances.index',
        ]),

        'volunteers' => $findRoute([
            'admin.volunteers.index',
            'volunteers.index',
        ]),

        'tasks' => $findRoute([
            'admin.tasks.index',
            'tasks.index',
        ]),

        'sponsors' => $findRoute([
            'admin.sponsors.index',
            'sponsors.index',
        ]),

        'eventSponsors' => $findRoute([
            'admin.event-sponsors.index',
            'event-sponsors.index',
        ]),

        'budgets' => $findRoute([
            'admin.budgets.index',
            'budgets.index',
        ]),

        'payments' => $findRoute([
            'admin.payments.index',
            'payments.index',
        ]),

        'certificates' => $findRoute([
            'admin.certificates.index',
            'certificates.index',
        ]),

        'notifications' => $findRoute([
            'admin.notifications.index',
            'notifications.index',
        ]),

        'reports' => $findRoute([
            'admin.reports.index',
            'reports.index',
        ]),
    ];
@endphp

<aside class="admin-sidebar"
       id="adminSidebar">

    <a href="{{ url('/') }}"
       class="sidebar-brand">

        <span class="sidebar-brand-icon">
            E
        </span>

        <span class="sidebar-brand-text">
            EventSphere
        </span>
    </a>

    <div class="sidebar-scroll">

        <div class="sidebar-section-title">
            Main
        </div>

        @if($routes['dashboard'])
            <a href="{{ route($routes['dashboard']) }}"
               class="sidebar-link {{
                    request()->routeIs('admin.dashboard')
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        @endif

        <div class="sidebar-section-title">
            Management
        </div>

        @if($routes['users'])
            <a href="{{ route($routes['users']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.users.*',
                        'users.*',
                        'admin.memberships.*',
                        'memberships.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-people-fill"></i>
                <span>Users & Roles</span>
            </a>
        @endif

        @if($routes['clubs'])
            <a href="{{ route($routes['clubs']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.clubs.*',
                        'clubs.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-buildings"></i>
                <span>Clubs</span>
            </a>
        @endif

        @if($routes['events'])
            <a href="{{ route($routes['events']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.events.*',
                        'events.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-calendar-event-fill"></i>
                <span>Events</span>
            </a>
        @endif

        @if($routes['venues'])
            <a href="{{ route($routes['venues']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.venues.*',
                        'venues.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-geo-alt-fill"></i>
                <span>Venues</span>
            </a>
        @endif

        <div class="sidebar-section-title">
            Operations
        </div>

        @if($routes['registrations'])
            <a href="{{ route($routes['registrations']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.registrations.*',
                        'registrations.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-person-check-fill"></i>
                <span>Registrations</span>
            </a>
        @endif

        @if($routes['attendances'])
            <a href="{{ route($routes['attendances']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.attendances.*',
                        'attendances.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-clipboard-check-fill"></i>
                <span>Attendances</span>
            </a>
        @endif

        @if($routes['volunteers'])
            <a href="{{ route($routes['volunteers']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.volunteers.*',
                        'volunteers.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-person-workspace"></i>
                <span>Volunteers</span>
            </a>
        @endif

        @if($routes['tasks'])
            <a href="{{ route($routes['tasks']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.tasks.*',
                        'tasks.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-list-task"></i>
                <span>Volunteer Tasks</span>
            </a>
        @endif

        <div class="sidebar-section-title">
            Finance
        </div>

        @if($routes['sponsors'])
            <a href="{{ route($routes['sponsors']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.sponsors.*',
                        'sponsors.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-building-check"></i>
                <span>Sponsors</span>
            </a>
        @endif

        @if($routes['eventSponsors'])
            <a href="{{ route($routes['eventSponsors']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.event-sponsors.*',
                        'event-sponsors.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-cash-coin"></i>
                <span>Event Sponsorships</span>
            </a>
        @endif

        @if($routes['budgets'])
            <a href="{{ route($routes['budgets']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.budgets.*',
                        'budgets.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-pie-chart-fill"></i>
                <span>Budgets</span>
            </a>
        @endif

        @if($routes['payments'])
            <a href="{{ route($routes['payments']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.payments.*',
                        'payments.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-credit-card-fill"></i>
                <span>Payments</span>
            </a>
        @endif

        <div class="sidebar-section-title">
            System
        </div>

        @if($routes['certificates'])
            <a href="{{ route($routes['certificates']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.certificates.*',
                        'certificates.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-award-fill"></i>
                <span>Certificates</span>
            </a>
        @endif

        @if($routes['notifications'])
            <a href="{{ route($routes['notifications']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.notifications.*',
                        'notifications.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-bell-fill"></i>
                <span>Notifications</span>
            </a>
        @endif

        @if($routes['reports'])
            <a href="{{ route($routes['reports']) }}"
               class="sidebar-link {{
                    request()->routeIs([
                        'admin.reports.*',
                        'reports.*'
                    ])
                        ? 'active'
                        : ''
               }}">

                <i class="bi bi-bar-chart-fill"></i>
                <span>Reports & Analytics</span>
            </a>
        @endif

    </div>

    <div class="sidebar-footer">

        <div class="sidebar-user">

            <span class="sidebar-user-avatar">
                {{ strtoupper(
                    substr(
                        auth()->user()?->name ?? 'A',
                        0,
                        1
                    )
                ) }}
            </span>

            <div class="sidebar-user-info">
                <div class="sidebar-user-name">
                    {{ auth()->user()?->name ?? 'Administrator' }}
                </div>

                <div class="sidebar-user-role">
                    {{ auth()->user()?->role?->display_name
                        ?? ucfirst(
                            auth()->user()?->role?->name
                            ?? 'Administrator'
                        ) }}
                </div>
            </div>

            <form method="POST"
                  action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="sidebar-logout"
                        title="Log out">

                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>

        </div>

    </div>

</aside>
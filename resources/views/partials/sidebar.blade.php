<div class="sidebar-header">
    🎉 EventSphere
</div>

<a href="{{ route('admin.dashboard') }}"
   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('admin.users.index') }}"
   class="{{ request()->routeIs('admin.users.*') ||
             request()->routeIs('admin.memberships.*')
                ? 'active'
                : '' }}">

    <i class="bi bi-people-fill"></i>
    <span>Users & Roles</span>
</a>

<a href="{{ route('admin.clubs.index') }}"
   class="{{ request()->routeIs('admin.clubs.*') ? 'active' : '' }}">
    <i class="bi bi-buildings"></i>
    <span>Clubs</span>
</a>

<a href="{{ route('admin.events.index') }}"
   class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
    <i class="bi bi-calendar-event-fill"></i>
    <span>Events</span>
</a>

<a href="{{ route('admin.venues.index') }}"
   class="{{ request()->routeIs('admin.venues.*') ? 'active' : '' }}">
    <i class="bi bi-geo-alt-fill"></i>
    <span>Venues</span>
</a>

<a href="{{ route('admin.registrations.index') }}"
   class="{{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
    <i class="bi bi-person-check-fill"></i>
    <span>Event Registrations</span>
</a>

<a href="{{ route('admin.attendances.index') }}"
   class="{{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard-check-fill"></i>
    <span>Attendances</span>
</a>

<a href="{{ route('admin.volunteers.index') }}"
   class="{{ request()->routeIs('admin.volunteers.*') ? 'active' : '' }}">
    <i class="bi bi-person-workspace"></i>
    <span>Volunteers</span>
</a>

<a href="{{ route('admin.tasks.index') }}"
   class="{{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
    <i class="bi bi-list-task"></i>
    <span>Volunteer Tasks</span>
</a>

<a href="{{ route('admin.sponsors.index') }}"
   class="{{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}">
    <i class="bi bi-building-check"></i>
    <span>Sponsors</span>
</a>

<a href="{{ route('admin.event-sponsors.index') }}"
   class="{{ request()->routeIs('admin.event-sponsors.*') ? 'active' : '' }}">
    <i class="bi bi-cash-coin"></i>
    <span>Event Sponsorships</span>
</a>

<a href="{{ route('admin.budgets.index') }}"
   class="{{ request()->routeIs('admin.budgets.*') ? 'active' : '' }}">
    <i class="bi bi-pie-chart-fill"></i>
    <span>Budgets</span>
</a>

<a href="{{ route('admin.payments.index') }}"
   class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
    <i class="bi bi-credit-card-fill"></i>
    <span>Payments</span>
</a>

<a href="{{ route('admin.certificates.index') }}"
   class="{{ request()->routeIs('admin.certificates.*')
        ? 'active'
        : '' }}">

    <i class="bi bi-award-fill"></i>
    <span>Certificates</span>
</a>

<a href="{{ route('admin.notifications.index') }}"
   class="{{ request()->routeIs(
        'admin.notifications.*'
   ) ? 'active' : '' }}">

    <i class="bi bi-bell-fill"></i>
    <span>Notifications</span>
</a>

<a href="{{ route('admin.reports.index') }}"
   class="{{ request()->routeIs(
        'admin.reports.*'
   ) ? 'active' : '' }}">

    <i class="bi bi-bar-chart-fill"></i>
    <span>Reports & Analytics</span>
</a>

<form method="POST"
      action="{{ route('logout') }}"
      class="mt-4 px-3">
    @csrf

    <button type="submit"
            class="btn btn-danger w-100">
        <i class="bi bi-box-arrow-right me-2"></i>
        Logout
    </button>
</form>


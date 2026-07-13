<div class="sidebar-header">

    🎉 EventSphere

</div>

<a href="{{ route('admin.dashboard') }}">
    <i class="bi bi-speedometer2"></i>
    Dashboard
</a>

<a href="#">
    <i class="bi bi-people-fill"></i>
    Users
</a>

<a href="{{ route('admin.clubs.index') }}">
    <i class="bi bi-buildings"></i>
    Clubs
</a>

<a href="{{ route('admin.events.index') }}">
    Events
</a>

<a href="{{ route('admin.venues.index') }}">
    <i class="bi bi-geo-alt-fill"></i>
    Venues
</a>

<a href="{{ route('admin.volunteers.index') }}">
    <i class="bi bi-person-workspace"></i>
    Volunteers
</a>
<a href="{{ route('admin.registrations.index') }}">
    Event Registrations
</a>
<a href="#">
    <i class="bi bi-cash-stack"></i>
    Budgets
</a>

<a href="#">
    <i class="bi bi-award-fill"></i>
    Certificates
</a>

<a href="#">
    <i class="bi bi-bar-chart-fill"></i>
    Reports
</a>

<form method="POST" action="{{ route('logout') }}" class="mt-4 px-3">
    @csrf

    <button class="btn btn-danger w-100">

        Logout

    </button>

</form>
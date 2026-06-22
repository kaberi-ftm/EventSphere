<div class="top-navbar">

    <h4 class="mb-0">

        @yield('page-title')

    </h4>

    <div class="d-flex align-items-center gap-3">

        <i class="bi bi-bell-fill fs-4"></i>

        <strong>

            {{ auth()->user()->name }}

        </strong>

    </div>

</div>
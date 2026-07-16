@extends('layouts.admin')

@section('title', 'Budgets')
@section('page-title', 'Budget Management')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Event Budgets</h2>
            <p class="text-muted mb-0">
                Track allocated, spent and remaining amounts.
            </p>
        </div>

        <a href="{{ route('admin.budgets.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Create Budget
        </a>
    </div>

    <form method="GET"
          action="{{ route('admin.budgets.index') }}"
          class="card shadow-sm mb-4">

        <div class="card-body">
            <div class="row g-3">

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Search</label>

                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Event, category or description">
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Category</label>

                    <select name="category" class="form-select">
                        <option value="">All Categories</option>

                        @foreach([
                            'venue',
                            'food',
                            'marketing',
                            'transport',
                            'equipment',
                            'decoration',
                            'security',
                            'miscellaneous'
                        ] as $value)
                            <option value="{{ $value }}"
                                @selected($category === $value)>
                                {{ ucfirst($value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Status</label>

                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>

                        @foreach([
                            'planned',
                            'approved',
                            'closed',
                            'cancelled'
                        ] as $value)
                            <option value="{{ $value }}"
                                @selected($status === $value)>
                                {{ ucfirst($value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Sort By</label>

                    <select name="sort" class="form-select">
                        <option value="event"
                            @selected($sort === 'event')>
                            Event
                        </option>

                        <option value="category"
                            @selected($sort === 'category')>
                            Category
                        </option>

                        <option value="allocated"
                            @selected($sort === 'allocated')>
                            Allocated Amount
                        </option>

                        <option value="spent"
                            @selected($sort === 'spent')>
                            Spent Amount
                        </option>

                        <option value="remaining"
                            @selected($sort === 'remaining')>
                            Remaining Amount
                        </option>

                        <option value="status"
                            @selected($sort === 'status')>
                            Status
                        </option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label">Order</label>

                    <select name="direction" class="form-select">
                        <option value="asc"
                            @selected($direction === 'ASC')>
                            ASC
                        </option>

                        <option value="desc"
                            @selected($direction === 'DESC')>
                            DESC
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex align-items-end gap-2">
                    <button type="submit"
                            class="btn btn-dark flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>
                        Apply
                    </button>

                    <a href="{{ route('admin.budgets.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Budget Records</h5>

            <span class="badge bg-primary">
                {{ count($budgets) }} records
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Allocated</th>
                        <th>Spent</th>
                        <th>Remaining</th>
                        <th>Utilization</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($budgets as $budget)
                        @php
                            $statusClass = match(strtolower($budget->status)) {
                                'approved' => 'bg-success',
                                'planned' => 'bg-warning text-dark',
                                'closed' => 'bg-secondary',
                                'cancelled' => 'bg-danger',
                                default => 'bg-dark'
                            };

                            $utilization = (float) $budget->utilization_percentage;

                            $progressClass = match(true) {
                                $utilization >= 90 => 'bg-danger',
                                $utilization >= 70 => 'bg-warning',
                                default => 'bg-success'
                            };
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $budget->event_title }}
                                </div>

                                <small class="text-muted">
                                    Budget #{{ $budget->id }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($budget->category) }}
                                </span>
                            </td>

                            <td class="fw-semibold">
                                ৳{{ number_format((float) $budget->allocated_amount, 2) }}
                            </td>

                            <td class="text-danger fw-semibold">
                                ৳{{ number_format((float) $budget->spent_amount, 2) }}
                            </td>

                            <td class="text-success fw-semibold">
                                ৳{{ number_format((float) $budget->remaining_amount, 2) }}
                            </td>

                            <td style="min-width: 150px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>{{ number_format($utilization, 2) }}%</small>
                                </div>

                                <div class="progress"
                                     style="height: 7px;">
                                    @php
    $progressWidth = min(
        max((float) $utilization, 0),
        100
    );
@endphp

<div class="progress"
     style="height: 7px;">

    <div class="progress-bar {{ $progressClass }}"
         role="progressbar"
         style="{{ 'width: ' . $progressWidth . '%;' }}"
         aria-valuenow="{{ $progressWidth }}"
         aria-valuemin="0"
         aria-valuemax="100">@extends('layouts.admin')

@section('title', 'Budgets')
@section('page-title', 'Budget Management')

@push('styles')
<style>
    .budget-progress {
        display: block;
        width: 100%;
        height: 8px;
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        background-color: #e5e7eb;
        accent-color: #198754;
    }

    .budget-progress.progress-safe {
        accent-color: #198754;
    }

    .budget-progress.progress-warning {
        accent-color: #ffc107;
    }

    .budget-progress.progress-danger {
        accent-color: #dc3545;
    }

    .budget-progress::-webkit-progress-bar {
        background-color: #e5e7eb;
        border-radius: 10px;
    }

    .budget-progress.progress-safe::-webkit-progress-value {
        background-color: #198754;
        border-radius: 10px;
    }

    .budget-progress.progress-warning::-webkit-progress-value {
        background-color: #ffc107;
        border-radius: 10px;
    }

    .budget-progress.progress-danger::-webkit-progress-value {
        background-color: #dc3545;
        border-radius: 10px;
    }

    .budget-progress.progress-safe::-moz-progress-bar {
        background-color: #198754;
        border-radius: 10px;
    }

    .budget-progress.progress-warning::-moz-progress-bar {
        background-color: #ffc107;
        border-radius: 10px;
    }

    .budget-progress.progress-danger::-moz-progress-bar {
        background-color: #dc3545;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Event Budgets
            </h2>

            <p class="text-muted mb-0">
                Track allocated, spent and remaining amounts.
            </p>
        </div>

        <a href="{{ route('admin.budgets.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-circle me-1"></i>
            Create Budget
        </a>
    </div>

    {{-- Search, filter and sorting --}}
    <form method="GET"
          action="{{ route('admin.budgets.index') }}"
          class="card shadow-sm mb-4">

        <div class="card-body">
            <div class="row g-3">

                {{-- Search --}}
                <div class="col-lg-3 col-md-6">
                    <label for="search"
                           class="form-label">
                        Search
                    </label>

                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Event, category or description">
                </div>

                {{-- Category --}}
                <div class="col-lg-2 col-md-6">
                    <label for="category"
                           class="form-label">
                        Category
                    </label>

                    <select id="category"
                            name="category"
                            class="form-select">

                        <option value="">
                            All Categories
                        </option>

                        @foreach([
                            'venue',
                            'food',
                            'marketing',
                            'transport',
                            'equipment',
                            'decoration',
                            'security',
                            'miscellaneous'
                        ] as $value)

                            <option value="{{ $value }}"
                                @selected($category === $value)>

                                {{ ucfirst($value) }}
                            </option>

                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-lg-2 col-md-6">
                    <label for="status"
                           class="form-label">
                        Status
                    </label>

                    <select id="status"
                            name="status"
                            class="form-select">

                        <option value="">
                            All Statuses
                        </option>

                        @foreach([
                            'planned',
                            'approved',
                            'closed',
                            'cancelled'
                        ] as $value)

                            <option value="{{ $value }}"
                                @selected($status === $value)>

                                {{ ucfirst($value) }}
                            </option>

                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div class="col-lg-2 col-md-6">
                    <label for="sort"
                           class="form-label">
                        Sort By
                    </label>

                    <select id="sort"
                            name="sort"
                            class="form-select">

                        <option value="event"
                            @selected($sort === 'event')>
                            Event
                        </option>

                        <option value="category"
                            @selected($sort === 'category')>
                            Category
                        </option>

                        <option value="allocated"
                            @selected($sort === 'allocated')>
                            Allocated Amount
                        </option>

                        <option value="spent"
                            @selected($sort === 'spent')>
                            Spent Amount
                        </option>

                        <option value="remaining"
                            @selected($sort === 'remaining')>
                            Remaining Amount
                        </option>

                        <option value="status"
                            @selected($sort === 'status')>
                            Status
                        </option>

                    </select>
                </div>

                {{-- Direction --}}
                <div class="col-lg-1 col-md-6">
                    <label for="direction"
                           class="form-label">
                        Order
                    </label>

                    <select id="direction"
                            name="direction"
                            class="form-select">

                        <option value="asc"
                            @selected(strtoupper($direction) === 'ASC')>
                            ASC
                        </option>

                        <option value="desc"
                            @selected(strtoupper($direction) === 'DESC')>
                            DESC
                        </option>

                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-lg-2 col-md-6 d-flex align-items-end gap-2">

                    <button type="submit"
                            class="btn btn-dark flex-grow-1">

                        <i class="bi bi-funnel me-1"></i>
                        Apply
                    </button>

                    <a href="{{ route('admin.budgets.index') }}"
                       class="btn btn-outline-secondary"
                       title="Reset filters">

                        <i class="bi bi-arrow-clockwise"></i>
                    </a>

                </div>

            </div>
        </div>
    </form>

    {{-- Budget table --}}
    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Budget Records
            </h5>

            <span class="badge bg-primary">
                {{ count($budgets) }} records
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Allocated</th>
                        <th>Spent</th>
                        <th>Remaining</th>
                        <th>Utilization</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($budgets as $budget)

                        @php
                            $budgetStatus = strtolower(
                                (string) $budget->status
                            );

                            $statusClass = match($budgetStatus) {
                                'approved' => 'bg-success',
                                'planned' => 'bg-warning text-dark',
                                'closed' => 'bg-secondary',
                                'cancelled' => 'bg-danger',
                                default => 'bg-dark'
                            };

                            $utilization = (float) (
                                $budget->utilization_percentage ?? 0
                            );

                            $progressWidth = min(
                                max($utilization, 0),
                                100
                            );

                            $progressClass = match(true) {
                                $utilization >= 90 =>
                                    'progress-danger',

                                $utilization >= 70 =>
                                    'progress-warning',

                                default =>
                                    'progress-safe'
                            };
                        @endphp

                        <tr>

                            {{-- Event --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $budget->event_title }}
                                </div>

                                <small class="text-muted">
                                    Budget #{{ $budget->id }}
                                </small>
                            </td>

                            {{-- Category --}}
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($budget->category) }}
                                </span>
                            </td>

                            {{-- Allocated --}}
                            <td class="fw-semibold">
                                ৳{{ number_format(
                                    (float) $budget->allocated_amount,
                                    2
                                ) }}
                            </td>

                            {{-- Spent --}}
                            <td class="text-danger fw-semibold">
                                ৳{{ number_format(
                                    (float) $budget->spent_amount,
                                    2
                                ) }}
                            </td>

                            {{-- Remaining --}}
                            <td class="text-success fw-semibold">
                                ৳{{ number_format(
                                    (float) $budget->remaining_amount,
                                    2
                                ) }}
                            </td>

                            {{-- Utilization --}}
                            <td style="min-width: 160px;">

                                <div class="d-flex justify-content-between mb-1">

                                    <small class="fw-semibold">
                                        {{ number_format(
                                            $utilization,
                                            2
                                        ) }}%
                                    </small>

                                    @if($utilization > 100)
                                        <small class="text-danger">
                                            Over budget
                                        </small>
                                    @endif

                                </div>

                                <progress
                                    class="budget-progress {{ $progressClass }}"
                                    value="{{ $progressWidth }}"
                                    max="100"
                                    aria-label="Budget utilization">

                                    {{ $progressWidth }}%

                                </progress>

                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($budgetStatus) }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-end text-nowrap">

                                <a href="{{ route(
                                    'admin.budgets.show',
                                    $budget->id
                                ) }}"
                                   class="btn btn-sm btn-info"
                                   title="View budget">

                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route(
                                    'admin.budgets.edit',
                                    $budget->id
                                ) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Edit budget">

                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route(
                                          'admin.budgets.destroy',
                                          $budget->id
                                      ) }}"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Delete budget"
                                            onclick="return confirm(
                                                'Delete this budget?'
                                            )">

                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="text-center py-5">

                                <i class="bi bi-wallet2 fs-1 text-muted"></i>

                                <h5 class="mt-3">
                                    No budgets found
                                </h5>

                                <p class="text-muted">
                                    Create your first event budget.
                                </p>

                                <a href="{{ route(
                                    'admin.budgets.create'
                                ) }}"
                                   class="btn btn-primary">

                                    <i class="bi bi-plus-circle me-1"></i>
                                    Create Budget
                                </a>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
    </div>

</div>
                                </div>
                            </td>

                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($budget->status) }}
                                </span>
                            </td>

                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.budgets.show', $budget->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.budgets.edit', $budget->id) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.budgets.destroy', $budget->id) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this budget?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8"
                                class="text-center py-5">

                                <i class="bi bi-wallet2 fs-1 text-muted"></i>

                                <h5 class="mt-3">No budgets found</h5>

                                <p class="text-muted">
                                    Create your first event budget.
                                </p>

                                <a href="{{ route('admin.budgets.create') }}"
                                   class="btn btn-primary">
                                    Create Budget
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
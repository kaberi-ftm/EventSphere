@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h3>Sponsors</h3>
            <p class="text-muted">Manage sponsors and contribution history.</p>
        </div>

        <a href="{{ route('admin.sponsors.create') }}"
           class="btn btn-success">
            Add Sponsor
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Search sponsor">
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected($status === 'active')>
                        Active
                    </option>
                    <option value="inactive" @selected($status === 'inactive')>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="name" @selected($sort === 'name')>
                        Name
                    </option>
                    <option value="contribution"
                        @selected($sort === 'contribution')>
                        Contribution
                    </option>
                    <option value="events" @selected($sort === 'events')>
                        Events
                    </option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="direction" class="form-select">
                    <option value="asc" @selected($direction === 'ASC')>
                        Ascending
                    </option>
                    <option value="desc" @selected($direction === 'DESC')>
                        Descending
                    </option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Events</th>
                        <th>Total Contribution</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sponsors as $sponsor)
                        <tr>
                            <td>{{ $sponsor->name }}</td>
                            <td>{{ ucfirst($sponsor->sponsor_type) }}</td>
                            <td>
                                {{ $sponsor->contact_person ?? 'N/A' }}<br>
                                <small>{{ $sponsor->email }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $sponsor->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($sponsor->status) }}
                                </span>
                            </td>
                            <td>{{ $sponsor->total_events }}</td>
                            <td>
                                {{ number_format($sponsor->total_contribution, 2) }}
                            </td>
                            <td>
                                <a href="{{ route('admin.sponsors.show', $sponsor->id) }}"
                                   class="btn btn-info btn-sm">View</a>

                                <a href="{{ route('admin.sponsors.edit', $sponsor->id) }}"
                                   class="btn btn-warning btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('admin.sponsors.destroy', $sponsor->id) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete sponsor?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No sponsors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h3>Event Sponsorships</h3>
            <p class="text-muted">Manage event contributions and rankings.</p>
        </div>

        <a href="{{ route('admin.event-sponsors.create') }}"
           class="btn btn-success">
            Add Sponsorship
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
            <div class="col-md-3">
                <input name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Sponsor or event">
            </div>

            <div class="col-md-3">
                <select name="event_id" class="form-select">
                    <option value="">All Events</option>

                    @foreach($events as $event)
                        <option value="{{ $event->id }}"
                            @selected($eventId == $event->id)>
                            {{ $event->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>

                    @foreach(['pledged','confirmed','paid','cancelled'] as $value)
                        <option value="{{ $value }}"
                            @selected($status === $value)>
                            {{ ucfirst($value) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="date" @selected($sort === 'date')>
                        Agreement Date
                    </option>
                    <option value="amount" @selected($sort === 'amount')>
                        Amount
                    </option>
                    <option value="sponsor" @selected($sort === 'sponsor')>
                        Sponsor
                    </option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Sponsor</th>
                        <th>Event</th>
                        <th>Amount</th>
                        <th>Rank</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Agreement</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($eventSponsors as $item)
                        <tr>
                            <td>{{ $item->sponsor_name }}</td>
                            <td>{{ $item->event_title }}</td>
                            <td>{{ number_format($item->amount, 2) }}</td>
                            <td>#{{ $item->contribution_rank }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $item->contribution_type)) }}
                            </td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->agreement_date }}</td>
                            <td>
                                <a href="{{ route('admin.event-sponsors.show', $item->id) }}"
                                   class="btn btn-info btn-sm">View</a>

                                <a href="{{ route('admin.event-sponsors.edit', $item->id) }}"
                                   class="btn btn-warning btn-sm">Edit</a>

                                <form method="POST"
                                      action="{{ route('admin.event-sponsors.destroy', $item->id) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete sponsorship?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No event sponsorships found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
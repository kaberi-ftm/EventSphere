@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between">
            <h4>{{ $sponsor->name }}</h4>

            <a href="{{ route('admin.sponsors.edit', $sponsor->id) }}"
               class="btn btn-warning btn-sm">
                Edit
            </a>
        </div>

        <div class="card-body">
            <p><strong>Contact:</strong> {{ $sponsor->contact_person ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $sponsor->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $sponsor->phone ?? 'N/A' }}</p>
            <p><strong>Type:</strong> {{ ucfirst($sponsor->sponsor_type) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($sponsor->status) }}</p>
            <p><strong>Total Events:</strong> {{ $sponsor->total_events }}</p>
            <p>
                <strong>Total Contribution:</strong>
                {{ number_format($sponsor->total_contribution, 2) }}
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Sponsorship History</h5>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($eventSponsorships as $item)
                        <tr>
                            <td>{{ $item->event_title }}</td>
                            <td>{{ number_format($item->amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $item->contribution_type)) }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->agreement_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No sponsorship history.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
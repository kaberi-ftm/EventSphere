@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <h4>Sponsorship Details</h4>

            <a href="{{ route(
                'admin.event-sponsors.edit',
                $eventSponsor->id
            ) }}"
               class="btn btn-warning btn-sm">
                Edit
            </a>
        </div>

        <div class="card-body">
            <p><strong>Sponsor:</strong> {{ $eventSponsor->sponsor_name }}</p>
            <p><strong>Event:</strong> {{ $eventSponsor->event_title }}</p>
            <p><strong>Amount:</strong> {{ number_format($eventSponsor->amount, 2) }}</p>
            <p>
                <strong>Type:</strong>
                {{ ucfirst(str_replace('_', ' ', $eventSponsor->contribution_type)) }}
            </p>
            <p><strong>Status:</strong> {{ ucfirst($eventSponsor->status) }}</p>
            <p><strong>Agreement Date:</strong> {{ $eventSponsor->agreement_date }}</p>
            <p><strong>Notes:</strong> {{ $eventSponsor->notes ?? 'N/A' }}</p>

            <a href="{{ route('admin.event-sponsors.index') }}"
               class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>
</div>
@endsection
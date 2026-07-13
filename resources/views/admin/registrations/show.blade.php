@extends('layouts.admin')

@section('title', 'Registration Details')

@section('content')

<div class="container">

    <div class="card shadow-sm">
        <div class="card-header">
            <h3>Registration Details</h3>
        </div>

        <div class="card-body">

            <p><strong>ID:</strong> {{ $registration->id }}</p>

            <p>
                <strong>Event:</strong>
                {{ $registration->event_title }}
            </p>

            <p>
                <strong>User:</strong>
                {{ $registration->user_name }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ ucfirst($registration->status) }}
            </p>

            <p>
                <strong>Registered At:</strong>
                {{ $registration->registered_at }}
            </p>

            <a href="{{ route('admin.registrations.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </div>

    </div>

</div>

@endsection
@extends('layouts.admin')

@section('title', 'Registrations')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-4">
        <h2>Event Registrations</h2>

        <a href="{{ route('admin.registrations.create') }}"
           class="btn btn-primary">
            + Register User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card p-4">

        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Status</th>
                    <th>Registered At</th>
                    <th width="120">Action</th>
                </tr>
            </thead>

            <tbody>

            @forelse($registrations as $registration)

                <tr>

                    <td>{{ $registration->id }}</td>

                    <td>{{ $registration->user_name }}</td>

                    <td>{{ $registration->event_title
    ?? $registration->event_name
    ?? 'Unknown Event' }}</td>

                    <td>
                        <span class="badge bg-success">
                            {{ $registration->status }}
                        </span>
                    </td>

                    <td>
                        {{ $registration->registered_at }}
                    </td>

                    <td>

                        <form
                            method="POST"
                            action="{{ route('admin.registrations.destroy',$registration->id) }}"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete registration?')"
                            >
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center">
                        No registrations found.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
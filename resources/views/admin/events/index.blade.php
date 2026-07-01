@extends('layouts.admin')

@section('title', 'Events')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Event Management</h2>

    <a href="{{ route('admin.events.create') }}"
       class="btn btn-primary">
        + Create Event
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">
                <tr>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Club</th>
                    <th>Created By</th>
                    <th>Start Time</th>
                    <th>Status</th>
                    <th>Capacity</th>
                    <th width="220">Actions</th>
                </tr>
            </thead>

            <tbody>

            @forelse($events as $event)

                <tr>

                    <td width="120">

                        @if($event->poster)

                            <img
                                src="{{ asset($event->poster) }}"
                                width="100"
                                height="70"
                                class="rounded shadow"
                                style="object-fit:cover">

                        @else

                            <span class="text-muted">
                                No Poster
                            </span>

                        @endif

                    </td>

                    <td>{{ $event->title }}</td>

                    <td>{{ $event->club_name }}</td>

                    <td>{{ $event->creator_name }}</td>

                    <td>{{ $event->start_time }}</td>

                    <td>
                        <span class="badge bg-primary">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>

                    <td>{{ $event->max_participants }}</td>

                    <td>

                        <a href="{{ route('admin.events.show',$event->id) }}"
                           class="btn btn-info btn-sm">
                            View
                        </a>

                        <a href="{{ route('admin.events.edit',$event->id) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form
                            action="{{ route('admin.events.destroy',$event->id) }}"
                            method="POST"
                            style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this event?')">

                                Delete

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8" class="text-center">
                        No events found.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>
</div>

@endsection
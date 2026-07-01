@extends('layouts.admin')

@section('title', 'Event Details')

@section('content')

<h2 class="mb-4">Event Details</h2>

<div class="card">
    <div class="card-body">

        @if($event->poster)

            <div class="mb-4">

                <img
                    src="{{ asset($event->poster) }}"
                    class="img-fluid rounded shadow"
                    style="
                        max-height:450px;
                        width:100%;
                        object-fit:cover;
                    ">

            </div>

        @endif

        <table class="table table-bordered">

            <tr>
                <th width="250">Title</th>
                <td>{{ $event->title }}</td>
            </tr>

            <tr>
                <th>Club</th>
                <td>{{ $event->club_name }}</td>
            </tr>

            <tr>
                <th>Created By</th>
                <td>{{ $event->creator_name }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-primary">
                        {{ ucfirst($event->status) }}
                    </span>
                </td>
            </tr>

            <tr>
                <th>Start Time</th>
                <td>{{ $event->start_time }}</td>
            </tr>

            <tr>
                <th>Maximum Participants</th>
                <td>{{ $event->max_participants }}</td>
            </tr>

            <tr>
                <th>Description</th>
                <td>{{ $event->description }}</td>
            </tr>

        </table>

        <a
            href="{{ route('admin.events.index') }}"
            class="btn btn-secondary">

            Back

        </a>

    </div>
</div>

@endsection
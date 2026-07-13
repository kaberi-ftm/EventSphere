@extends('layouts.admin')

@section('title', 'Attendance Details')

@section('content')

<div class="container">

    <div class="card shadow-sm">
        <div class="card-header">
            <h3>Attendance Details</h3>
        </div>

        <div class="card-body">

            <p><strong>ID:</strong> {{ $attendance->id }}</p>

            <p>
                <strong>Event:</strong>
                {{ $attendance->event_title }}
            </p>

            <p>
                <strong>User:</strong>
                {{ $attendance->user_name }}
            </p>

            <p>
                <strong>Status:</strong>

                @if($attendance->is_present == 'Y')
                    Present
                @else
                    Absent
                @endif
            </p>

            <p>
                <strong>Checked In:</strong>
                {{ $attendance->checked_in_at }}
            </p>

            <a
                href="{{ route('admin.attendances.index') }}"
                class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>

</div>

@endsection
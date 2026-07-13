@extends('layouts.admin')

@section('title', 'Attendance')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-4">

        <h2>Attendance Records</h2>

        <a href="{{ route('admin.attendances.create') }}"
           class="btn btn-primary">
            + Mark Attendance
        </a>

    </div>

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    <div class="card p-4">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Present</th>
                    <th>Checked In</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

            @forelse($attendances as $attendance)

                <tr>

                    <td>{{ $attendance->id }}</td>

                    <td>{{ $attendance->user_name }}</td>

                    <td>{{ $attendance->event_title }}</td>

                    <td>

                        @if($attendance->is_present=='Y')

                            <span class="badge bg-success">
                                Present
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Absent
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $attendance->checked_in_at }}
                    </td>

                    <td>

                        <form
                            method="POST"
                            action="{{ route('admin.attendances.destroy',$attendance->id) }}"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete attendance?')"
                            >
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="text-center">
                        No attendance records found.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
@extends('layouts.admin')

@section('title', 'Mark Attendance')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Mark Attendance
    </h2>

    <div class="card p-4">

        <form
            method="POST"
            action="{{ route('admin.attendances.store') }}"
        >

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    User
                </label>

                <select
                    name="user_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        Select User
                    </option>

                    @foreach($users as $user)

                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Event
                </label>

                <select
                    name="event_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        Select Event
                    </option>

                    @foreach($events as $event)

                        <option value="{{ $event->id }}">
                            {{ $event->title }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Present?
                </label>

                <select
                    name="is_present"
                    class="form-control"
                >

                    <option value="Y">
                        Present
                    </option>

                    <option value="N">
                        Absent
                    </option>

                </select>

            </div>

            <button class="btn btn-success">
                Save Attendance
            </button>

            <a
                href="{{ route('admin.attendances.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>

        </form>

    </div>

</div>

@endsection
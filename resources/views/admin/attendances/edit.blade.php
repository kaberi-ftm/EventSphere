@extends('layouts.admin')

@section('title', 'Edit Attendance')

@section('content')

<div class="container">

    <div class="card shadow-sm">
        <div class="card-header">
            <h3>Edit Attendance</h3>
        </div>

        <div class="card-body">

            <form
                action="{{ route('admin.attendances.update',$attendance->id) }}"
                method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label>Event</label>

                    <select
                        name="event_id"
                        class="form-control">

                        @foreach($events as $event)

                            <option
                                value="{{ $event->id }}"
                                {{ $attendance->event_id == $event->id ? 'selected' : '' }}>

                                {{ $event->title }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label>User</label>

                    <select
                        name="user_id"
                        class="form-control">

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                {{ $attendance->user_id == $user->id ? 'selected' : '' }}>

                                {{ $user->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label>Attendance Status</label>

                    <select
                        name="is_present"
                        class="form-control">

                        <option
                            value="Y"
                            {{ $attendance->is_present=='Y'?'selected':'' }}>
                            Present
                        </option>

                        <option
                            value="N"
                            {{ $attendance->is_present=='N'?'selected':'' }}>
                            Absent
                        </option>

                    </select>

                </div>

                <button class="btn btn-primary">
                    Update Attendance
                </button>

            </form>

        </div>

    </div>

</div>

@endsection
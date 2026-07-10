@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Apply as Volunteer</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <strong>Event:</strong>
                {{ $event->title }}
            </div>

            <div class="mb-3">
                <strong>Club:</strong>
                {{ $event->club_name }}
            </div>

            <div class="mb-3">
                <strong>Start Time:</strong>
                {{ $event->start_time }}
            </div>

            @if($existingApplication)
                <div class="alert alert-info">
                    You have already applied for this event.

                    Current status:
                    <strong>
                        {{ ucfirst($existingApplication->status) }}
                    </strong>
                </div>
            @else
                <form method="POST"
                      action="{{ route('volunteers.store') }}">
                    @csrf

                    <input type="hidden"
                           name="event_id"
                           value="{{ $event->id }}">

                    <div class="mb-3">
                        <label class="form-label">
                            Preferred Volunteer Role
                        </label>

                        <select name="role"
                                class="form-select"
                                required>
                            <option value="">
                                Select a role
                            </option>

                            @foreach($availableRoles as $role)
                                <option value="{{ $role }}"
                                    @selected(old('role') === $role)>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="btn btn-primary">
                        Submit Application
                    </button>

                    <a href="{{ route('participant.dashboard') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
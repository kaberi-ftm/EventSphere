@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">Create Volunteer Application</h4>
        </div>

        <div class="card-body">

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

            <form method="POST"
                  action="{{ route('admin.volunteers.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">User</label>

                    <select name="user_id"
                            class="form-select"
                            required>
                        <option value="">Select User</option>

                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @selected(old('user_id') == $user->id)>
                                {{ $user->name }} — {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Event</label>

                    <select name="event_id"
                            class="form-select"
                            required>
                        <option value="">Select Event</option>

                        @foreach($events as $event)
                            <option value="{{ $event->id }}"
                                @selected(old('event_id') == $event->id)>
                                {{ $event->title }}
                                — {{ $event->start_time }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Volunteer Role
                    </label>

                    <select name="role"
                            class="form-select"
                            required>
                        <option value="">Select Role</option>

                        @foreach($availableRoles as $role)
                            <option value="{{ $role }}"
                                @selected(old('role') === $role)>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select name="status"
                            class="form-select"
                            required>
                        <option value="pending"
                            @selected(old('status', 'pending') === 'pending')>
                            Pending
                        </option>

                        <option value="approved"
                            @selected(old('status') === 'approved')>
                            Approved
                        </option>

                        <option value="rejected"
                            @selected(old('status') === 'rejected')>
                            Rejected
                        </option>
                    </select>
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Save Application
                </button>

                <a href="{{ route('admin.volunteers.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            </form>

        </div>
    </div>
</div>
@endsection
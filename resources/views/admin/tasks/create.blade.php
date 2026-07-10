@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Assign Task</h4>
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

            @if(count($volunteers) === 0)
                <div class="alert alert-warning">
                    No approved volunteers are available.
                    Approve a volunteer application first.
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.tasks.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Approved Volunteer
                    </label>

                    <select name="volunteer_id"
                            class="form-select"
                            required>
                        <option value="">
                            Select approved volunteer
                        </option>

                        @foreach($volunteers as $volunteer)
                            <option value="{{ $volunteer->id }}"
                                @selected(
                                    old('volunteer_id') == $volunteer->id
                                )>
                                {{ $volunteer->user_name }}
                                — {{ $volunteer->event_title }}
                                — {{ $volunteer->role }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Task Title
                    </label>

                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description"
                              class="form-control"
                              rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Status
                    </label>

                    <select name="status"
                            class="form-select">
                        <option value="pending"
                            @selected(old('status') === 'pending')>
                            Pending
                        </option>

                        <option value="in_progress"
                            @selected(old('status') === 'in_progress')>
                            In Progress
                        </option>

                        <option value="completed"
                            @selected(old('status') === 'completed')>
                            Completed
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Deadline
                    </label>

                    <input type="datetime-local"
                           name="deadline"
                           class="form-control"
                           value="{{ old('deadline') }}">
                </div>

                <button type="submit"
                        class="btn btn-primary"
                        @disabled(count($volunteers) === 0)>
                    Assign Task
                </button>

                <a href="{{ route('admin.tasks.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
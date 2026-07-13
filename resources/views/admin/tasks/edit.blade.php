@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">Edit Task</h4>
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
                  action="{{ route('admin.tasks.update', $task->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">
                        Approved Volunteer
                    </label>

                    <select name="volunteer_id"
                            class="form-select"
                            required>
                        @foreach($volunteers as $volunteer)
                            <option value="{{ $volunteer->id }}"
                                @selected(
                                    old(
                                        'volunteer_id',
                                        $task->volunteer_id
                                    ) == $volunteer->id
                                )>
                                {{ $volunteer->user_name }}
                                — {{ $volunteer->event_title }}
                                — {{ $volunteer->role }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Task Title</label>

                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title', $task->title) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>

                    <textarea name="description"
                              class="form-control"
                              rows="4">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select name="status"
                            class="form-select"
                            required>
                        <option value="pending"
                            @selected(
                                old('status', $task->status) === 'pending'
                            )>
                            Pending
                        </option>

                        <option value="in_progress"
                            @selected(
                                old('status', $task->status) === 'in_progress'
                            )>
                            In Progress
                        </option>

                        <option value="completed"
                            @selected(
                                old('status', $task->status) === 'completed'
                            )>
                            Completed
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deadline</label>

                    <input type="datetime-local"
                           name="deadline"
                           class="form-control"
                           value="{{ old(
                               'deadline',
                               $task->deadline
                                   ? \Carbon\Carbon::parse($task->deadline)
                                       ->format('Y-m-d\TH:i')
                                   : ''
                           ) }}">
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Update Task
                </button>

                <a href="{{ route('admin.tasks.show', $task->id) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            </form>

        </div>
    </div>
</div>
@endsection
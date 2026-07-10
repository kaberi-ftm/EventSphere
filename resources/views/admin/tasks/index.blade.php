@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Task Management</h3>

        <a href="{{ route('admin.tasks.create') }}"
           class="btn btn-primary">
            Assign New Task
        </a>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Volunteer</th>
                        <th>Event</th>
                        <th>Role</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th width="220">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->volunteer_name }}</td>
                            <td>{{ $task->event_title }}</td>
                            <td>{{ $task->volunteer_role }}</td>
                            <td>{{ $task->title }}</td>

                            <td>
                                @if($task->status === 'completed')
                                    <span class="badge bg-success">
                                        Completed
                                    </span>
                                @elseif($task->status === 'in_progress')
                                    <span class="badge bg-warning text-dark">
                                        In Progress
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $task->deadline ?? 'No deadline' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.tasks.show', $task->id) }}"
                                   class="btn btn-info btn-sm">
                                    View
                                </a>

                                <a href="{{ route('admin.tasks.edit', $task->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.tasks.destroy', $task->id) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8"
                                class="text-center text-muted">
                                No tasks have been assigned yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
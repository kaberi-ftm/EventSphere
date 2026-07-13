@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Task Details</h4>

                <a href="{{ route('admin.tasks.edit', $task->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit Task
                </a>
            </div>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Task ID</div>
                <div class="col-md-8">
                    {{ $task->id }}
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Title</div>
                <div class="col-md-8">
                    {{ $task->title }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Description</div>
                <div class="col-md-8">
                    {{ $task->description ?? 'No description' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Volunteer</div>
                <div class="col-md-8">
                    {{ $task->volunteer_name }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Volunteer Email</div>
                <div class="col-md-8">
                    {{ $task->volunteer_email ?? 'N/A' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Volunteer Role</div>
                <div class="col-md-8">
                    {{ $task->volunteer_role }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Event</div>
                <div class="col-md-8">
                    {{ $task->event_title }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Status</div>
                <div class="col-md-8">

                    @if(strtolower($task->status) === 'completed')
                        <span class="badge bg-success">
                            Completed
                        </span>
                    @elseif(strtolower($task->status) === 'in_progress')
                        <span class="badge bg-warning text-dark">
                            In Progress
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            Pending
                        </span>
                    @endif

                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Deadline</div>
                <div class="col-md-8">
                    {{ $task->deadline ?? 'No deadline' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Completed At</div>
                <div class="col-md-8">
                    {{ $task->completed_at ?? 'Not completed' }}
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 fw-bold">Created At</div>
                <div class="col-md-8">
                    {{ $task->created_at ?? 'N/A' }}
                </div>
            </div>

            <a href="{{ route('admin.tasks.index') }}"
               class="btn btn-secondary">
                Back to Tasks
            </a>

        </div>
    </div>
</div>
@endsection
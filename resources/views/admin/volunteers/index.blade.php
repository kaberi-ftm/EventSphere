@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Volunteer Applications</h3>
            <p class="text-muted mb-0">
                Review, approve, or reject volunteer applications.
            </p>
        </div>
       <div class="d-flex gap-2">
    <a href="{{ route('admin.volunteers.create') }}"
       class="btn btn-success">
        <i class="bi bi-person-plus"></i>
        Add Volunteer
    </a>

    <a href="{{ route('admin.tasks.index') }}"
       class="btn btn-primary">
        <i class="bi bi-list-task"></i>
        Manage Tasks
    </a>
</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                Application List
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Event</th>
                            <th>Preferred Role</th>
                            <th>Applied At</th>
                            <th>Status</th>
                            <th class="text-center">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($volunteers as $volunteer)
                            <tr>
                                <td>
                                    {{ $volunteer->id }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $volunteer->user_name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $volunteer->user_email }}
                                </td>

                                <td>
                                    {{ $volunteer->event_title }}
                                </td>

                                <td>
                                    {{ $volunteer->role }}
                                </td>

                                <td>
                                    {{ $volunteer->applied_at ?? 'N/A' }}
                                </td>

                                <td>
                                    @if(strtolower($volunteer->status) === 'approved')
                                        <span class="badge bg-success">
                                            Approved
                                        </span>

                                    @elseif(strtolower($volunteer->status) === 'rejected')
                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>

                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
    <a href="{{ route('admin.volunteers.show', $volunteer->id) }}"
       class="btn btn-info btn-sm">
        <i class="bi bi-eye"></i>
        View
    </a>

    <a href="{{ route('admin.volunteers.edit', $volunteer->id) }}"
       class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i>
        Edit
    </a>

    @if(strtolower($volunteer->status) === 'pending')
        <form method="POST"
              action="{{ route('admin.volunteers.approve', $volunteer->id) }}"
              class="d-inline">
            @csrf

            <button type="submit"
                    class="btn btn-success btn-sm"
                    onclick="return confirm('Approve this volunteer application?')">
                Approve
            </button>
        </form>

        <form method="POST"
              action="{{ route('admin.volunteers.reject', $volunteer->id) }}"
              class="d-inline">
            @csrf

            <button type="submit"
                    class="btn btn-secondary btn-sm"
                    onclick="return confirm('Reject this volunteer application?')">
                Reject
            </button>
        </form>
    @endif

    <form method="POST"
          action="{{ route('admin.volunteers.destroy', $volunteer->id) }}"
          class="d-inline">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Delete this volunteer application?')">
            Delete
        </button>
    </form>
</td>
 </tr>

                        @empty
                            <tr>
                                <td colspan="8"
                                    class="text-center py-5">

                                    <i class="bi bi-person-x fs-1 text-muted"></i>

                                    <h5 class="mt-3">
                                        No Volunteer Applications
                                    </h5>

                                    <p class="text-muted mb-0">
                                        No participant has applied as a volunteer yet.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
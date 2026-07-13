@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">

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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    Volunteer Application Details
                </h4>

                <a href="{{ route('admin.volunteers.edit', $volunteer->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>
            </div>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Application ID
                </div>
                <div class="col-md-8">
                    {{ $volunteer->id }}
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Applicant
                </div>
                <div class="col-md-8">
                    {{ $volunteer->user_name }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Email
                </div>
                <div class="col-md-8">
                    {{ $volunteer->user_email }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Event
                </div>
                <div class="col-md-8">
                    {{ $volunteer->event_title }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Event Start
                </div>
                <div class="col-md-8">
                    {{ $volunteer->start_time ?? 'N/A' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Event End
                </div>
                <div class="col-md-8">
                    {{ $volunteer->end_time ?? 'N/A' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Preferred Role
                </div>
                <div class="col-md-8">
                    {{ $volunteer->role }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Status
                </div>
                <div class="col-md-8">

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

                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Applied At
                </div>
                <div class="col-md-8">
                    {{ $volunteer->applied_at ?? 'N/A' }}
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 fw-bold">
                    Approved At
                </div>
                <div class="col-md-8">
                    {{ $volunteer->approved_at ?? 'Not approved yet' }}
                </div>
            </div>

            @if(strtolower($volunteer->status) === 'pending')
                <form method="POST"
                      action="{{ route('admin.volunteers.approve', $volunteer->id) }}"
                      class="d-inline">
                    @csrf

                    <button type="submit"
                            class="btn btn-success">
                        Approve
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('admin.volunteers.reject', $volunteer->id) }}"
                      class="d-inline">
                    @csrf

                    <button type="submit"
                            class="btn btn-danger">
                        Reject
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.volunteers.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </div>
    </div>
</div>
@endsection
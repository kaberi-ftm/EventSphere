@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                {{ $user->name }}
            </h2>

            <p class="text-muted mb-0">
                {{ $user->email }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route(
                'admin.users.edit',
                $user->id
            ) }}"
               class="btn btn-warning">

                <i class="bi bi-pencil-square me-1"></i>
                Edit
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Role
                    </small>

                    <h5 class="fw-bold mt-2 mb-0">
                        {{ $user->role_display_name ?? 'No Role' }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Engagement Points
                    </small>

                    <h3 class="fw-bold mt-2 mb-0 text-primary">
                        {{ number_format(
                            $user->engagement_points
                        ) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Engagement Rank
                    </small>

                    <h3 class="fw-bold mt-2 mb-0 text-success">
                        #{{ $user->engagement_rank }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Club Memberships
                    </small>

                    <h3 class="fw-bold mt-2 mb-0">
                        {{ count($memberships) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                Add Club Membership
            </h5>
        </div>

        <div class="card-body">

            @if(count($clubs) > 0)

                <form method="POST"
                      action="{{ route(
                          'admin.users.memberships.store',
                          $user->id
                      ) }}">

                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                Club
                            </label>

                            <select name="club_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Select Club
                                </option>

                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                Member Role
                            </label>

                            <select name="member_role"
                                    class="form-select"
                                    required>

                                @foreach([
                                    'member',
                                    'executive',
                                    'president',
                                    'secretary',
                                    'treasurer',
                                    'coordinator'
                                ] as $value)

                                    <option value="{{ $value }}">
                                        {{ ucfirst($value) }}
                                    </option>

                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit"
                                    class="btn btn-primary w-100">
                                Add
                            </button>
                        </div>

                    </div>
                </form>

            @else
                <p class="text-muted mb-0">
                    User already belongs to every available club.
                </p>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Club Memberships
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">

                <thead>
                    <tr>
                        <th>Club</th>
                        <th>Role</th>
                        <th>Joined At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($memberships as $membership)
                        <tr>
                            <td class="fw-semibold">
                                {{ $membership->club_name }}
                            </td>

                            <td>
                                <form method="POST"
                                      action="{{ route(
                                          'admin.memberships.update',
                                          $membership->id
                                      ) }}"
                                      class="d-flex gap-2">

                                    @csrf
                                    @method('PUT')

                                    <select name="member_role"
                                            class="form-select form-select-sm">

                                        @foreach([
                                            'member',
                                            'executive',
                                            'president',
                                            'secretary',
                                            'treasurer',
                                            'coordinator'
                                        ] as $value)

                                            <option value="{{ $value }}"
                                                @selected(
                                                    strtolower(
                                                        $membership->member_role
                                                    ) === $value
                                                )>

                                                {{ ucfirst($value) }}
                                            </option>

                                        @endforeach
                                    </select>

                                    <button class="btn btn-sm btn-primary">
                                        Update
                                    </button>
                                </form>
                            </td>

                            <td>
                                {{ $membership->joined_at ?? 'N/A' }}
                            </td>

                            <td class="text-end">

                                <form method="POST"
                                      action="{{ route(
                                          'admin.memberships.destroy',
                                          $membership->id
                                      ) }}">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm(
                                                'Remove this membership?'
                                            )">

                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4"
                                class="text-center py-4 text-muted">
                                No club memberships found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
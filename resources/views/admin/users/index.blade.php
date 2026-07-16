@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users & Roles')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">System Users</h2>

            <p class="text-muted mb-0">
                Manage users, roles, engagement points and memberships.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="btn btn-primary">

            <i class="bi bi-person-plus-fill me-1"></i>
            Add User
        </a>
    </div>

    <form method="GET"
          action="{{ route('admin.users.index') }}"
          class="card shadow-sm mb-4">

        <div class="card-body">
            <div class="row g-3">

                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Search</label>

                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Name, email or role">
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Role</label>

                    <select name="role_id"
                            class="form-select">

                        <option value="">All Roles</option>

                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                @selected(
                                    (string) $roleId ===
                                    (string) $role->id
                                )>

                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Sort By</label>

                    <select name="sort"
                            class="form-select">

                        <option value="name"
                            @selected($sort === 'name')>
                            Name
                        </option>

                        <option value="email"
                            @selected($sort === 'email')>
                            Email
                        </option>

                        <option value="role"
                            @selected($sort === 'role')>
                            Role
                        </option>

                        <option value="points"
                            @selected($sort === 'points')>
                            Engagement Points
                        </option>

                        <option value="clubs"
                            @selected($sort === 'clubs')>
                            Club Count
                        </option>

                        <option value="created"
                            @selected($sort === 'created')>
                            Created Date
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Order</label>

                    <select name="direction"
                            class="form-select">

                        <option value="asc"
                            @selected(
                                strtoupper($direction) === 'ASC'
                            )>
                            Ascending
                        </option>

                        <option value="desc"
                            @selected(
                                strtoupper($direction) === 'DESC'
                            )>
                            Descending
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex align-items-end gap-2">
                    <button class="btn btn-dark flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>
                        Apply
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Records</h5>

            <span class="badge bg-primary">
                {{ count($users) }} users
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Points</th>
                        <th>Rank</th>
                        <th>Clubs</th>
                        <th>Verified</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)

                        @php
                            $roleClass = match(
                                strtolower(
                                    (string) $user->role_name
                                )
                            ) {
                                'admin' => 'bg-danger',
                                'executive' => 'bg-primary',
                                'volunteer' => 'bg-success',
                                'participant' => 'bg-info text-dark',
                                default => 'bg-secondary'
                            };
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $user->name }}
                                </div>

                                <small class="text-muted">
                                    {{ $user->email }}
                                </small>
                            </td>

                            <td>
                                <span class="badge {{ $roleClass }}">
                                    {{ $user->role_display_name
                                        ?? 'No Role' }}
                                </span>
                            </td>

                            <td class="fw-bold">
                                {{ number_format(
                                    $user->engagement_points
                                ) }}
                            </td>

                            <td>
                                #{{ $user->engagement_rank }}
                            </td>

                            <td>
                                {{ $user->club_count }}
                            </td>

                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        Unverified
                                    </span>
                                @endif
                            </td>

                            <td class="text-nowrap">
                                {{ $user->created_at }}
                            </td>

                            <td class="text-end text-nowrap">

                                <a href="{{ route(
                                    'admin.users.show',
                                    $user->id
                                ) }}"
                                   class="btn btn-sm btn-info">

                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route(
                                    'admin.users.edit',
                                    $user->id
                                ) }}"
                                   class="btn btn-sm btn-warning">

                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                @if((string) auth()->id() !==
                                    (string) $user->id)

                                    <form method="POST"
                                          action="{{ route(
                                              'admin.users.destroy',
                                              $user->id
                                          ) }}"
                                          class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm(
                                                    'Delete this user?'
                                                )">

                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8"
                                class="text-center py-5">

                                <i class="bi bi-people fs-1 text-muted"></i>

                                <h5 class="mt-3">
                                    No users found
                                </h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
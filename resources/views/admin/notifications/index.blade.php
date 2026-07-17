@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notification Management')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Notifications
            </h2>

            <p class="text-muted mb-0">
                Send and manage system notifications.
            </p>
        </div>

        <div class="d-flex gap-2">
            <form method="POST"
                  action="{{ route(
                      'admin.notifications.read-all'
                  ) }}">
                @csrf

                <button type="submit"
                        class="btn btn-outline-success">
                    <i class="bi bi-check2-all me-1"></i>
                    Mark All Read
                </button>
            </form>

            <a href="{{ route(
                'admin.notifications.create'
            ) }}"
               class="btn btn-primary">
                <i class="bi bi-send-fill me-1"></i>
                Send Notification
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Total Notifications
                    </small>

                    <h3 class="fw-bold mt-2">
                        {{ $summary->total_notifications ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Unread
                    </small>

                    <h3 class="fw-bold text-warning mt-2">
                        {{ $summary->unread_notifications ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Read
                    </small>

                    <h3 class="fw-bold text-success mt-2">
                        {{ $summary->read_notifications ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <form method="GET"
          class="card shadow-sm mb-4">

        <div class="card-body">
            <div class="row g-3">

                <div class="col-lg-4">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Recipient, email or message">
                </div>

                <div class="col-lg-3">
                    <select name="user_id"
                            class="form-select">

                        <option value="">
                            All Recipients
                        </option>

                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @selected(
                                    (string) $userId ===
                                    (string) $user->id
                                )>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="status"
                            class="form-select">
                        <option value="">All Statuses</option>

                        <option value="unread"
                            @selected($status === 'unread')>
                            Unread
                        </option>

                        <option value="read"
                            @selected($status === 'read')>
                            Read
                        </option>
                    </select>
                </div>

                <div class="col-lg-3 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1">
                        Apply
                    </button>

                    <a href="{{ route(
                        'admin.notifications.index'
                    ) }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">

            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Notification</th>
                        <th>Recipient</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($notifications as $notification)

                        @php
                            $levelClass = match(
                                strtolower($notification->level)
                            ) {
                                'success' => 'bg-success',
                                'warning' => 'bg-warning text-dark',
                                'danger' => 'bg-danger',
                                default => 'bg-info text-dark'
                            };
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $notification->title }}
                                </div>

                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit(
                                        $notification->message,
                                        70
                                    ) }}
                                </small>
                            </td>

                            <td>
                                {{ $notification->recipient_name
                                    ?? 'Unknown User' }}

                                <br>

                                <small class="text-muted">
                                    {{ $notification->recipient_email }}
                                </small>
                            </td>

                            <td>
                                <span class="badge {{ $levelClass }}">
                                    {{ ucfirst($notification->level) }}
                                </span>
                            </td>

                            <td>
                                @if($notification->read_at)
                                    <span class="badge bg-success">
                                        Read
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        Unread
                                    </span>
                                @endif
                            </td>

                            <td class="text-nowrap">
                                {{ $notification->created_at }}
                            </td>

                            <td class="text-end text-nowrap">

                                <a href="{{ route(
                                    'admin.notifications.show',
                                    $notification->id
                                ) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if(!$notification->read_at)
                                    <form method="POST"
                                          action="{{ route(
                                              'admin.notifications.read',
                                              $notification->id
                                          ) }}"
                                          class="d-inline">
                                        @csrf

                                        <button class="btn btn-sm btn-success">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                @endif

                                <form method="POST"
                                      action="{{ route(
                                          'admin.notifications.destroy',
                                          $notification->id
                                      ) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm(
                                                'Delete this notification?'
                                            )">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6"
                                class="text-center py-5">
                                No notifications found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
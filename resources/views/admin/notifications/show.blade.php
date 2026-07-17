@extends('layouts.admin')

@section('title', 'Notification Details')
@section('page-title', 'Notification Details')

@section('content')
<div class="container-fluid px-0">

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between">
            <h4 class="mb-0">
                {{ $notification->title }}
            </h4>

            <span class="badge {{
                $notification->read_at
                    ? 'bg-success'
                    : 'bg-warning text-dark'
            }}">
                {{ $notification->read_at
                    ? 'Read'
                    : 'Unread' }}
            </span>
        </div>

        <div class="card-body">

            <div class="mb-4">
                <strong>Recipient</strong>

                <div class="text-muted">
                    {{ $notification->recipient_name }}
                    — {{ $notification->recipient_email }}
                </div>
            </div>

            <div class="mb-4">
                <strong>Message</strong>

                <div class="border rounded p-3 mt-2 bg-light">
                    {{ $notification->message }}
                </div>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <strong>Level</strong>
                    <div class="text-muted">
                        {{ ucfirst($notification->level) }}
                    </div>
                </div>

                <div class="col-md-4">
                    <strong>Sent At</strong>
                    <div class="text-muted">
                        {{ $notification->created_at }}
                    </div>
                </div>

                <div class="col-md-4">
                    <strong>Read At</strong>
                    <div class="text-muted">
                        {{ $notification->read_at ?? 'Not read' }}
                    </div>
                </div>

                <div class="col-12">
                    <strong>Action URL</strong>

                    <div class="text-muted">
                        {{ $notification->action_url
                            ?? 'No action URL' }}
                    </div>
                </div>

            </div>

            <hr>

            <div class="d-flex gap-2">
                @if(!$notification->read_at)
                    <form method="POST"
                          action="{{ route(
                              'admin.notifications.read',
                              $notification->id
                          ) }}">
                        @csrf

                        <button class="btn btn-success">
                            Mark as Read
                        </button>
                    </form>
                @endif

                <a href="{{ route(
                    'admin.notifications.index'
                ) }}"
                   class="btn btn-outline-secondary">
                    Back
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
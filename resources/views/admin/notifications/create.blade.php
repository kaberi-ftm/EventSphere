@extends('layouts.admin')

@section('title', 'Send Notification')
@section('page-title', 'Send Notification')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h4 class="mb-1">
                        <i class="bi bi-send-fill me-2"></i>
                        New Notification
                    </h4>

                    <p class="text-muted mb-0">
                        Send a message to one user or all users.
                    </p>
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ route(
                              'admin.notifications.store'
                          ) }}">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">
                                    Recipient Mode
                                </label>

                                <select name="recipient_mode"
                                        id="recipient_mode"
                                        class="form-select"
                                        required>

                                    <option value="single"
                                        @selected(
                                            old(
                                                'recipient_mode',
                                                'single'
                                            ) === 'single'
                                        )>
                                        Single User
                                    </option>

                                    <option value="all"
                                        @selected(
                                            old('recipient_mode')
                                                === 'all'
                                        )>
                                        All Users
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6"
                                 id="userField">
                                <label class="form-label">
                                    User
                                </label>

                                <select name="user_id"
                                        class="form-select">

                                    <option value="">
                                        Select User
                                    </option>

                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                            @selected(
                                                old('user_id')
                                                == $user->id
                                            )>
                                            {{ $user->name }}
                                            — {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">
                                    Title
                                </label>

                                <input type="text"
                                       name="title"
                                       class="form-control"
                                       maxlength="200"
                                       value="{{ old('title') }}"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Level
                                </label>

                                <select name="level"
                                        class="form-select"
                                        required>

                                    @foreach([
                                        'info',
                                        'success',
                                        'warning',
                                        'danger'
                                    ] as $value)
                                        <option value="{{ $value }}"
                                            @selected(
                                                old(
                                                    'level',
                                                    'info'
                                                ) === $value
                                            )>
                                            {{ ucfirst($value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Message
                                </label>

                                <textarea name="message"
                                          rows="5"
                                          maxlength="1000"
                                          class="form-control"
                                          required>{{ old('message') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Action URL
                                </label>

                                <input type="text"
                                       name="action_url"
                                       maxlength="500"
                                       class="form-control"
                                       value="{{ old(
                                           'action_url'
                                       ) }}"
                                       placeholder="/participant/dashboard">
                            </div>

                            <div class="col-12 text-end">
                                <a href="{{ route(
                                    'admin.notifications.index'
                                ) }}"
                                   class="btn btn-outline-secondary">
                                    Cancel
                                </a>

                                <button class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>
                                    Send Notification
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mode = document.getElementById('recipient_mode');
        const userField = document.getElementById('userField');

        function updateRecipientField() {
            userField.style.display =
                mode.value === 'single'
                    ? 'block'
                    : 'none';
        }

        mode.addEventListener(
            'change',
            updateRecipientField
        );

        updateRecipientField();
    });
</script>
@endpush
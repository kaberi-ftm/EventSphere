@extends('layouts.admin')

@section('title', 'Issue Certificate')
@section('page-title', 'Issue Certificate')

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Issue New Certificate</h4>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.certificates.store') }}">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Recipient</label>

                        <select name="user_id"
                                class="form-select"
                                required>
                            <option value="">Select User</option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    @selected(
                                        old('user_id') == $user->id
                                    )>
                                    {{ $user->name }} —
                                    {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Event</label>

                        <select name="event_id"
                                class="form-select"
                                required>
                            <option value="">Select Event</option>

                            @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    @selected(
                                        old('event_id') == $event->id
                                    )>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Certificate Type
                        </label>

                        <select name="certificate_type"
                                class="form-select"
                                required>
                            @foreach([
                                'participation',
                                'volunteer',
                                'achievement',
                                'organizer',
                                'winner'
                            ] as $value)
                                <option value="{{ $value }}"
                                    @selected(
                                        old(
                                            'certificate_type',
                                            'participation'
                                        ) === $value
                                    )>
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Title</label>

                        <input name="title"
                               class="form-control"
                               maxlength="200"
                               value="{{ old('title') }}"
                               required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  class="form-control"
                                  maxlength="500"
                                  rows="4">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12 text-end">
                        <a href="{{ route(
                            'admin.certificates.index'
                        ) }}"
                           class="btn btn-outline-secondary">
                            Cancel
                        </a>

                        <button class="btn btn-primary">
                            Issue Certificate
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
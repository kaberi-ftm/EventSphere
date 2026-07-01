@extends('layouts.admin')

@section('title', 'Create Event')

@section('content')

<h2 class="mb-4">Create Event</h2>

<div class="card">
    <div class="card-body">

        <form
            action="{{ route('admin.events.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="mb-3">

                <label>Club</label>

                <select
                    name="club_id"
                    class="form-control"
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

            <div class="mb-3">

                <label>Event Poster / Banner</label>

                <input
                    type="file"
                    name="poster"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp">

            </div>

            <div class="mb-3">

                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="5"
                    class="form-control"></textarea>

            </div>

            <div class="mb-3">

                <label>Start Time</label>

                <input
                    type="datetime-local"
                    name="start_time"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label>Maximum Participants</label>

                <input
                    type="number"
                    name="max_participants"
                    class="form-control"
                    value="100">

            </div>

            <button class="btn btn-success">
                Save Event
            </button>

        </form>

    </div>
</div>

@endsection
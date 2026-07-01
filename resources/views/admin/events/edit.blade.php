@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')

<h2 class="mb-4">Edit Event</h2>

<div class="card">
    <div class="card-body">

        <form
            action="{{ route('admin.events.update',$event->id) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label>Current Poster</label>

                <br>

                @if($event->poster)

                    <img
                        src="{{ asset($event->poster) }}"
                        width="250"
                        class="rounded shadow mb-3">

                @else

                    <p>No poster uploaded.</p>

                @endif

            </div>

            <div class="mb-3">

                <label>Upload New Poster</label>

                <input
                    type="file"
                    name="poster"
                    class="form-control">

            </div>

            <div class="mb-3">

                <label>Club</label>

                <select
                    name="club_id"
                    class="form-control">

                    @foreach($clubs as $club)

                        <option
                            value="{{ $club->id }}"
                            {{ $club->id == $event->club_id ? 'selected' : '' }}>

                            {{ $club->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-3">

                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    value="{{ $event->title }}">

            </div>

            <div class="mb-3">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="5"
                    class="form-control">{{ $event->description }}</textarea>

            </div>

            <div class="mb-3">

                <label>Maximum Participants</label>

                <input
                    type="number"
                    name="max_participants"
                    class="form-control"
                    value="{{ $event->max_participants }}">

            </div>

            <button class="btn btn-primary">
                Update Event
            </button>

        </form>

    </div>
</div>

@endsection